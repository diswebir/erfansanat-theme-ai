#!/usr/bin/env bash
# Helper sourced by temp/phase-XX/tests/run-phase-XX-tests.sh
set -uo pipefail
ES_PASS=0; ES_FAIL=0
run_test() {
  local label="$1"; local file="$2"
  echo; echo "---- $label ($file) ----"
  if node ../tools/harness/run-tests.mjs "$file"; then ES_PASS=$((ES_PASS+1)); else ES_FAIL=$((ES_FAIL+1)); fi
}
phase_summary() {
  echo; echo "########## $1 : PASS=$ES_PASS FAIL=$ES_FAIL ##########"
  [ "$ES_FAIL" -eq 0 ] || exit 1
}
