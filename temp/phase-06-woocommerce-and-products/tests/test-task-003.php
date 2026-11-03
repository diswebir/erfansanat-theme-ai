<?php
/**
 * P06-T003 — گالری محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T003', 'گالری محصول' );

es_check( 'target: erfansanat/template-parts/product/gallery.php', es_file('template-parts/product/gallery.php', ES_THEME) );

es_pending( 'SC-01: گالری legacy درست نمایش داده شود.' );
es_pending( 'SC-02: کلیک تصویر لایت‌باکس باز کند.' );

es_summary();
