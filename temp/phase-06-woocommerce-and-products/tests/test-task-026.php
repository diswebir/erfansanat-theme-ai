<?php
/**
 * P06-T026 — حساب کاربری Woo
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-026.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T026', 'حساب کاربری Woo' );

es_check( 'target: erfansanat/woocommerce/myaccount/my-account.php', es_file('woocommerce/myaccount/my-account.php', ES_THEME) );
es_check( 'target: erfansanat/woocommerce/myaccount/dashboard.php', es_file('woocommerce/myaccount/dashboard.php', ES_THEME) );

es_pending( 'SC-01: ورود کاربر و مشاهده سفارش‌ها کار کند.' );
es_pending( 'SC-02: آدرس پیش‌فرض ذخیره شود.' );

es_summary();
