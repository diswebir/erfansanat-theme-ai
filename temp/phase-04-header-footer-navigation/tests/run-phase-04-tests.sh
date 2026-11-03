#!/usr/bin/env bash
# تست جامع پایان فاز 04 — هدر، مگامنو، منوی موبایل و فوتر
# اجرا: bash temp/phase-04-header-footer-navigation/tests/run-phase-04-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0; total=0; passed=0
for t in "$ROOT"/temp/phase-04-header-footer-navigation/tests/test-task-*.php; do
  echo "--- $(basename "$t")"
  if "$PHP" -f "$t"; then passed=$((passed+1)); else rc=1; fi
  total=$((total+1))
done
echo "== فاز 04: {passed}/{total} تسک پاس =="
exit $rc
