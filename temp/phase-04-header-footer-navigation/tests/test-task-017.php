<?php
/**
 * P04-T017 — Hreflang در head
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T017', 'Hreflang در head' );

es_check( 'target: erfansanat/inc/Core/Hreflang.php', es_file('inc/Core/Hreflang.php', ES_THEME) );

es_pending( 'SC-01: سه زبان fa/en/ar خروجی درست بدهند.' );
es_pending( 'SC-02: مقدار خالی تگ تولید نکند.' );

es_summary();
