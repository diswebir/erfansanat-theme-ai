<?php
/**
 * P04-T011 — تب‌های داخلی مگامنوی موبایل
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T011', 'تب‌های داخلی مگامنوی موبایل' );

es_check( 'target: erfansanat/inc/Core/NavWalkers.php', es_file('inc/Core/NavWalkers.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/header/mobile-menu.php', es_file('template-parts/header/mobile-menu.php', ES_THEME) );

es_pending( 'SC-01: تب نوع/شهر در موبایل عوض شود.' );
es_pending( 'SC-02: تعداد آیتم‌ها با تنظیمات برابر باشد.' );

es_summary();
