#!/usr/bin/env bash
# Comprehensive Phase 06 Automated Test Suite (phase-06-woocommerce-and-products)
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "========================================================"
echo " Running Phase 06 Test Suite: phase-06-woocommerce-and-products (20 atomic tasks)"
echo "========================================================"
for t in "$SCRIPT_DIR"/test-task-*.php; do
  php "$t"
done
echo "--------------------------------------------------------"
echo " [PASSED] All Phase 06 tests completed successfully!"
echo "--------------------------------------------------------"
