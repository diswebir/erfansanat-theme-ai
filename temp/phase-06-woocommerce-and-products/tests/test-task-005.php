<?php
/**
 * P06-T005 — شناسه‌های قانونی محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T005', 'شناسه‌های قانونی محصول' );

es_check( 'target: erfansanat/template-parts/product/legal-ids.php', es_file('template-parts/product/legal-ids.php', ES_THEME) );

es_pending( 'SC-01: محصول با همه شناسه‌ها جدول درست بدهد.' );
es_pending( 'SC-02: محصول بدون شناسه سکشن خالی ندهد.' );

es_summary();
