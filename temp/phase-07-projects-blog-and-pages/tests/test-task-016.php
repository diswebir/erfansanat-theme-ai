<?php
/**
 * P07-T016 — مقالات مرتبط
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T016', 'مقالات مرتبط' );

es_check( 'target: erfansanat/template-parts/post/related.php', es_file('template-parts/post/related.php', ES_THEME) );

es_pending( 'SC-01: سه مقاله مرتبط بدون نوشته جاری رندر شوند.' );

es_summary();
