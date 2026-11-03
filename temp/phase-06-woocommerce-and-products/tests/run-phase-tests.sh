#!/usr/bin/env bash
# Comprehensive phase test runner — phase-06-woocommerce-and-products
# Usage: bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh
set -u
PHP=/home/user/tools/node_modules/.bin/php-wasm-cli
DIR="$(cd "$(dirname "$0")" && pwd)"
FAIL=0
for t in test-task-6-1.php test-task-6-2.php test-task-6-3.php; do
  echo "== $t =="
  "$PHP" "$DIR/$t" || FAIL=1
done
exit $FAIL
