<?php
/**
 * P06-T018 — آرشیو دسته product_cat
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T018', 'آرشیو دسته product_cat' );

es_check( 'target: erfansanat/woocommerce/taxonomy-product_cat.php', es_file('woocommerce/taxonomy-product_cat.php', ES_THEME) );

es_pending( 'SC-01: یک دسته با FAQ رندر شود.' );
es_pending( 'SC-02: زیردسته 404 ندهد.' );

es_summary();
