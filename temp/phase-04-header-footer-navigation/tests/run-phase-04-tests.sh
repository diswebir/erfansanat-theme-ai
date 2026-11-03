#!/usr/bin/env bash
# Phase 4 test suite — هدر، مگامنو، منوی موبایل و فوتر
set -uo pipefail
cd "$(dirname "$0")/.."         # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 4 ##########"
run_test "Task 4.1" "test-task-1.php"
run_test "Task 4.2" "test-task-2.php"
run_test "Task 4.3" "test-task-3.php"

phase_summary "Phase 4"
