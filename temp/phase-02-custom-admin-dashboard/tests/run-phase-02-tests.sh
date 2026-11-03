#!/usr/bin/env bash
# تست جامع پایان فاز 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
# اجرا: bash temp/phase-02-custom-admin-dashboard/tests/run-phase-02-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0; total=0; passed=0
for t in "$ROOT"/temp/phase-02-custom-admin-dashboard/tests/test-task-*.php; do
  echo "--- $(basename "$t")"
  if "$PHP" -f "$t"; then passed=$((passed+1)); else rc=1; fi
  total=$((total+1))
done
echo "== فاز 02: {passed}/{total} تسک پاس =="
exit $rc
