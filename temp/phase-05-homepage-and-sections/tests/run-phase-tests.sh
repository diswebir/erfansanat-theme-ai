#!/usr/bin/env bash
# Comprehensive phase test runner — phase-05-homepage-and-sections
# Usage: bash temp/phase-05-homepage-and-sections/tests/run-phase-tests.sh
set -u
PHP=/home/user/tools/node_modules/.bin/php-wasm-cli
DIR="$(cd "$(dirname "$0")" && pwd)"
FAIL=0
for t in test-task-5-1.php test-task-5-2.php test-dom-fidelity.php; do
  echo "== $t =="
  "$PHP" "$DIR/$t" || FAIL=1
done
exit $FAIL
