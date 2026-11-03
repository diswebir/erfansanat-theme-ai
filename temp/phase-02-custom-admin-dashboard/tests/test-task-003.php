<?php
/**
 * P02-T003 — منوی ادمین و دسترسی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T003', 'منوی ادمین و دسترسی' );

es_check( 'target: erfansanat/inc/Admin/ThemeOptions.php', es_file('inc/Admin/ThemeOptions.php', ES_THEME) );

es_pending( 'SC-01: admin مجاز صفحه را ببیند.' );
es_pending( 'SC-02: subscriber پاسخ 403 بگیرد.' );

es_summary();
