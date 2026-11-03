#!/usr/bin/env bash
# تست جامع پایان فاز 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
# اجرا: bash temp/phase-05-homepage-and-sections/tests/run-phase-05-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0; total=0; passed=0
for t in "$ROOT"/temp/phase-05-homepage-and-sections/tests/test-task-*.php; do
  echo "--- $(basename "$t")"
  if "$PHP" -f "$t"; then passed=$((passed+1)); else rc=1; fi
  total=$((total+1))
done
echo "== فاز 05: $passed/$total تسک پاس =="
exit $rc
