#!/usr/bin/env bash
# تست جامع پایان فاز 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
# اجرا: bash temp/phase-01-core-and-assets/tests/run-phase-01-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0; total=0; passed=0
for t in "$ROOT"/temp/phase-01-core-and-assets/tests/test-task-*.php; do
  echo "--- $(basename "$t")"
  if "$PHP" -f "$t"; then passed=$((passed+1)); else rc=1; fi
  total=$((total+1))
done
echo "== فاز 01: $passed/$total تسک پاس =="
exit $rc
