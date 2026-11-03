<?php
/**
 * P03-T009 — ریدایرکت aliasهای محصول/دسته
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T009', 'ریدایرکت aliasهای محصول/دسته' );

es_check( 'target: erfansanat/inc/Core/LegacyRedirects.php', es_file('inc/Core/LegacyRedirects.php', ES_THEME) );

es_pending( 'SC-01: هر alias یک 301 و مقصد 200 بدهد.' );
es_pending( 'SC-02: query string حفظ شود.' );
es_pending( 'SC-03: حلقه ریدایرکت ایجاد نشود.' );

es_summary();
