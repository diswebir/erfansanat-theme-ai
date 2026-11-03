<?php
/**
 * P06-T004 — خلاصه عنوان/زیرعنوان/عنوان انگلیسی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T004', 'خلاصه عنوان/زیرعنوان/عنوان انگلیسی' );

es_check( 'target: erfansanat/template-parts/product/summary.php', es_file('template-parts/product/summary.php', ES_THEME) );

es_pending( 'SC-01: محصول با/بدون زیرعنوان درست رندر شود.' );

es_summary();
