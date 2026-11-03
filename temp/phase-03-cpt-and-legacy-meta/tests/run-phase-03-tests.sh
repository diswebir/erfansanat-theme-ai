#!/usr/bin/env bash
# Phase 3 test suite — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
set -uo pipefail
cd "$(dirname "$0")/.."         # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 3 ##########"
run_test "Task 3.1" "test-task-1.php"
run_test "Task 3.2" "test-task-2.php"
run_test "Task 3.3" "test-task-3.php"

phase_summary "Phase 3"
