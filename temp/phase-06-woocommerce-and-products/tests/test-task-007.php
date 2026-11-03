<?php
/**
 * P06-T007 — Variation Swatches و Quantity
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T007', 'Variation Swatches و Quantity' );

es_check( 'target: erfansanat/woocommerce/single-product/variable.php', es_file('woocommerce/single-product/variable.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/product/purchase.php', es_file('template-parts/product/purchase.php', ES_THEME) );

es_pending( 'SC-01: محصول متغیر با سواچ خریداری شود.' );
es_pending( 'SC-02: تعداد نامعتبر رد شود.' );

es_summary();
