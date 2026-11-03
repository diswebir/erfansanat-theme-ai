<?php
/**
 * P08-T003 — جستجوی زنده پروژه‌ها و مقالات
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T003', 'جستجوی زنده پروژه‌ها و مقالات' );

es_check( 'target: erfansanat/inc/Core/AjaxSearch.php', es_file('inc/Core/AjaxSearch.php', ES_THEME) );

es_pending( 'SC-01: جستجوی نام پروژه/مقاله نتیجه درست بدهد.' );
es_pending( 'SC-02: ترم فارسی در جستجو کار کند.' );

es_summary();
