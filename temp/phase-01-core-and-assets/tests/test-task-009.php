<?php
/**
 * P01-T009 — انتقال JS و رسانه‌های پایه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T009', 'انتقال JS و رسانه‌های پایه' );

es_check( 'target: erfansanat/assets/js/main.js', es_file('assets/js/main.js', ES_THEME) );
es_check( 'target: erfansanat/assets/images/', es_dir('assets/images', ES_THEME) );

es_pending( 'SC-01: assetها HTTP 200 بدهند.' );
es_pending( 'SC-02: هیرو ویدیو در صفحه اصلی لود شود.' );

es_summary();
