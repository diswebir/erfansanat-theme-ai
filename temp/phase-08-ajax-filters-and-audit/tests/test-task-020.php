<?php
/**
 * P08-T020 — ممیزی کارایی و Escaping
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-020.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T020', 'ممیزی کارایی و Escaping' );

es_check( 'target: temp/phase-08-ajax-filters-and-audit/tests/test-task-020.php', es_file('phase-08-ajax-filters-and-audit/tests/test-task-020.php', ES_ROOT) );

es_pending( 'SC-01: اسکن escape/PHPCS پاس شود.' );
es_pending( 'SC-02: صفحات کلیدی بدون warning رندر شوند.' );

es_summary();
