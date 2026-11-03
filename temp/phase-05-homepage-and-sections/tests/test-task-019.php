<?php
/**
 * P05-T019 — سکشن جدیدترین مقالات
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T019', 'سکشن جدیدترین مقالات' );

es_check( 'target: erfansanat/template-parts/home/section-blog.php', es_file('template-parts/home/section-blog.php', ES_THEME) );

es_pending( 'SC-01: سه مقاله آخر رندر شوند.' );
es_pending( 'SC-02: مقاله پیش‌نویس نمایش داده نشود.' );

es_summary();
