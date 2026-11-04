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
"$PHP_BIN" -l "$ROOT/inc/Admin/ThemeOptions.php"
"$PHP_BIN" -l "$ROOT/inc/Admin/SectionManager.php"
"$PHP_BIN" -l "$ROOT/inc/Helpers.php"
WP_TEST_ROOT="$WP_TEST_ROOT" "$PHP_BIN" "$ROOT/temp/phase-02-custom-admin-dashboard/tests/test-task-01.php"
WP_TEST_ROOT="$WP_TEST_ROOT" "$PHP_BIN" "$ROOT/temp/phase-02-custom-admin-dashboard/tests/test-task-02.php"
WP_TEST_ROOT="$WP_TEST_ROOT" "$PHP_BIN" "$ROOT/temp/phase-02-custom-admin-dashboard/tests/test-task-03.php"
printf "Phase 02 tests passed.\n"
