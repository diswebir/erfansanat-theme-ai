#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
cd "$ROOT"
for test in "temp/phase-02-custom-admin-dashboard/tests"/test-task-*.php; do
  tools/phpunit --do-not-cache-result --no-progress --colors=never "$test"
done
