<?php
/**
 * P05-T010 — سکشن درباره و آمار
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T010', 'سکشن درباره و آمار' );

es_check( 'target: erfansanat/template-parts/home/section-about.php', es_file('template-parts/home/section-about.php', ES_THEME) );

es_pending( 'SC-01: تغییر عدد آمار در خروجی دیده شود.' );
es_pending( 'SC-02: شمارنده انیمیشن داشته باشد.' );

es_summary();
