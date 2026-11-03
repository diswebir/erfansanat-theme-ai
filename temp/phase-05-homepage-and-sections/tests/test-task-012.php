<?php
/**
 * P05-T012 — سکشن تاریخچه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T012', 'سکشن تاریخچه' );

es_check( 'target: erfansanat/template-parts/home/section-history.php', es_file('template-parts/home/section-history.php', ES_THEME) );

es_pending( 'SC-01: افزودن آیتم تاریخچه رندر شود.' );
es_pending( 'SC-02: شمارنده کل با تعداد آیتم برابر باشد.' );

es_summary();
