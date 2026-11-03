<?php
/**
 * P08-T009 — مرتب‌سازی/صفحه‌بندی/حذف فیلتر
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T009', 'مرتب‌سازی/صفحه‌بندی/حذف فیلتر' );

es_check( 'target: erfansanat/assets/js/product-filters.js', es_file('assets/js/product-filters.js', ES_THEME) );
es_check( 'target: erfansanat/inc/Core/SmartFilters.php', es_file('inc/Core/SmartFilters.php', ES_THEME) );

es_pending( 'SC-01: صفحه دوم فیلترشده نتیجه درست بدهد.' );
es_pending( 'SC-02: reset تعداد کل را برگرداند.' );

es_summary();
