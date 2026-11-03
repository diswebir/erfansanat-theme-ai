<?php
/**
 * P03-T006 — حفظ product_tag/product_brand
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T006', 'حفظ product_tag/product_brand' );

es_check( 'target: erfansanat/inc/Core/Rewrites.php', es_file('inc/Core/Rewrites.php', ES_THEME) );

es_pending( 'SC-01: آرشیو برند/برچسب موجود 404 ندهد.' );

es_summary();
