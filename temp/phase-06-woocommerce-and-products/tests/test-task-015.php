<?php
/**
 * P06-T015 — محصولات مرتبط/مکمل
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T015', 'محصولات مرتبط/مکمل' );

es_check( 'target: erfansanat/template-parts/product/related-products.php', es_file('template-parts/product/related-products.php', ES_THEME) );

es_pending( 'SC-01: چهار محصول مرتبط بدون محصول جاری رندر شوند.' );

es_summary();
