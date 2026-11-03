#!/usr/bin/env bash
# Phase 2 test suite — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
set -uo pipefail
cd "$(dirname "$0")/.."         # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 2 ##########"
run_test "Task 2.1" "test-task-1.php"
run_test "Task 2.2" "test-task-2.php"
run_test "Task 2.3" "test-task-3.php"

phase_summary "Phase 2"
