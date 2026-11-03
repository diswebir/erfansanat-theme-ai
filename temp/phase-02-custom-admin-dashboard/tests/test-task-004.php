<?php
/**
 * P02-T004 — پوسته UI تب‌بندی RTL
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T004', 'پوسته UI تب‌بندی RTL' );

es_check( 'target: erfansanat/inc/Admin/ThemeOptions.php', es_file('inc/Admin/ThemeOptions.php', ES_THEME) );
es_check( 'target: erfansanat/assets/admin/options.css', es_file('assets/admin/options.css', ES_THEME) );
es_check( 'target: erfansanat/assets/admin/options.js', es_file('assets/admin/options.js', ES_THEME) );

es_pending( 'SC-01: جابجایی تب بدون reload.' );
es_pending( 'SC-02: focus trap/aria-selected درست باشد.' );

es_summary();
