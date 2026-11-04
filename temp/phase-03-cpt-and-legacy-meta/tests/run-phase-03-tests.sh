#!/usr/bin/env sh
set -eu
ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/../../.." && pwd)"
cd "$ROOT"
exec node tools/harness/run-phase.mjs temp/phase-03-cpt-and-legacy-meta/tests
