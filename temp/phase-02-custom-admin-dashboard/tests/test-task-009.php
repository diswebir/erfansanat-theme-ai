<?php
/**
 * P02-T009 — AJAX Save: sanitize و validation
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T009', 'AJAX Save: sanitize و validation' );

es_check( 'target: erfansanat/inc/Options/Sanitizer.php', es_file('inc/Options/Sanitizer.php', ES_THEME) );

es_pending( 'SC-01: رنگ/ایمیل/URL نامعتبر رد شود.' );
es_pending( 'SC-02: متن فارسی و ایموجی سالم بماند.' );

es_summary();
