#!/usr/bin/env bash
# تست جامع پایان فاز 04 — تبدیل هدر، مگامنو، منوی موبایل و فوتر استاتیک
# اجرا: bash temp/phase-04-header-footer-navigation/tests/run-phase-04-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0
for t in "$ROOT"/temp/phase-04-header-footer-navigation/tests/test-task-*.php; do
  echo "--- Running $(basename "$t")"
  "$PHP" -f "$t" || rc=1
done
exit $rc
