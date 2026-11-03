<?php
/**
 * P07-T009 — آرشیو اصلی وبلاگ
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T009', 'آرشیو اصلی وبلاگ' );

es_check( 'target: erfansanat/home.php', es_file('home.php', ES_THEME) );

es_pending( 'SC-01: /blog/ مقالات را رندر کند.' );
es_pending( 'SC-02: صفحه دوم وبلاگ کار کند.' );

es_summary();
