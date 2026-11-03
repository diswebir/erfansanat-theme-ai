<?php
/**
 * P06-T011 — کاربردهای محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T011', 'کاربردهای محصول' );

es_check( 'target: erfansanat/template-parts/product/usage.php', es_file('template-parts/product/usage.php', ES_THEME) );

es_pending( 'SC-01: کاربرد متنی و ترمی هر دو رندر شوند.' );

es_summary();
