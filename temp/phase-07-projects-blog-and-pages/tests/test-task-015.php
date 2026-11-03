<?php
/**
 * P07-T015 — شمارنده بازدید نوشته/پروژه/محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T015', 'شمارنده بازدید نوشته/پروژه/محصول' );

es_check( 'target: erfansanat/inc/Core/ViewCounter.php', es_file('inc/Core/ViewCounter.php', ES_THEME) );

es_pending( 'SC-01: بازدید عادی +1 شود.' );
es_pending( 'SC-02: preview/admin/bot افزایش ندهد.' );

es_summary();
