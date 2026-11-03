#!/usr/bin/env bash
# تست جامع پایان فاز 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
# اجرا: bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-08-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0; total=0; passed=0
for t in "$ROOT"/temp/phase-08-ajax-filters-and-audit/tests/test-task-*.php; do
  echo "--- $(basename "$t")"
  if "$PHP" -f "$t"; then passed=$((passed+1)); else rc=1; fi
  total=$((total+1))
done
echo "== فاز 08: {passed}/{total} تسک پاس =="
exit $rc
