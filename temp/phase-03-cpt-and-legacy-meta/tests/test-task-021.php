<?php
/**
 * P03-T021 — متاباکس تاریخ و محصولات پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-021.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T021', 'متاباکس تاریخ و محصولات پروژه' );

es_check( 'target: erfansanat/inc/Admin/ProjectMetaboxes.php', es_file('inc/Admin/ProjectMetaboxes.php', ES_THEME) );

es_pending( 'SC-01: تاریخ 1404/01/01 round-trip شود.' );
es_pending( 'SC-02: دو محصول مرتبط ذخیره شوند.' );

es_summary();
