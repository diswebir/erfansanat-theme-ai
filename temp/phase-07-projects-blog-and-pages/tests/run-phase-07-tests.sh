#!/usr/bin/env bash
# Comprehensive Phase 07 Automated Test Suite (phase-07-projects-blog-and-pages)
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "========================================================"
echo " Running Phase 07 Test Suite: phase-07-projects-blog-and-pages (20 atomic tasks)"
echo "========================================================"
for t in "$SCRIPT_DIR"/test-task-*.php; do
  php "$t"
done
echo "--------------------------------------------------------"
echo " [PASSED] All Phase 07 tests completed successfully!"
echo "--------------------------------------------------------"
