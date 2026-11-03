#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
export ERFANSANAT_TOOL_CACHE="${ERFANSANAT_TOOL_CACHE:-$HOME/.cache/erfansanat-tools}"
export PATH="$ERFANSANAT_TOOL_CACHE/node_modules/node/bin:$PATH"

printf '%s\n' '[1/7] Required discovery documents and no premature theme files'
for file in temp/PROJECT_GOAL.md temp/PROJECT_MEMORY.md temp/DATA_BLUEPRINT.md temp/STATIC_TEMPLATE_MAP.md; do
  test -s "$file"
done
test ! -e functions.php
test ! -e style.css
test ! -e index.php

printf '%s\n' '[2/7] Source hashes, complete mapping, DOM, links, fonts and JSON-LD'
python3 tools/tests/test_discovery.py

printf '%s\n' '[3/7] Static JavaScript syntax and SVG XML'
node --check static-mirror/assets/js/main.js
tools/xmllint --noout static-mirror/images/ese-logo.svg

printf '%s\n' '[4/7] PHP 8.4, WP-CLI and coding standards'
tools/php -r 'if (PHP_MAJOR_VERSION !== 8 || PHP_MINOR_VERSION !== 4) { exit(1); } echo "PHP " . PHP_VERSION . PHP_EOL;'
tools/wp --info --allow-root | grep -E 'PHP version:|WP-CLI version:'
tools/phpcs --version | grep -q '3.13.5'
tools/phpcs --standard=WordPress -e | grep -q 'WordPress.WP.I18n'

printf '%s\n' '[5/7] PHPUnit runtime smoke tests'
tools/phpunit --do-not-cache-result --no-progress --colors=never tools/tests/ToolingSmokeTest.php

printf '%s\n' '[6/7] Scanner reproducibility and read-only source behavior'
python3 - <<'PY'
from pathlib import Path
import hashlib, subprocess
paths=[Path('temp/static-inventory.json'),Path('temp/STATIC_TEMPLATE_MAP.md')]
before={str(p):hashlib.sha256(p.read_bytes()).hexdigest() for p in paths}
source_before={str(p):hashlib.sha256(p.read_bytes()).hexdigest() for p in Path('static-mirror').rglob('*') if p.is_file()}
subprocess.run(['python3','tools/scan_static.py'],check=True,stdout=subprocess.DEVNULL)
after={str(p):hashlib.sha256(p.read_bytes()).hexdigest() for p in paths}
source_after={str(p):hashlib.sha256(p.read_bytes()).hexdigest() for p in Path('static-mirror').rglob('*') if p.is_file()}
assert before==after, 'Scanner output is not deterministic'
assert source_before==source_after, 'Scanner modified the static source'
print('Deterministic inventory/map; all 156 source files unchanged.')
PY

printf '%s\n' '[7/7] Tooling script syntax and Git whitespace'
bash -n tools/setup.sh tools/env.sh tools/php tools/wp tools/phpcs tools/phpunit tools/test-stage0.sh tools/run-php-tests.sh tools/preview-wordpress.sh tools/test-local.sh
python3 -m py_compile tools/scan_static.py tools/prepare-phpunit.py tools/tests/test_discovery.py tools/build-asset-manifest.py temp/phase-01-core-and-assets/tests/check-http.py
# The uploaded reference is immutable, including its original trailing whitespace.
git diff --check -- . ':!static-mirror'
git diff --cached --check -- . ':!static-mirror'
printf '%s\n' 'STAGE 0 TESTS PASSED — WordPress integration and production data are not yet tested.'
