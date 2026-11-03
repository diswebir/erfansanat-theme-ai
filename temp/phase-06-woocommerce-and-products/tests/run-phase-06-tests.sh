#!/usr/bin/env bash
# Phase 6 test suite — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
set -uo pipefail
cd "$(dirname "$0")/.."         # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 6 ##########"
run_test "Task 6.1" "test-task-1.php"
run_test "Task 6.2" "test-task-2.php"
run_test "Task 6.3" "test-task-3.php"

phase_summary "Phase 6"
