#!/usr/bin/env bash
# تست جامع پایان فاز 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
# اجرا: bash temp/phase-01-core-and-assets/tests/run-phase-01-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0
for t in "$ROOT"/temp/phase-01-core-and-assets/tests/test-task-*.php; do
  echo "--- Running $(basename "$t")"
  "$PHP" -f "$t" || rc=1
done
exit $rc
