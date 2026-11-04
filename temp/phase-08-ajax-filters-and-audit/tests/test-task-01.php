<?php
/**
 * Automated QA Test Script — Task 8.1: زیرساخت AJAX قالب
 * Phase: phase-08-ajax-filters-and-audit
 * NOTE: Skeleton — completed in [WRITE TEST] step of Task 8.1 before implementation (TDD RED).
 */
declare(strict_types=1);

$themeRoot = dirname(__DIR__, 3);
$wpRoot    = '/opt/wp-testbed';

echo "=== [TEST] Task 8.1: زیرساخت AJAX قالب ===\n";

// Target files for this atomic task:
//   - inc/Ajax/AjaxController.php

if (!is_dir($themeRoot)) {
    fwrite(STDERR, "[FAIL] Theme root directory missing.\n");
    exit(1);
}

echo "[SKELETON READY] Task 8.1 test harness initialized — scenarios defined in tasks/task-01.md\n";
exit(0);
