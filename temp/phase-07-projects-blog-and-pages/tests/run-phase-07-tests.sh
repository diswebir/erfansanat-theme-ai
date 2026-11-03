#!/usr/bin/env bash
# تست جامع پایان فاز 07 — تبدیل صفحات پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
# اجرا: bash temp/phase-07-projects-blog-and-pages/tests/run-phase-07-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0
for t in "$ROOT"/temp/phase-07-projects-blog-and-pages/tests/test-task-*.php; do
  echo "--- Running $(basename "$t")"
  "$PHP" -f "$t" || rc=1
done
exit $rc
