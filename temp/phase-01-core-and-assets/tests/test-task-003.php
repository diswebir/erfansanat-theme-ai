<?php
/**
 * P01-T003 — Autoloader اختصاصی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T003', 'Autoloader اختصاصی' );

es_check( 'target: erfansanat/inc/Autoloader.php', es_file('inc/Autoloader.php', ES_THEME) );

es_pending( 'SC-01: بارگذاری کلاس نمونه موفق باشد.' );
es_pending( 'SC-02: کلاس ناشناخته خطای قابل‌کنترل بدهد.' );

es_summary();
