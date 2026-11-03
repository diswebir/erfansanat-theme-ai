#!/usr/bin/env bash
# Phase 8 test suite — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی
set -uo pipefail
cd "$(dirname "$0")/.."         # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 8 ##########"
run_test "Task 8.1" "test-task-1.php"
run_test "Task 8.2" "test-task-2.php"
run_test "Task 8.3" "test-task-3.php"

phase_summary "Phase 8"
