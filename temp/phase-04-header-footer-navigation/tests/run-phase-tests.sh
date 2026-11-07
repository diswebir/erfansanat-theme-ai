#!/usr/bin/env bash
# Comprehensive phase test runner — phase-04-header-footer-navigation
# Usage: bash temp/phase-04-header-footer-navigation/tests/run-phase-tests.sh
set -u
PHP=/home/user/tools/node_modules/.bin/php-wasm-cli
DIR="$(cd "$(dirname "$0")" && pwd)"
FAIL=0
for t in test-task-4-1.php test-task-4-2.php; do
  echo "== $t =="
  "$PHP" "$DIR/$t" || FAIL=1
done
exit $FAIL
