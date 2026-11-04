#!/usr/bin/env bash
# Phase 4 focused suite — isolate each PHP-WASM test request to avoid global stub collisions.
set -euo pipefail
cd "$(dirname "$0")/../../.."
exec node tools/harness/run-phase-isolated.mjs temp/phase-04-header-footer-navigation/tests
