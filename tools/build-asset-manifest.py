#!/usr/bin/env python3
"""Write erfansanat/assets/manifest.json from the transferred files.

The manifest records the SHA-256 and size of every asset so the acceptance
contract can prove the files were copied byte-for-byte from the immutable
static reference and that later edits are deliberate.

Usage:
    python3 tools/build-asset-manifest.py [--check]
"""

from __future__ import annotations

import argparse
import hashlib
import json
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
ASSETS = ROOT / "erfansanat/assets"
REFERENCE = ROOT / "static-mirror"
MANIFEST = ASSETS / "manifest.json"

# Reference sources for the files copied verbatim into the theme.
SOURCES = {
    "css/styles.css": "assets/css/styles.css",
    "js/main.js": "assets/js/main.js",
    "fonts/vazirmatn-arabic.woff2": "assets/fonts/vazirmatn-arabic.woff2",
    "fonts/vazirmatn-latin.woff2": "assets/fonts/vazirmatn-latin.woff2",
    "fonts/vazirmatn-latin-ext.woff2": "assets/fonts/vazirmatn-latin-ext.woff2",
    "images/ese-logo.svg": "images/ese-logo.svg",
    "video/hero-video.mp4": "images/hero-video.mp4",
}


# Files the theme intentionally edits after copying. A deliberate edit must be
# listed here with its reason, so a byte-for-byte contract never silently rots
# and a later revert is caught just as loudly.
PATCHED = {
    "js/main.js": (
        "Phase 6.2: the approved archive markup marks its pager with "
        "data-pagination-root / data-page-num and records server-side paging, "
        "while the copied script only looked for data-page-btn and the string "
        "\"true\". Without the fix the client pager never bound, so paging was "
        "dead on every shop archive. Phase 7.2: in server-paged mode the same "
        "function left every item hidden after a filter change, because the "
        "slice loop below it never ran; matching items are now un-hidden before "
        "the early return. Phase 8.1 added the live-search panel and the "
        "Phase 8.2 change stopped the contact handler from showing success for "
        "a submission the server had not accepted."
    ),
}


def digest(path: Path) -> str:
    """SHA-256 of a file."""
    return hashlib.sha256(path.read_bytes()).hexdigest()


def build() -> dict:
    """Describe every transferred asset and compare it with its source."""
    files = {}
    for path in sorted(ASSETS.rglob("*")):
        if not path.is_file() or path == MANIFEST:
            continue
        relative = path.relative_to(ASSETS).as_posix()
        entry = {
            "sha256": digest(path),
            "bytes": path.stat().st_size,
        }
        if relative in SOURCES:
            source = REFERENCE / SOURCES[relative]
            entry["source"] = SOURCES[relative]
            entry["source_sha256"] = digest(source) if source.exists() else None
            entry["identical_to_source"] = entry["source_sha256"] == entry["sha256"]
            if relative in PATCHED:
                entry["patched"] = True
                entry["patch_note"] = PATCHED[relative]
        else:
            # Images keep their directory layout under images/.
            source = REFERENCE / "images" / relative.removeprefix("images/")
            if source.exists():
                entry["source"] = source.relative_to(REFERENCE).as_posix()
                entry["source_sha256"] = digest(source)
                entry["identical_to_source"] = entry["source_sha256"] == entry["sha256"]
            else:
                # Theme-authored assets have no reference counterpart at all, so
                # they are not drift: only files copied from the mirror can drift.
                entry["source"] = None
                entry["source_sha256"] = None
                entry["identical_to_source"] = False
                entry["authored"] = True
        files[relative] = entry
    return {
        "generated_by": "tools/build-asset-manifest.py",
        "reference": "static-mirror/",
        "files": files,
    }


def main() -> int:
    """Write or verify the manifest."""
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--check", action="store_true", help="Fail if the manifest is stale.")
    args = parser.parse_args()

    data = build()
    serialized = json.dumps(data, indent=2, ensure_ascii=False) + "\n"

    if args.check:
        if not MANIFEST.exists():
            print("manifest.json is missing; run python3 tools/build-asset-manifest.py", file=sys.stderr)
            return 1
        if MANIFEST.read_text(encoding="utf-8") != serialized:
            print("manifest.json is stale; run python3 tools/build-asset-manifest.py", file=sys.stderr)
            return 1
        unpatched = [
            name
            for name, meta in data["files"].items()
            if not meta["identical_to_source"] and not meta.get("patched") and not meta.get("authored")
        ]
        if unpatched:
            print("Assets differ from the reference without a documented patch: " + ", ".join(unpatched), file=sys.stderr)
            return 1
        patched = [name for name, meta in data["files"].items() if meta.get("patched")]
        print(
            f"manifest.json is current: {len(data['files'])} assets, "
            f"{len(patched)} with a documented patch"
            + (f" ({', '.join(patched)})" if patched else "")
            + "."
        )
        return 0

    MANIFEST.write_text(serialized, encoding="utf-8")
    drift = [name for name, meta in data["files"].items() if not meta["identical_to_source"]]
    print(f"Wrote {MANIFEST.relative_to(ROOT)} with {len(data['files'])} assets.")
    unpatched = [
        name
        for name, meta in data["files"].items()
        if not meta["identical_to_source"] and not meta.get("patched") and not meta.get("authored")
    ]
    if unpatched:
        print("Warning, differs from reference without a documented patch: " + ", ".join(unpatched), file=sys.stderr)
        return 1
    print("All assets match the static reference, except documented patches.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
