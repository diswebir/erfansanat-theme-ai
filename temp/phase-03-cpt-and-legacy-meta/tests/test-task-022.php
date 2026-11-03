<?php
/**
 * P03-T022 — متای دسته محصول: thumbnail و FAQ
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-022.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T022', 'متای دسته محصول: thumbnail و FAQ' );

es_check( 'target: erfansanat/inc/Admin/TermMetaboxes.php', es_file('inc/Admin/TermMetaboxes.php', ES_THEME) );

es_pending( 'SC-01: ذخیره روی ترم فارسی کار کند.' );
es_pending( 'SC-02: FAQ خالی رندر نشود.' );

es_summary();
