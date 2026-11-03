<?php
/**
 * P01-T001 — هدر قالب و style.css
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T001', 'هدر قالب و style.css' );

es_check( 'target: erfansanat/style.css', es_file('style.css', ES_THEME) );

es_pending( 'SC-01: WP قالب را در فهرست پوسته‌ها نشان دهد.' );
es_pending( 'SC-02: فعال‌سازی بدون fatal انجام شود.' );

es_summary();
