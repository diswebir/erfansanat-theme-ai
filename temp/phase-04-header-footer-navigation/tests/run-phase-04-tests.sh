#!/usr/bin/env bash
# Task-aware runner for the Phase 4 PHPUnit contracts.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
cd "$ROOT"
exec tools/run-php-tests.sh temp/phase-04-header-footer-navigation/tests "$@"
