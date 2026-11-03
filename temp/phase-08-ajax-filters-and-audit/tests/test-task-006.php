<?php
/**
 * P08-T006 — UI فیلترهای آرشیو محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T006', 'UI فیلترهای آرشیو محصول' );

es_check( 'target: erfansanat/template-parts/archive/product-filters.php', es_file('template-parts/archive/product-filters.php', ES_THEME) );
es_check( 'target: erfansanat/assets/js/product-filters.js', es_file('assets/js/product-filters.js', ES_THEME) );

es_pending( 'SC-01: انتخاب دسته کارت‌ها را فیلتر کند.' );
es_pending( 'SC-02: دکمه حذف فیلترها حالت اول را برگرداند.' );

es_summary();
