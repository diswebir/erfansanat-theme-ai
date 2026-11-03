<?php
/**
 * P06-T002 — Shell تک‌محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T002', 'Shell تک‌محصول' );

es_check( 'target: erfansanat/woocommerce/single-product.php', es_file('woocommerce/single-product.php', ES_THEME) );
es_check( 'target: erfansanat/woocommerce/content-single-product.php', es_file('woocommerce/content-single-product.php', ES_THEME) );

es_pending( 'SC-01: یک محصول ساده رندر شود.' );
es_pending( 'SC-02: DOM سکشن‌های اصلی با مرجع parity داشته باشد.' );

es_summary();
