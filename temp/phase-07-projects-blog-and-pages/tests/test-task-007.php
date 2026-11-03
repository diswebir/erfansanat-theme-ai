<?php
/**
 * P07-T007 — آرشیو نوع پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T007', 'آرشیو نوع پروژه' );

es_check( 'target: erfansanat/taxonomy-project_type.php', es_file('taxonomy-project_type.php', ES_THEME) );

es_pending( 'SC-01: /project_type/تونل-نوری/ یا معادل واقعی 200 بدهد.' );

es_summary();
