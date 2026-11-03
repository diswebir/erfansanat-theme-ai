<?php
/**
 * P03-T014 — متاباکس عنوان/وضعیت فروش محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T014', 'متاباکس عنوان/وضعیت فروش محصول' );

es_check( 'target: erfansanat/inc/Admin/ProductMetaboxes.php', es_file('inc/Admin/ProductMetaboxes.php', ES_THEME) );

es_pending( 'SC-01: محصول استعلامی و آنلاین درست ذخیره شوند.' );
es_pending( 'SC-02: مقدار نامعتبر true_sale رد شود.' );

es_summary();
