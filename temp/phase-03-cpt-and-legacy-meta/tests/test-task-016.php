<?php
/**
 * P03-T016 — متاباکس روابط محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T016', 'متاباکس روابط محصول' );

es_check( 'target: erfansanat/inc/Admin/ProductMetaboxes.php', es_file('inc/Admin/ProductMetaboxes.php', ES_THEME) );

es_pending( 'SC-01: رابط دوطرفه داده را خراب نکند.' );
es_pending( 'SC-02: ID حذف‌شده پاک‌سازی شود.' );

es_summary();
