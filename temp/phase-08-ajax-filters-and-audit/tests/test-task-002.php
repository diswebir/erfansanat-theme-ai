<?php
/**
 * P08-T002 — جستجوی زنده محصولات
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T002', 'جستجوی زنده محصولات' );

es_check( 'target: erfansanat/inc/Core/AjaxSearch.php', es_file('inc/Core/AjaxSearch.php', ES_THEME) );

es_pending( 'SC-01: عنوان فارسی نتیجه بدهد.' );
es_pending( 'SC-02: محصول پیش‌نویس برنگردد.' );
es_pending( 'SC-03: پاسخ زیر بودجه 200ms در QA باشد.' );

es_summary();
