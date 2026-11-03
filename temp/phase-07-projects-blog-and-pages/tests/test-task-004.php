<?php
/**
 * P07-T004 — محصولات استفاده‌شده در پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T004', 'محصولات استفاده‌شده در پروژه' );

es_check( 'target: erfansanat/template-parts/project/used-products.php', es_file('template-parts/project/used-products.php', ES_THEME) );

es_pending( 'SC-01: دو محصول استفاده‌شده نمایش داده شوند.' );
es_pending( 'SC-02: محصول حذف‌شده رندر نشود.' );

es_summary();
