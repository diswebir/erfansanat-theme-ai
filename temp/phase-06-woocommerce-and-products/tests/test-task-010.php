<?php
/**
 * P06-T010 — جدول ۱۸ ویژگی فنی pa_*
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T010', 'جدول ۱۸ ویژگی فنی pa_*' );

es_check( 'target: erfansanat/template-parts/product/specs-table.php', es_file('template-parts/product/specs-table.php', ES_THEME) );

es_pending( 'SC-01: محصول با همه ویژگی‌ها جدول کامل بدهد.' );
es_pending( 'SC-02: ویژگی خالی ردیف نسازد.' );

es_summary();
