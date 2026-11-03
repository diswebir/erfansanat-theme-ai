<?php
/**
 * P04-T007 — مگامنوی پروژه‌ها
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T007', 'مگامنوی پروژه‌ها' );

es_check( 'target: erfansanat/template-parts/header/mega-projects.php', es_file('template-parts/header/mega-projects.php', ES_THEME) );

es_pending( 'SC-01: تب شهر/نوع عوض شود.' );
es_pending( 'SC-02: شمارنده با تعداد واقعی برابر باشد.' );

es_summary();
