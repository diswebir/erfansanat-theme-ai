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
        else:
            # Images keep their directory layout under images/.
            source = REFERENCE / "images" / relative.removeprefix("images/")
            if source.exists():
                entry["source"] = source.relative_to(REFERENCE).as_posix()
                entry["source_sha256"] = digest(source)
                entry["identical_to_source"] = entry["source_sha256"] == entry["sha256"]
            else:
                entry["source"] = None
                entry["source_sha256"] = None
                entry["identical_to_source"] = False
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
        drift = [name for name, meta in data["files"].items() if not meta["identical_to_source"]]
        if drift:
            print("Assets differ from the reference: " + ", ".join(drift), file=sys.stderr)
            return 1
        print(f"manifest.json is current: {len(data['files'])} assets, all identical to the reference.")
        return 0

    MANIFEST.write_text(serialized, encoding="utf-8")
    drift = [name for name, meta in data["files"].items() if not meta["identical_to_source"]]
    print(f"Wrote {MANIFEST.relative_to(ROOT)} with {len(data['files'])} assets.")
    if drift:
        print("Warning, differs from reference: " + ", ".join(drift), file=sys.stderr)
        return 1
    print("All assets are byte-identical to the static reference.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
