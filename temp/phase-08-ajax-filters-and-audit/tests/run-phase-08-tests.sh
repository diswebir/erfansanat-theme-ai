#!/usr/bin/env bash
# تست جامع پایان فاز 08 — جایگزینی Native برای JetSearch، JetSmartFilters، فرم‌ها و ممیزی نهایی
# اجرا: bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-08-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0
for t in "$ROOT"/temp/phase-08-ajax-filters-and-audit/tests/test-task-*.php; do
  echo "--- Running $(basename "$t")"
  "$PHP" -f "$t" || rc=1
done
exit $rc
