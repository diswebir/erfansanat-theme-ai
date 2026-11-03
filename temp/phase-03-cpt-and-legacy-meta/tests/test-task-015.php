<?php
/**
 * P03-T015 — متاباکس کاتالوگ و گالری محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T015', 'متاباکس کاتالوگ و گالری محصول' );

es_check( 'target: erfansanat/inc/Admin/ProductMetaboxes.php', es_file('inc/Admin/ProductMetaboxes.php', ES_THEME) );

es_pending( 'SC-01: گالری legacy خوانده و بدون تخریب ذخیره شود.' );
es_pending( 'SC-02: حذف تصویر از گالری کلیدهای همگام را درست کند.' );

es_summary();
