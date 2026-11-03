#!/usr/bin/env bash
# تست جامع پایان فاز 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
# اجرا: bash temp/phase-07-projects-blog-and-pages/tests/run-phase-07-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0; total=0; passed=0
for t in "$ROOT"/temp/phase-07-projects-blog-and-pages/tests/test-task-*.php; do
  echo "--- $(basename "$t")"
  if "$PHP" -f "$t"; then passed=$((passed+1)); else rc=1; fi
  total=$((total+1))
done
echo "== فاز 07: $passed/$total تسک پاس =="
exit $rc
