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
TASKS="${PHASE_TASKS:-01 02}"
for task in $TASKS; do
  case "$task" in
    01|02) ;;
    *) echo "Unknown Phase 05 task: $task" >&2; exit 2 ;;
  esac
  "$PHP_BIN" -l "$ROOT/temp/phase-05-homepage-and-sections/tests/test-task-$task.php"
  WP_TEST_ROOT="$WP_TEST_ROOT" "$PHP_BIN" "$ROOT/temp/phase-05-homepage-and-sections/tests/test-task-$task.php"
done
printf "Selected Phase 05 tests passed.\n"
