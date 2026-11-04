#!/usr/bin/env bash
# Comprehensive Phase 02 Automated Test Suite (phase-02-custom-admin-dashboard)
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "========================================================"
echo " Running Phase 02 Test Suite: phase-02-custom-admin-dashboard (15 atomic tasks)"
echo "========================================================"
for t in "$SCRIPT_DIR"/test-task-*.php; do
  php "$t"
done
echo "--------------------------------------------------------"
echo " [PASSED] All Phase 02 tests completed successfully!"
echo "--------------------------------------------------------"
