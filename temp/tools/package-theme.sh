#!/usr/bin/env bash
# P08-T024 — release package: zip the theme folder ONLY (AC-02).
#
# Output: temp/dist/erfansanat-<version>.zip with erfansanat/ as the zip root
# (WordPress expects <zip>/erfansanat/style.css). No temp/, static-mirror/ or
# OS junk inside.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
VERSION="$(grep -m1 'Version:' "$ROOT/erfansanat/style.css" | awk '{print $2}')"
DIST="$ROOT/temp/dist"
ZIP="$DIST/erfansanat.zip" # P01-T012 contract: fixed name; version is reported, not in the filename
mkdir -p "$DIST"
rm -f "$ZIP"

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
echo "zip: $ZIP"
echo "version: $VERSION files: $FILES size: $SIZE"
