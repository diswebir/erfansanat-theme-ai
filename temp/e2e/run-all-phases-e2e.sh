#!/usr/bin/env bash
# سوئیت سرتاسری کل پروژه — اجرای تست‌های تمام فازها (تسک 8.3)
set -u
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0
for run in "$ROOT"/temp/phase-*/tests/run-phase-*-tests.sh; do
  echo "=== $(basename "$run")"
  bash "$run" || rc=1
done
exit $rc
