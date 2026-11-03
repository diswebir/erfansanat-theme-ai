<?php
/**
 * P03-T005 — حفظ rewrite محصول و product_cat
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T005', 'حفظ rewrite محصول و product_cat' );

es_check( 'target: erfansanat/inc/Core/Rewrites.php', es_file('inc/Core/Rewrites.php', ES_THEME) );

es_pending( 'SC-01: لینک محصول و دسته موجود 200 بدهد.' );
es_pending( 'SC-02: ساختار دسته سلسله‌مراتبی حفظ شود.' );

es_summary();
