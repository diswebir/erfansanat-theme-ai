#!/usr/bin/env bash
# Comprehensive phase test runner — phase-02-custom-admin-dashboard
# Usage: bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh
set -u
PHP=/home/user/tools/node_modules/.bin/php-wasm-cli
DIR="$(cd "$(dirname "$0")" && pwd)"
FAIL=0
for t in test-task-2-1.php test-task-2-2.php test-task-2-3.php; do
  echo "== $t =="
  "$PHP" "$DIR/$t" || FAIL=1
done
exit $FAIL
