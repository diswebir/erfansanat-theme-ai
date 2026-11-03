<?php
/**
 * P06-T013 — FAQ محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T013', 'FAQ محصول' );

es_check( 'target: erfansanat/template-parts/product/faq.php', es_file('template-parts/product/faq.php', ES_THEME) );

es_pending( 'SC-01: باز/بسته‌شدن سوال کار کند.' );
es_pending( 'SC-02: پاسخ HTML مجاز escape کنترل‌شده داشته باشد.' );

es_summary();
