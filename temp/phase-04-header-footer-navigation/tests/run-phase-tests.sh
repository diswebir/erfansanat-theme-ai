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
for file in \
  "$ROOT/header.php" \
  "$ROOT/footer.php" \
  "$ROOT/inc/TemplateParts.php" \
  "$ROOT/inc/Navigation/Walker.php" \
  "$ROOT/inc/Navigation/Navigation.php"; do
  "$PHP_BIN" -l "$file"
done
for task in 01 02; do
  WP_TEST_ROOT="$WP_TEST_ROOT" "$PHP_BIN" "$ROOT/temp/phase-04-header-footer-navigation/tests/test-task-$task.php"
done
printf "Phase 04 tests passed.\n"
