<?php
/**
 * P06-T024 — فرم Checkout
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-024.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T024', 'فرم Checkout' );

es_check( 'target: erfansanat/woocommerce/checkout/form-checkout.php', es_file('woocommerce/checkout/form-checkout.php', ES_THEME) );

es_pending( 'SC-01: checkout با فیلد اجباری خالی خطا بدهد.' );
es_pending( 'SC-02: سفارش آزمایشی ثبت شود.' );

es_summary();
