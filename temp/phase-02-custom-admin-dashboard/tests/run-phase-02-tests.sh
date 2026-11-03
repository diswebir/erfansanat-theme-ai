#!/usr/bin/env bash
# تست جامع پایان فاز 02 — مهندسی فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
# اجرا: bash temp/phase-02-custom-admin-dashboard/tests/run-phase-02-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0
for t in "$ROOT"/temp/phase-02-custom-admin-dashboard/tests/test-task-*.php; do
  echo "--- Running $(basename "$t")"
  "$PHP" -f "$t" || rc=1
done
exit $rc
