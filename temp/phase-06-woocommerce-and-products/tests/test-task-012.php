<?php
/**
 * P06-T012 — پروژه‌های مرتبط با محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T012', 'پروژه‌های مرتبط با محصول' );

es_check( 'target: erfansanat/template-parts/product/related-projects.php', es_file('template-parts/product/related-projects.php', ES_THEME) );

es_pending( 'SC-01: دو پروژه مرتبط نمایش داده شوند.' );
es_pending( 'SC-02: پروژه حذف‌شده رندر نشود.' );

es_summary();
