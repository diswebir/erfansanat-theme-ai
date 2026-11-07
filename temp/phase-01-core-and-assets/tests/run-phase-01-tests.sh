#!/usr/bin/env bash
# Phase 1 acceptance runner: PHPUnit contracts plus HTTP/asset checks.
#
# Usage:
#   temp/phase-01-core-and-assets/tests/run-phase-01-tests.sh [--task 1.1|1.2|1.3]
#
# Requires the local WordPress preview on 127.0.0.1:8080 (tools/preview-wordpress.sh).
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
cd "$ROOT"

TASK_ARGS=()
FILTER=""
if [[ "${1:-}" == "--task" && -n "${2:-}" ]]; then
  TASK_ARGS=(--task "$2")
  FILTER="task-0${2%%.*}"
fi

printf '%s\n' '[1/3] PHP syntax of the theme'
while IFS= read -r file; do
  tools/php -l "$file" | grep -v 'No syntax errors' || true
done < <(find erfansanat -name '*.php' -type f)

printf '%s\n' '[2/3] PHPUnit acceptance contracts'
tools/run-php-tests.sh temp/phase-01-core-and-assets/tests "$FILTER"

printf '%s\n' '[3/3] HTTP, asset and admin checks'
python3 temp/phase-01-core-and-assets/tests/check-http.py "${TASK_ARGS[@]}"

printf '%s\n' 'PHASE 1 TESTS PASSED — isolated local WordPress only.'
