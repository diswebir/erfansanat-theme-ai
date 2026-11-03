<?php
/**
 * P07-T019 — صفحه 404
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T019', 'صفحه 404' );

es_check( 'target: erfansanat/404.php', es_file('404.php', ES_THEME) );

es_pending( 'SC-01: URL ناموجود 404 و صفحه طراحی‌شده بدهد.' );

es_summary();
