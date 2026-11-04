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
for file in ContentTypes.php Admin/ProductMetaboxes.php Admin/ProjectMetaboxes.php Admin/TermMetaboxes.php Admin/ContentMeta.php; do
  "$PHP_BIN" -l "$ROOT/inc/$file"
done
for task in 01 02 03; do
  WP_TEST_ROOT="$WP_TEST_ROOT" "$PHP_BIN" "$ROOT/temp/phase-03-cpt-and-legacy-meta/tests/test-task-$task.php"
done
printf "Phase 03 tests passed.\n"
