#!/bin/sh
set -eu
ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/../../.." && pwd)"
PHP_BIN="${PHP_BIN:-php}"
"$PHP_BIN" "$ROOT/temp/phase-06-woocommerce-and-products/tests/test-task-01.php"
"$PHP_BIN" "$ROOT/temp/phase-06-woocommerce-and-products/tests/test-task-02.php"
"$PHP_BIN" "$ROOT/temp/phase-06-woocommerce-and-products/tests/test-task-03.php"
printf "Phase 06 tests passed.\n"
