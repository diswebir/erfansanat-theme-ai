<?php
/**
 * P03-T001 — ثبت CPT projects
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T001', 'ثبت CPT projects' );

es_check( 'target: erfansanat/inc/Core/PostTypes.php', es_file('inc/Core/PostTypes.php', ES_THEME) );

es_pending( 'SC-01: یک پروژه ساخته و permalink آن /projects/slug/ باشد.' );
es_pending( 'SC-02: آرشیو projects فعال باشد.' );

es_summary();
