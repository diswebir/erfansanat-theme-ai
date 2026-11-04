#!/usr/bin/env bash
# Comprehensive Phase 1 Automated Test Suite (phase-01-core-and-assets)
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "========================================================"
echo " Running Phase 01 Test Suite: phase-01-core-and-assets"
echo "========================================================"

php "$SCRIPT_DIR/test-task-01.php"
php "$SCRIPT_DIR/test-task-02.php"
php "$SCRIPT_DIR/test-task-03.php"

echo "--------------------------------------------------------"
echo " [PASSED] All Phase 01 tests completed successfully!"
echo "--------------------------------------------------------"
