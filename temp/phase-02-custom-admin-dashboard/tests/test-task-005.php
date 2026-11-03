<?php
/**
 * P02-T005 — Renderer فیلدهای پایه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T005', 'Renderer فیلدهای پایه' );

es_check( 'target: erfansanat/inc/Admin/FieldRenderer.php', es_file('inc/Admin/FieldRenderer.php', ES_THEME) );

es_pending( 'SC-01: هر نوع فیلد مقدار قبلی را نشان دهد.' );
es_pending( 'SC-02: خروجی HTML معتبر باشد.' );

es_summary();
