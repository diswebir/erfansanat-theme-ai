<?php
/**
 * P03-T024 — ذخیره‌گاه شمارنده بازدید
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-024.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T024', 'ذخیره‌گاه شمارنده بازدید' );

es_check( 'target: erfansanat/inc/Core/ViewCounter.php', es_file('inc/Core/ViewCounter.php', ES_THEME) );

es_pending( 'SC-01: fallback درست خوانده شود.' );
es_pending( 'SC-02: دو افزایش هم‌زمان مقدار را گم نکند.' );

es_summary();
