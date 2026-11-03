<?php
/**
 * P01-T002 — Bootstrap و ثابت‌های قالب
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T002', 'Bootstrap و ثابت‌های قالب' );

es_check( 'target: erfansanat/functions.php', es_file('functions.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Constants.php', es_file('inc/Constants.php', ES_THEME) );

es_pending( 'SC-01: دو بار include کردن bootstrap خطا ندهد.' );
es_pending( 'SC-02: مسیرها روی نصب تست درست resolve شوند.' );

es_summary();
