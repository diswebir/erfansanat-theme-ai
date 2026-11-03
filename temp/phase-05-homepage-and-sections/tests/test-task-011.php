<?php
/**
 * P05-T011 — سکشن بومی‌سازی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T011', 'سکشن بومی‌سازی' );

es_check( 'target: erfansanat/template-parts/home/section-localization.php', es_file('template-parts/home/section-localization.php', ES_THEME) );

es_pending( 'SC-01: ویرایش متن در خروجی دیده شود.' );
es_pending( 'SC-02: تصویر جایگزین رندر شود.' );

es_summary();
