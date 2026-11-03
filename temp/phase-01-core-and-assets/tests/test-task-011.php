<?php
/**
 * P01-T011 — AssetManager: enqueue شرطی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T011', 'AssetManager: enqueue شرطی' );

es_check( 'target: erfansanat/inc/Core/AssetManager.php', es_file('inc/Core/AssetManager.php', ES_THEME) );

es_pending( 'SC-01: صفحه اصلی فقط assetهای home را بگیرد.' );
es_pending( 'SC-02: single product asset محصول را بگیرد.' );
es_pending( 'SC-03: checkout اسکریپت Woo را از دست ندهد.' );

es_summary();
