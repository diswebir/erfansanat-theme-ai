#!/usr/bin/env bash
# Phase 1 test suite — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
set -uo pipefail
cd "$(dirname "$0")/.."         # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 1 ##########"
run_test "Task 1.1" "test-task-1.php"
run_test "Task 1.2" "test-task-2.php"
run_test "Task 1.3" "test-task-3.php"

phase_summary "Phase 1"
