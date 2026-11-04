#!/usr/bin/env bash
# Comprehensive phase test runner — phase-08-ajax-filters-and-audit
# Usage: bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh
set -u
PHP=/home/user/tools/node_modules/.bin/php-wasm-cli
DIR="$(cd "$(dirname "$0")" && pwd)"
FAIL=0
for t in test-task-8-1.php test-task-8-2.php test-task-8-3.php test-wp-api-surface.php test-smoke-render.php test-wp-integration.php test-debug-full.php test-security-assets.php test-admin-a11y-perf.php; do
  echo "== $t =="
  "$PHP" "$DIR/$t" || FAIL=1
done
exit $FAIL
