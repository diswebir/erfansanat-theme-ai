<?php
/**
 * P06-T025 — رسید/پیش‌فاکتور Checkout
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-025.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T025', 'رسید/پیش‌فاکتور Checkout' );

es_check( 'target: erfansanat/woocommerce/checkout/thankyou.php', es_file('woocommerce/checkout/thankyou.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/checkout/proforma.php', es_file('template-parts/checkout/proforma.php', ES_THEME) );

es_pending( 'SC-01: صاحب سفارش رسید را ببیند.' );
es_pending( 'SC-02: کاربر دیگر دسترسی نگیرد.' );

es_summary();
