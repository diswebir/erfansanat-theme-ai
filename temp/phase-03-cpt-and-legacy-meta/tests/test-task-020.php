<?php
/**
 * P03-T020 — متاباکس گالری پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-020.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T020', 'متاباکس گالری پروژه' );

es_check( 'target: erfansanat/inc/Admin/ProjectMetaboxes.php', es_file('inc/Admin/ProjectMetaboxes.php', ES_THEME) );

es_pending( 'SC-01: تصویر چهارم در foure-image ذخیره شود.' );
es_pending( 'SC-02: حذف/جابجایی گالری همگام بماند.' );

es_summary();
