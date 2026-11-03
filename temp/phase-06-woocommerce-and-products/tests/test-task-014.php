<?php
/**
 * P06-T014 — دیدگاه‌ها و امتیاز محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T014', 'دیدگاه‌ها و امتیاز محصول' );

es_check( 'target: erfansanat/woocommerce/single-product/review.php', es_file('woocommerce/single-product/review.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/product/reviews.php', es_file('template-parts/product/reviews.php', ES_THEME) );

es_pending( 'SC-01: ثبت دیدگاه آزمایشی کار کند.' );
es_pending( 'SC-02: میانگین امتیاز به‌روز شود.' );

es_summary();
