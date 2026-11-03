<?php
/**
 * P06-T009 — مشخصات و ویژگی‌های کلیدی محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T009', 'مشخصات و ویژگی‌های کلیدی محصول' );

es_check( 'target: erfansanat/template-parts/product/features.php', es_file('template-parts/product/features.php', ES_THEME) );

es_pending( 'SC-01: دو repeater با داده legacy رندر شوند.' );

es_summary();
