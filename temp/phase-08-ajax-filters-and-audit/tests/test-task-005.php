<?php
/**
 * P08-T005 — QueryBuilder فیلتر محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T005', 'QueryBuilder فیلتر محصول' );

es_check( 'target: erfansanat/inc/Core/SmartFilters.php', es_file('inc/Core/SmartFilters.php', ES_THEME) );

es_pending( 'SC-01: فیلتر ترکیبی دسته+قیمت+ویژگی نتیجه درست بدهد.' );
es_pending( 'SC-02: کلید ناشناخته رد شود.' );

es_summary();
