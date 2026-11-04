#!/usr/bin/env bash
# Master End-to-End Test Suite — Runs All Phases (Phase 01 through Phase 08)
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
echo "########################################################"
echo " ERFAN SANAT THEME — FULL E2E TEST SUITE (PHASES 1..8)"
echo "########################################################"

bash "$ROOT_DIR/temp/phase-01-core-and-assets/tests/run-phase-01-tests.sh"
bash "$ROOT_DIR/temp/phase-02-custom-admin-dashboard/tests/run-phase-02-tests.sh"
bash "$ROOT_DIR/temp/phase-03-cpt-and-legacy-meta/tests/run-phase-03-tests.sh"
bash "$ROOT_DIR/temp/phase-04-header-footer-navigation/tests/run-phase-04-tests.sh"
bash "$ROOT_DIR/temp/phase-05-homepage-and-sections/tests/run-phase-05-tests.sh"
bash "$ROOT_DIR/temp/phase-06-woocommerce-and-products/tests/run-phase-06-tests.sh"
bash "$ROOT_DIR/temp/phase-07-projects-blog-and-pages/tests/run-phase-07-tests.sh"
bash "$ROOT_DIR/temp/phase-08-ajax-filters-and-audit/tests/run-phase-08-tests.sh"

echo "########################################################"
echo " [ALL PHASES PASSED] Theme is 100% verified & ready!"
echo "########################################################"
