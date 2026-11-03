<?php
/**
 * P08-T008 — فیلتر ۱۸ ویژگی pa_*
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T008', 'فیلتر ۱۸ ویژگی pa_*' );

es_check( 'target: erfansanat/inc/Core/SmartFilters.php', es_file('inc/Core/SmartFilters.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/archive/product-filters.php', es_file('template-parts/archive/product-filters.php', ES_THEME) );

es_pending( 'SC-01: فیلتر IP/ولتاژ/رنگ نتیجه درست بدهد.' );
es_pending( 'SC-02: مقدار ناموجود رد شود.' );

es_summary();
