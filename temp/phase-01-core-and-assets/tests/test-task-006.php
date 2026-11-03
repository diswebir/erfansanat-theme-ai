<?php
/**
 * P01-T006 — DeBloat: صف‌های بلوک
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T006', 'DeBloat: صف‌های بلوک' );

es_check( 'target: erfansanat/inc/Core/DeBloat.php', es_file('inc/Core/DeBloat.php', ES_THEME) );

es_pending( 'SC-01: سورس فرانت‌اند CSS/JS بلوک نداشته باشد.' );
es_pending( 'SC-02: صف admin دست‌نخورده بماند.' );

es_summary();
