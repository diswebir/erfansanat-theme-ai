#!/usr/bin/env bash
# Comprehensive Phase 08 Automated Test Suite (phase-08-ajax-filters-and-audit)
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "========================================================"
echo " Running Phase 08 Test Suite: phase-08-ajax-filters-and-audit (17 atomic tasks)"
echo "========================================================"
for t in "$SCRIPT_DIR"/test-task-*.php; do
  php "$t"
done
echo "--------------------------------------------------------"
echo " [PASSED] All Phase 08 tests completed successfully!"
echo "--------------------------------------------------------"
