<?php
/**
 * P08-T024 — E2E نهایی و بسته انتشار
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-024.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T024', 'E2E نهایی و بسته انتشار' );

es_check( 'target: temp/run-all-phases-e2e.sh', es_file('run-all-phases-e2e.sh', ES_ROOT) );
es_check( 'target: temp/tools/package-theme.sh', es_file('tools/package-theme.sh', ES_ROOT) );
es_check( 'target: temp/RELEASE_CHECKLIST.md', es_file('RELEASE_CHECKLIST.md', ES_ROOT) );

es_pending( 'SC-01: run-all-phases-e2e.sh exit 0 بدهد.' );
es_pending( 'SC-02: زیپ انتشار روی نصب تمیز فعال شود.' );

es_summary();
