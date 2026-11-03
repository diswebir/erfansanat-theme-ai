<?php
/**
 * P03-T010 — ریدایرکت project و aliasهای پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T010', 'ریدایرکت project و aliasهای پروژه' );

es_check( 'target: erfansanat/inc/Core/LegacyRedirects.php', es_file('inc/Core/LegacyRedirects.php', ES_THEME) );

es_pending( 'SC-01: /project/ یک 301 به /projects/ بدهد.' );
es_pending( 'SC-02: alias شهر فارسی درست resolve شود.' );

es_summary();
