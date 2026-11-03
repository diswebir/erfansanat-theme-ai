<?php
/**
 * P06-T008 — اطلاعیه قبل از خرید و کاتالوگ
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T008', 'اطلاعیه قبل از خرید و کاتالوگ' );

es_check( 'target: erfansanat/template-parts/product/notices.php', es_file('template-parts/product/notices.php', ES_THEME) );

es_pending( 'SC-01: اطلاعیه و فایل کاتالوگ نمایش داده شوند.' );
es_pending( 'SC-02: حذف فایل لینک را حذف کند.' );

es_summary();
