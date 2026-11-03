<?php
/**
 * P01-T005 — Fallback index.php
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T005', 'Fallback index.php' );

es_check( 'target: erfansanat/index.php', es_file('index.php', ES_THEME) );

es_pending( 'SC-01: یک نوشته آزمایشی رندر شود.' );
es_pending( 'SC-02: آرشیو خالی fatal ندهد.' );

es_summary();
