<?php
/**
 * P03-T002 — ثبت CPT services
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T002', 'ثبت CPT services' );

es_check( 'target: erfansanat/inc/Core/PostTypes.php', es_file('inc/Core/PostTypes.php', ES_THEME) );

es_pending( 'SC-01: تک‌خدمت /services/slug/ بدهد.' );
es_pending( 'SC-02: آرشیو مستقل 404/redirect طبق طراحی باشد.' );

es_summary();
