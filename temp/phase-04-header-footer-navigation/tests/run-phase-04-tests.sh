#!/usr/bin/env bash
# Comprehensive Phase 04 Automated Test Suite (phase-04-header-footer-navigation)
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "========================================================"
echo " Running Phase 04 Test Suite: phase-04-header-footer-navigation (11 atomic tasks)"
echo "========================================================"
for t in "$SCRIPT_DIR"/test-task-*.php; do
  php "$t"
done
echo "--------------------------------------------------------"
echo " [PASSED] All Phase 04 tests completed successfully!"
echo "--------------------------------------------------------"
