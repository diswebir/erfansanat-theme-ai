<?php
/**
 * P02-T002 — Helper es_option و کش
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T002', 'Helper es_option و کش' );

es_check( 'target: erfansanat/inc/Helpers.php', es_file('inc/Helpers.php', ES_THEME) );

es_pending( 'SC-01: تغییر مقدار بلافاصله در همان request دیده شود.' );
es_pending( 'SC-02: کلید ناموجود default بدهد.' );

es_summary();
