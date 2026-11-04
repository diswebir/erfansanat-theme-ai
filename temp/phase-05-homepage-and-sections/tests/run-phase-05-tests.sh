#!/usr/bin/env bash
# Phase 5 suite — isolate PHP-WASM requests so per-task WordPress stubs cannot collide.
set -euo pipefail
cd "$(dirname "$0")/../../.."
exec node tools/harness/run-phase-isolated.mjs temp/phase-05-homepage-and-sections/tests
