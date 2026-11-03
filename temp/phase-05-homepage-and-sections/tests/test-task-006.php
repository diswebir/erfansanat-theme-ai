<?php
/**
 * P05-T006 — اسلایدهای انتخابی هیرو از پروژه‌ها
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T006', 'اسلایدهای انتخابی هیرو از پروژه‌ها' );

es_check( 'target: erfansanat/inc/Options/Tabs/HomeTab.php', es_file('inc/Options/Tabs/HomeTab.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/home/section-hero.php', es_file('template-parts/home/section-hero.php', ES_THEME) );

es_pending( 'SC-01: دو پروژه انتخاب و به ترتیب رندر شوند.' );
es_pending( 'SC-02: پروژه پیش‌نویس نمایش داده نشود.' );

es_summary();
