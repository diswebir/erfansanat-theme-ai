<?php
/**
 * P08-T011 — کارایی/کش فیلتر و جستجو
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T011', 'کارایی/کش فیلتر و جستجو' );

es_check( 'target: erfansanat/inc/Core/SmartFilters.php', es_file('inc/Core/SmartFilters.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Core/AjaxSearch.php', es_file('inc/Core/AjaxSearch.php', ES_THEME) );

es_pending( 'SC-01: بنچمارک ۱۰۰/۱۰۰ آیتم ثبت شود.' );
es_pending( 'SC-02: انتشار/ویرایش پست کش را پاک کند.' );

es_summary();
