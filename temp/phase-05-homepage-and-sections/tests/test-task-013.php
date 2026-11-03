<?php
/**
 * P05-T013 — سکشن محصولات منتخب
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T013', 'سکشن محصولات منتخب' );

es_check( 'target: erfansanat/template-parts/home/section-products.php', es_file('template-parts/home/section-products.php', ES_THEME) );

es_pending( 'SC-01: تعداد/دسته کارت‌ها با تنظیم برابر باشد.' );
es_pending( 'SC-02: کارت محصول data-attr کامل داشته باشد.' );

es_summary();
