<?php
/**
 * P03-T008 — Flush و رگرسیون Permalink
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T008', 'Flush و رگرسیون Permalink' );

es_check( 'target: erfansanat/inc/Core/Rewrites.php', es_file('inc/Core/Rewrites.php', ES_THEME) );

es_pending( 'SC-01: ماتریس URL مرجع 200/301 مورد انتظار بدهد.' );
es_pending( 'SC-02: بازفعال‌سازی قالب قوانین را بازسازی کند.' );

es_summary();
