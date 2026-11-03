#!/usr/bin/env bash
# تست جامع پایان فاز 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
# اجرا: bash temp/phase-05-homepage-and-sections/tests/run-phase-05-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0
for t in "$ROOT"/temp/phase-05-homepage-and-sections/tests/test-task-*.php; do
  echo "--- Running $(basename "$t")"
  "$PHP" -f "$t" || rc=1
done
exit $rc
