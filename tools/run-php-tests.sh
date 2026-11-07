#!/usr/bin/env bash
# Run WordPress PHPUnit contracts whose filenames are task-based rather than class-based.
#
# PHPUnit 12 derives the expected class suffix from the file basename, so
# `test-task-01.php` can never contain `TaskP01T01Test`. Each contract is copied
# to a cache path named after its class and pointed at the absolute bootstrap.
#
# Usage:
#   tools/run-php-tests.sh temp/phase-01-core-and-assets/tests [1.1]
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
TEST_DIR="${1:?usage: tools/run-php-tests.sh temp/phase-XX/tests [tag]}"
FILTER="${2:-}"
CACHE="${ERFANSANAT_PHPUNIT_CACHE:-$HOME/.cache/erfansanat-phpunit}"
mkdir -p "$CACHE"
TEST_DIR="$(cd "$TEST_DIR" && pwd)"
BOOTSTRAP="$TEST_DIR/bootstrap.php"
shopt -s nullglob
tests=("$TEST_DIR"/test-*.php)
if [[ ${#tests[@]} -eq 0 ]]; then echo "No tests found in $TEST_DIR" >&2; exit 1; fi
failed=0
ran=0
for test in "${tests[@]}"; do
  base="$(basename "$test" .php)"
  if [[ -n "$FILTER" && "$base" != *"$FILTER"* ]]; then
    continue
  fi
  class="$(grep -oE 'final class [A-Za-z0-9_]+' "$test" | head -1 | awk '{print $3}')"
  if [[ -z "$class" ]]; then echo "No test class in $test" >&2; exit 1; fi
  prepared="$CACHE/$class.php"
  python3 - "$test" "$BOOTSTRAP" "$prepared" <<'PY'
import re, sys
src, bootstrap, out = sys.argv[1:]
text = open(src, encoding="utf-8").read()
text = re.sub(r"require_once __DIR__ \. '/bootstrap\.php';", f"require_once '{bootstrap}';", text)
open(out, "w", encoding="utf-8").write(text)
PY
  ran=$((ran + 1))
  if ! tools/phpunit --do-not-cache-result --no-progress --colors=never "$prepared"; then
    failed=$((failed + 1))
  fi
done
if [[ "$ran" -eq 0 ]]; then echo "No test matched '${FILTER}' in $TEST_DIR" >&2; exit 1; fi
if [[ "$failed" -ne 0 ]]; then echo "$failed of $ran contract files failed." >&2; exit 1; fi
echo "All $ran contract files passed."
