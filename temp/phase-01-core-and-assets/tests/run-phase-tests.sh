#!/usr/bin/env bash
# P01 — run all phase tests (strict).
set -uo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
fail=0

# Prepare real QA state: active theme, HTTP asset report, deploy zip.
if ! wp theme is-active erfansanat --allow-root >/dev/null 2>&1; then
  wp theme activate erfansanat --allow-root >/dev/null 2>&1 || { echo "[FAIL] cannot activate erfansanat"; fail=1; }
fi
bash "$ROOT/temp/tools/check-assets-http.sh" || fail=1
bash "$ROOT/temp/tools/package-theme.sh" || fail=1

for test in "$DIR"/test-task-*.php; do
  echo "----- $(basename "$test") -----"
  php "$test"
  code=$?
  if [ $code -ne 0 ]; then
    echo "[FAIL] $(basename "$test") exit=$code"
    fail=1
  fi
done
if [ $fail -ne 0 ]; then
  echo "PHASE RESULT: FAIL"
  exit 1
fi
echo "PHASE RESULT: PASS"
