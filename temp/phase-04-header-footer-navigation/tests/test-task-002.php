<?php
/**
 * P04-T002 — Shell هدر و head استاندارد
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T002', 'Shell هدر و head استاندارد' );

es_check( 'target: erfansanat/header.php', es_file('header.php', ES_THEME) );

es_pending( 'SC-01: سورس هدر با مرجع DOM parity داشته باشد.' );
es_pending( 'SC-02: body_class خروجی درست بدهد.' );

es_summary();
