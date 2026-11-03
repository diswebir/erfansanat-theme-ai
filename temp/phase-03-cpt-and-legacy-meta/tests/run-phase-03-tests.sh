#!/usr/bin/env bash
# تست جامع پایان فاز 03 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
# اجرا: bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-03-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0
for t in "$ROOT"/temp/phase-03-cpt-and-legacy-meta/tests/test-task-*.php; do
  echo "--- Running $(basename "$t")"
  "$PHP" -f "$t" || rc=1
done
exit $rc
