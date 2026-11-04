#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
WP="$ROOT/.local/wordpress"

echo '[1/4] PHP syntax'
tools/php -l tools/local/router.php
tools/php -l tools/local/fixture-types.php
tools/php -l tools/local/seed.php
tools/php -l tools/local/test-runtime.php

echo '[2/4] WooCommerce storefront translations'
python3 tools/patch-woocommerce-translations.py --check

echo '[2/4] WordPress runtime acceptance'
tools/wp --path="$WP" eval-file tools/local/test-runtime.php

echo '[3/4] HTTP smoke (requires preview server on 8080)'
python3 tools/local/test-http.py

echo '[4/4] Fixture idempotency and source integrity'
tools/wp --path="$WP" eval-file tools/local/seed.php
tools/wp --path="$WP" eval-file tools/local/test-runtime.php
python3 tools/scan_static.py > /home/user/.cache/erfansanat-scan-after-local.json
git diff --exit-code -- static-mirror
echo 'LOCAL TESTS PASSED — isolated synthetic environment only; not production compatibility.'
