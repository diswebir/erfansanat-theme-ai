<?php
/**
 * P05-T005 — تنظیمات ویدیوی اصلی هیرو
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T005', 'تنظیمات ویدیوی اصلی هیرو' );

es_check( 'target: erfansanat/inc/Options/Tabs/HomeTab.php', es_file('inc/Options/Tabs/HomeTab.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/home/section-hero.php', es_file('template-parts/home/section-hero.php', ES_THEME) );

es_pending( 'SC-01: ویدیوی انتخابی در هیرو لود شود.' );
es_pending( 'SC-02: حذف ویدیو هیرو را خراب نکند.' );

es_summary();
