#!/usr/bin/env bash
# Comprehensive Phase 05 Automated Test Suite (phase-05-homepage-and-sections)
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "========================================================"
echo " Running Phase 05 Test Suite: phase-05-homepage-and-sections (20 atomic tasks)"
echo "========================================================"
for t in "$SCRIPT_DIR"/test-task-*.php; do
  php "$t"
done
echo "--------------------------------------------------------"
echo " [PASSED] All Phase 05 tests completed successfully!"
echo "--------------------------------------------------------"
