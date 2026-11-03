<?php
/**
 * P02-T008 — AJAX Save: امنیت
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T008', 'AJAX Save: امنیت' );

es_check( 'target: erfansanat/inc/Admin/ThemeOptions.php', es_file('inc/Admin/ThemeOptions.php', ES_THEME) );

es_pending( 'SC-01: nonce نامعتبر 403 بدهد.' );
es_pending( 'SC-02: کاربر غیرمجاز 403 بدهد.' );
es_pending( 'SC-03: ذخیره معتبر 200 بدهد.' );

es_summary();
