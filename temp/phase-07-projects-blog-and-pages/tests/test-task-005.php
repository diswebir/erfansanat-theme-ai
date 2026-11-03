<?php
/**
 * P07-T005 — پروژه‌های مرتبط
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T005', 'پروژه‌های مرتبط' );

es_check( 'target: erfansanat/template-parts/project/related-projects.php', es_file('template-parts/project/related-projects.php', ES_THEME) );

es_pending( 'SC-01: چهار پروژه مرتبط بدون پروژه جاری رندر شوند.' );

es_summary();
