#!/usr/bin/env bash
# P08-T024 — release package: zip the theme folder ONLY (AC-02).
#
# Output: temp/dist/erfansanat.zip with erfansanat/ as the zip root
# (WordPress expects <zip>/erfansanat/style.css). No temp/, static-mirror/ or
# OS junk inside. P08-T025: refuses to package a drifted working tree and
# verifies zip ↔ git parity before declaring success.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
VERSION="$(grep -m1 'Version:' "$ROOT/erfansanat/style.css" | awk '{print $2}')"
DIST="$ROOT/temp/dist"
ZIP="$DIST/erfansanat.zip" # P01-T012 contract: fixed name; version is reported, not in the filename
mkdir -p "$DIST"
rm -f "$ZIP"

# P08-T025 drift guard: the package must come from the committed state, so a
# release is always reproducible from git. Any modification, addition or
# deletion under erfansanat/ (including untracked files) aborts with exit 3.
DRIFT="$(git -C "$ROOT" status --porcelain -- erfansanat/ 2>/dev/null || true)"
if [ -n "$DRIFT" ]; then
	echo "package: erfansanat/ working tree differs from HEAD — commit first:" >&2
	echo "$DRIFT" | head -20 >&2
	exit 3
fi

(cd "$ROOT" && zip -qr "$ZIP" erfansanat -x '*.DS_Store' -x '*__MACOSX*' -x '*.git*')

# Sanity: theme entrypoint present, no stray top-level folders.
# (Capture the listing first: grep -q closes the pipe early and SIGPIPEs unzip
# under `set -o pipefail`.)
LISTING="$(unzip -l "$ZIP")"
echo "$LISTING" | grep -q 'erfansanat/style.css' || { echo "package: style.css missing" >&2; exit 2; }
if echo "$LISTING" | grep -qE ' (temp/|static-mirror/|\.git/)'; then
	echo "package: unexpected entries in zip" >&2
	exit 2
fi

FILES="$(echo "$LISTING" | tail -1 | awk '{print $2}')"
SIZE="$(du -h "$ZIP" | cut -f1)"

# P08-T025 zip ↔ git parity: every tracked file exactly once, nothing extra.
ZIP_FILES="$(echo "$LISTING" | awk '$NF ~ /^erfansanat\// && $NF !~ /\/$/ {print $NF}' | sort)"
GIT_FILES="$(git -C "$ROOT" ls-files erfansanat/ | sort)"
if [ "$ZIP_FILES" != "$GIT_FILES" ]; then
	echo "package: zip/git parity mismatch" >&2
	diff <(echo "$GIT_FILES") <(echo "$ZIP_FILES") | head -20 >&2 || true
	exit 3
fi

echo "zip: $ZIP"
echo "version: $VERSION files: $FILES size: $SIZE"
echo "parity: zip ↔ git OK ($(echo "$GIT_FILES" | wc -l) files)"
