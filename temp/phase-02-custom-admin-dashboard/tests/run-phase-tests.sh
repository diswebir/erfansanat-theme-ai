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
TMP_OUTPUT="$(mktemp)"
trap 'rm -f "$TMP_OUTPUT"' EXIT HUP INT TERM

run_checked() {
  if ! "$@" >"$TMP_OUTPUT" 2>&1; then
    cat "$TMP_OUTPUT"
    return 1
  fi
  cat "$TMP_OUTPUT"
  if grep -Eiq '^(FAIL|.*critical error|.*fatal error|.*parse error)' "$TMP_OUTPUT"; then
    echo "Phase 02 command reported a failure." >&2
    return 1
  fi
}

run_checked "$PHP_BIN" -l "$ROOT/inc/Admin/ThemeOptions.php"
run_checked "$PHP_BIN" -l "$ROOT/inc/Admin/SectionManager.php"
run_checked "$PHP_BIN" -l "$ROOT/inc/Helpers.php"
run_checked env WP_TEST_ROOT="$WP_TEST_ROOT" "$PHP_BIN" "$ROOT/temp/phase-02-custom-admin-dashboard/tests/test-task-01.php"
run_checked env WP_TEST_ROOT="$WP_TEST_ROOT" "$PHP_BIN" "$ROOT/temp/phase-02-custom-admin-dashboard/tests/test-task-02.php"
run_checked env WP_TEST_ROOT="$WP_TEST_ROOT" "$PHP_BIN" "$ROOT/temp/phase-02-custom-admin-dashboard/tests/test-task-03.php"
WP_TEST_DIR="${WP_TEST_DIR:-$ROOT/node_modules/.cache/ese-wp-test}"
if [ -s "$WP_TEST_DIR/wp-debug.log" ]; then
  cat "$WP_TEST_DIR/wp-debug.log" >&2
  echo "WP_DEBUG log is not empty." >&2
  exit 1
fi
printf "Phase 02 tests passed; WP_DEBUG log is empty.\n"
