#!/bin/sh
set -eu
ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/../../.." && pwd)"
PHP_BIN="${PHP_BIN:-php}"
"$PHP_BIN" "$ROOT/temp/phase-08-ajax-filters-and-audit/tests/test-task-01.php"
"$PHP_BIN" "$ROOT/temp/phase-08-ajax-filters-and-audit/tests/test-task-02.php"
"$PHP_BIN" "$ROOT/temp/phase-08-ajax-filters-and-audit/tests/test-task-03.php"
printf "Phase 08 tests passed.\n"
