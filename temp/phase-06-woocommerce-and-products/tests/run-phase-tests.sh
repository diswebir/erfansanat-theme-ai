#!/bin/sh
set -eu
ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/../../.." && pwd)"
if [ -n "${PHP_BIN:-}" ]; then
  :
elif command -v php >/dev/null 2>&1; then
  PHP_BIN="php"
elif [ -x "$ROOT/node_modules/.bin/php-wasm-cli" ]; then
  PHP_BIN="$ROOT/node_modules/.bin/php-wasm-cli"
else
  echo "PHP or PHP WASM is required. Run temp/setup-wordpress-test.sh first." >&2
  exit 1
fi
WP_TEST_ROOT="${WP_TEST_ROOT:-$ROOT/node_modules/.cache/ese-wp-test/WordPress-master}"
TASKS="${PHASE_TASKS:-01 02 03}"
for task in $TASKS; do
  case "$task" in
    01|02|03) ;;
    *) echo "Unknown Phase 06 task: $task" >&2; exit 2 ;;
  esac
  testFile="$ROOT/temp/phase-06-woocommerce-and-products/tests/test-task-$task.php"
  "$PHP_BIN" -l "$testFile"
  WP_TEST_ROOT="$WP_TEST_ROOT" "$PHP_BIN" "$testFile"
done
printf "Selected Phase 06 tests passed.\n"
