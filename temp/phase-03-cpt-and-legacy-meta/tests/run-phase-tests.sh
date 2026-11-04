#!/usr/bin/env bash
# Comprehensive phase test runner: CPTها، تکسونومی‌ها و متاباکس‌های Legacy
set -u
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT="$(cd "$DIR/../../.." && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
echo "================ CPTها، تکسونومی‌ها و متاباکس‌های Legacy ================"
if [ -f "$ROOT/erfansanat/style.css" ]; then
  if ! wp theme is-active erfansanat --allow-root >/dev/null 2>&1; then
    wp theme activate erfansanat --allow-root >/dev/null 2>&1 || { echo "[FAIL] cannot activate erfansanat"; exit 1; }
  fi
fi
fail=0
for t in "$DIR"/test-task-*.php; do
  echo "---- $(basename "$t") ----"
  php "$t" || fail=1
done
if [ "$fail" -eq 0 ]; then echo "PHASE RESULT: ALL PASS"; else echo "PHASE RESULT: FAILURES/PENDING"; fi
exit "$fail"
