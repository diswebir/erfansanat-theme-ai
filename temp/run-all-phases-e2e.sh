#!/usr/bin/env bash
# End-to-end suite: re-run every phase's tests (Task 8.3).
set -u
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
overall=0
for d in "$ROOT"/temp/phase-*/tests/run-phase-tests.sh; do
  bash "$d" || overall=1
done
if [ "$overall" -eq 0 ]; then echo "E2E RESULT: ALL PHASES PASS"; else echo "E2E RESULT: FAILURES"; fi
exit "$overall"
