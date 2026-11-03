#!/usr/bin/env bash
# تست جامع پایان فاز 06 — تبدیل قالب‌های ووکامرس، محصولات تکی و آرشیو فروشگاه
# اجرا: bash temp/phase-06-woocommerce-and-products/tests/run-phase-06-tests.sh
set -u
PHP=/home/user/tools/php
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
rc=0
for t in "$ROOT"/temp/phase-06-woocommerce-and-products/tests/test-task-*.php; do
  echo "--- Running $(basename "$t")"
  "$PHP" -f "$t" || rc=1
done
exit $rc
