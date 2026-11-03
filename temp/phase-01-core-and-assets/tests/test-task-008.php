<?php
/**
 * P01-T008 — انتقال CSS و فونت‌ها
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T008', 'انتقال CSS و فونت‌ها' );

es_check( 'target: erfansanat/assets/css/styles.css', es_file('assets/css/styles.css', ES_THEME) );
es_check( 'target: erfansanat/assets/fonts/', es_dir('assets/fonts', ES_THEME) );

es_pending( 'SC-01: HTTP 200 برای CSS/woff2.' );
es_pending( 'SC-02: مقایسه بایتی با mirror پاس شود.' );

es_summary();
