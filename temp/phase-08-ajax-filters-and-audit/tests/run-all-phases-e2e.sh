#!/usr/bin/env bash
# E2E: run every phase test suite
set -u
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
FAIL=0
for d in "$ROOT"/temp/phase-*/tests/run-phase-tests.sh; do
  echo "===== $d ====="
  bash "$d" || FAIL=1
done
echo "E2E RESULT: $([ $FAIL -eq 0 ] && echo ALL-PASS || echo FAILURES)"
exit $FAIL
