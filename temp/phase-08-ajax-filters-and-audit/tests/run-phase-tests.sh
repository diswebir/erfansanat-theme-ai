#!/usr/bin/env bash
# Comprehensive phase test runner: ایجکس، فرم‌ها، ممیزی و استقرار
set -u
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT="$(cd "$DIR/../../.." && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
echo "================ ایجکس، فرم‌ها، ممیزی و استقرار ================"
if [ -f "$ROOT/erfansanat/style.css" ]; then
  wp theme activate erfansanat || exit 1
fi
fail=0
for t in "$DIR"/test-task-*.php; do
  echo "---- $(basename "$t") ----"
  php "$t" || fail=1
done
if [ "$fail" -eq 0 ]; then echo "PHASE RESULT: ALL PASS"; else echo "PHASE RESULT: FAILURES/PENDING"; fi
exit "$fail"
