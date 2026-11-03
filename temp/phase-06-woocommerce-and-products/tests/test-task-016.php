<?php
/**
 * P06-T016 — Shell آرشیو فروشگاه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T016', 'Shell آرشیو فروشگاه' );

es_check( 'target: erfansanat/woocommerce/archive-product.php', es_file('woocommerce/archive-product.php', ES_THEME) );

es_pending( 'SC-01: /shop/ کارت‌ها را رندر کند.' );
es_pending( 'SC-02: آرشیو خالی fatal ندهد.' );

es_summary();
