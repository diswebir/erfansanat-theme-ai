<?php
/**
 * P07-T018 — صفحه جستجو
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T018', 'صفحه جستجو' );

es_check( 'target: erfansanat/search.php', es_file('search.php', ES_THEME) );

es_pending( 'SC-01: جستجوی فارسی نتیجه درست بدهد.' );
es_pending( 'SC-02: عبارت ناموجود حالت خالی بدهد.' );

es_summary();
