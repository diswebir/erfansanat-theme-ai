#!/usr/bin/env bash
# Master End-to-End Test Suite — Phases 01 through 08 plus final static-source readiness gate.
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
echo "########################################################"
echo " ERFAN SANAT THEME — FULL E2E TEST SUITE (PHASES 1..8)"
echo "########################################################"

bash "$ROOT_DIR/temp/phase-01-core-and-assets/tests/run-phase-01-tests.sh"
bash "$ROOT_DIR/temp/phase-02-custom-admin-dashboard/tests/run-phase-02-tests.sh"
bash "$ROOT_DIR/temp/phase-03-cpt-and-legacy-meta/tests/run-phase-03-tests.sh"
bash "$ROOT_DIR/temp/phase-04-header-footer-navigation/tests/run-phase-04-tests.sh"
bash "$ROOT_DIR/temp/phase-05-homepage-and-sections/tests/run-phase-05-tests.sh"
bash "$ROOT_DIR/temp/phase-06-woocommerce-and-products/tests/run-phase-06-tests.sh"
bash "$ROOT_DIR/temp/phase-07-projects-blog-and-pages/tests/run-phase-07-tests.sh"
bash "$ROOT_DIR/temp/phase-08-ajax-filters-and-audit/tests/run-phase-08-tests.sh"

node --check "$ROOT_DIR/assets/js/main.js"
while IFS= read -r -d '' php_file; do
    php -l "$php_file" >/dev/null
done < <(find "$ROOT_DIR" -type f -name '*.php' -not -path "$ROOT_DIR/.git/*" -print0)

if grep -RInE '\b(TODO|FIXME)\b' "$ROOT_DIR/inc" "$ROOT_DIR/template-parts" "$ROOT_DIR/page-templates" "$ROOT_DIR/assets/js/main.js"; then
    echo "[E2E FAILED] TODO/FIXME markers remain in shipped theme source." >&2
    exit 1
fi

echo "########################################################"
echo " [AUTOMATED TESTS PASSED] Phases 01..08 suites, PHP lint, JS syntax, and TODO scan are green."
echo "########################################################"

unpassed_tasks=()
for task_file in "$ROOT_DIR"/temp/phase-08-ajax-filters-and-audit/tasks/task-*.md; do
    task_name="$(basename "$task_file" .md)"
    [[ "$task_name" == "task-17" ]] && continue
    status_line="$(grep -m1 -F '**وضعیت:**' "$task_file" || true)"
    if [[ "$status_line" != *'[PASSED]'* ]]; then
        unpassed_tasks+=("$task_name")
    fi
done
if ((${#unpassed_tasks[@]} > 0)); then
    printf '[E2E BLOCKED] Full release readiness requires every Phase 08 task to be PASSED; unresolved tasks: %s\n' "${unpassed_tasks[*]}" >&2
    exit 2
fi

echo "########################################################"
echo " [ALL PHASES PASSED] Theme passed automated audits and every Phase 08 task is marked PASSED."
echo "########################################################"
