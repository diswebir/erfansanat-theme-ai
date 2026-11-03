#!/usr/bin/env bash
# Phase 7 test suite — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
set -uo pipefail
cd "$(dirname "$0")/.."         # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 7 ##########"
run_test "Task 7.1" "test-task-1.php"
run_test "Task 7.2" "test-task-2.php"
run_test "Task 7.3" "test-task-3.php"

phase_summary "Phase 7"
