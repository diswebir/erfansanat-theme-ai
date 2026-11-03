#!/usr/bin/env bash
# Phase 5 test suite — صفحه اصلی و کامپوننت‌های ماژولار
set -uo pipefail
cd "$(dirname "$0")/.."         # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 5 ##########"
run_test "Task 5.1" "test-task-1.php"
run_test "Task 5.2" "test-task-2.php"
run_test "Task 5.3" "test-task-3.php"

phase_summary "Phase 5"
