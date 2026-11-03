#!/usr/bin/env bash
# Comprehensive phase test runner: ووکامرس، محصولات تکی و آرشیو
set -u
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT="$(cd "$DIR/../../.." && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
echo "================ ووکامرس، محصولات تکی و آرشیو ================"
# Pre-activate our theme in a separate process so after_setup_theme fires with
# the correct theme when each test loads WP (theme-support checks stay accurate).
if [ -f "$ROOT/erfansanat/style.css" ]; then
  wp theme activate erfansanat >/dev/null 2>&1 || true
fi
fail=0
for t in "$DIR"/test-task-*.php; do
  echo "---- $(basename "$t") ----"
  php "$t" || fail=1
done
if [ "$fail" -eq 0 ]; then echo "PHASE RESULT: ALL PASS"; else echo "PHASE RESULT: FAILURES"; fi
exit "$fail"
