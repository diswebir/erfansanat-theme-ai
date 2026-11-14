#!/usr/bin/env bash
# End-to-end suite (P08-T024): re-run every phase's strict tests, write a
# readiness report, and exit non-zero on any failure or unauthorized skip.
set -u
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
REPORT="$ROOT/temp/e2e-report.md"
STATE="$ROOT/temp/e2e-state.txt"
STAMP="$(date -u +%Y-%m-%dT%H:%M:%SZ)"
overall=0
total_pass=0
total_fail=0
total_skip=0 # authorized pendings (e.g. P06-T028 staging deferral) stay visible but do not fail AC-01
: > "$STATE" # per-phase "phase|pass|fail" lines, read by P08-T024 mid-run

{
  echo "# گزارش E2E نهایی — $STAMP"
  echo
  echo "| فاز | پاس | رد |"
  echo "|---|---|---|"
} > "$REPORT"

for d in "$ROOT"/temp/phase-*/tests/run-phase-tests.sh; do
  phase="$(basename "$(dirname "$(dirname "$d")")")"
  out="$(bash "$d" 2>&1)" || overall=1
  p="$(printf '%s\n' "$out" | grep -oE '[0-9]+ passed' | awk '{s+=$1} END {print s+0}')"
  f="$(printf '%s\n' "$out" | grep -oE '[0-9]+ failed' | awk '{s+=$1} END {print s+0}')"
  s="$(printf '%s\n' "$out" | grep -oE '[0-9]+ skipped/pending' | awk '{s+=$1} END {print s+0}')"
  total_pass=$((total_pass + p))
  total_fail=$((total_fail + f))
  total_skip=$((total_skip + s))
  echo "| $phase | $p | $f |" >> "$REPORT"
  printf '%s\n' "$out" | grep -E "RESULT:" >> "$REPORT" || true
  if [ "$f" -gt 0 ]; then
    overall=1
    echo "PHASE $phase: failed=$f" >&2
  fi
  echo "$phase|$p|$f" >> "$STATE"
done

{
  echo
  echo "مجموع: $total_pass پاس، $total_fail رد، $total_skip pending مجاز (مستند در تست مربوطه)"
  if [ "$overall" -eq 0 ]; then
    echo
    echo "E2E RESULT: ALL PHASES PASS"
  else
    echo
    echo "E2E RESULT: FAILURES/PENDING"
  fi
} >> "$REPORT"

if [ "$overall" -eq 0 ]; then echo "E2E RESULT: ALL PHASES PASS ($total_pass checks)"; else echo "E2E RESULT: FAILURES/PENDING ($total_fail failed)"; fi
exit "$overall"
