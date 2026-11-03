#!/usr/bin/env bash
# Package only the erfansanat theme folder into temp/dist/erfansanat.zip.
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"
mkdir -p temp/dist
rm -f temp/dist/erfansanat.zip
zip -rq temp/dist/erfansanat.zip erfansanat \
  -x 'erfansanat/.git/*' \
  -x 'erfansanat/node_modules/*' \
  -x 'erfansanat/.DS_Store'
echo "Packaged: $ROOT/temp/dist/erfansanat.zip"
unzip -l temp/dist/erfansanat.zip | tail -3
