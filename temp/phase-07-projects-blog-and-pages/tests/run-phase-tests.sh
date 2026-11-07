#!/usr/bin/env bash
# Comprehensive phase test runner: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها
set -u
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT="$(cd "$DIR/../../.." && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
echo "================ پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها ================"
if [ -f "$ROOT/erfansanat/style.css" ]; then
  wp theme activate erfansanat || exit 1
fi
fail=0
for t in "$DIR"/test-task-*.php; do
  echo "---- $(basename "$t") ----"
  php "$t" || fail=1
done
# Companion behaviour tests execute the immutable production main.js in JSDOM.
for t in "$DIR"/test-task-*-dom.mjs; do
  [ -f "$t" ] || continue
  echo "---- $(basename "$t") ----"
  node "$t" || fail=1
done
if [ "$fail" -eq 0 ]; then echo "PHASE RESULT: ALL PASS"; else echo "PHASE RESULT: FAILURES/PENDING"; fi
exit "$fail"
