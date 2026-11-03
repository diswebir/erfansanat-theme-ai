<?php
/**
 * P05-T009 — سکشن پروژه‌های منتخب
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T009', 'سکشن پروژه‌های منتخب' );

es_check( 'target: erfansanat/template-parts/home/section-featured-projects.php', es_file('template-parts/home/section-featured-projects.php', ES_THEME) );

es_pending( 'SC-01: تعداد کارت با تنظیم برابر باشد.' );
es_pending( 'SC-02: پروژه پیش‌نویس نمایش داده نشود.' );

es_summary();
