<?php
/**
 * P03-T004 — ثبت تکسونومی city
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T004', 'ثبت تکسونومی city' );

es_check( 'target: erfansanat/inc/Core/Taxonomies.php', es_file('inc/Core/Taxonomies.php', ES_THEME) );

es_pending( 'SC-01: /city/اصفهان/ resolve شود.' );
es_pending( 'SC-02: تغییر نام ترم لینک را خراب نکند.' );

es_summary();
