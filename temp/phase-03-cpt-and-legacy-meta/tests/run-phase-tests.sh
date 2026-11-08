#!/usr/bin/env bash
# Comprehensive phase test runner — phase-03-cpt-and-legacy-meta
# Usage: bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh
set -u
PHP=/home/user/tools/node_modules/.bin/php-wasm-cli
DIR="$(cd "$(dirname "$0")" && pwd)"
FAIL=0
for t in test-task-3-1.php test-task-3-2.php test-task-3-3.php; do
  echo "== $t =="
  "$PHP" "$DIR/$t" || FAIL=1
done
exit $FAIL
