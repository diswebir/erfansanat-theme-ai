#!/bin/sh
set -eu
ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/../../.." && pwd)"
PHP_BIN="${PHP_BIN:-php}"
TMP_OUTPUT="$(mktemp)"
trap 'rm -f "$TMP_OUTPUT"' EXIT HUP INT TERM

for task in 01 02 03; do
  if ! "$PHP_BIN" "$ROOT/temp/phase-07-projects-blog-and-pages/tests/test-task-${task}.php" >"$TMP_OUTPUT" 2>&1; then
    cat "$TMP_OUTPUT"
    exit 1
  fi
  cat "$TMP_OUTPUT"
  if grep -q '^FAIL' "$TMP_OUTPUT"; then
    echo "Phase 07 task ${task} reported a failure." >&2
    exit 1
  fi
done

WP_TEST_DIR="${WP_TEST_DIR:-$ROOT/node_modules/.cache/ese-wp-test}"
if [ -s "$WP_TEST_DIR/wp-debug.log" ]; then
  cat "$WP_TEST_DIR/wp-debug.log" >&2
  echo "WP_DEBUG log is not empty." >&2
  exit 1
fi
printf "Phase 07 tests passed; WP_DEBUG log is empty.\n"
