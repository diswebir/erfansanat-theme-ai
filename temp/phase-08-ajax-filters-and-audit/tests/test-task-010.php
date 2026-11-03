<?php
/**
 * P08-T010 — فیلتر پروژه‌ها
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T010', 'فیلتر پروژه‌ها' );

es_check( 'target: erfansanat/inc/Core/SmartFilters.php', es_file('inc/Core/SmartFilters.php', ES_THEME) );
es_check( 'target: erfansanat/assets/js/project-filters.js', es_file('assets/js/project-filters.js', ES_THEME) );
es_check( 'target: erfansanat/template-parts/archive/project-filters.php', es_file('template-parts/archive/project-filters.php', ES_THEME) );

es_pending( 'SC-01: فیلتر شهر اصفهان فقط پروژه‌های همان شهر را بدهد.' );
es_pending( 'SC-02: شمارنده نوع پروژه درست باشد.' );

es_summary();
