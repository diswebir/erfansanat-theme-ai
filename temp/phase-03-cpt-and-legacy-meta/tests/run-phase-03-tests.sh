#!/usr/bin/env bash
# Comprehensive Phase 03 Automated Test Suite (phase-03-cpt-and-legacy-meta)
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "========================================================"
echo " Running Phase 03 Test Suite: phase-03-cpt-and-legacy-meta (17 atomic tasks)"
echo "========================================================"
for t in "$SCRIPT_DIR"/test-task-*.php; do
  php "$t"
done
echo "--------------------------------------------------------"
echo " [PASSED] All Phase 03 tests completed successfully!"
echo "--------------------------------------------------------"
