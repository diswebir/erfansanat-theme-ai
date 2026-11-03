<?php
/**
 * P06-T022 — قالب سبد خرید
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-022.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T022', 'قالب سبد خرید' );

es_check( 'target: erfansanat/woocommerce/cart/cart.php', es_file('woocommerce/cart/cart.php', ES_THEME) );

es_pending( 'SC-01: افزودن/حذف/تغییر تعداد جمع را درست کند.' );
es_pending( 'SC-02: کوپن نامعتبر پیام درست بدهد.' );

es_summary();
