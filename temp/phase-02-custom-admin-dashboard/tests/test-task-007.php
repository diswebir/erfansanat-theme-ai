<?php
/**
 * P02-T007 — فیلد Repeater
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T007', 'فیلد Repeater' );

es_check( 'target: erfansanat/inc/Admin/FieldRenderer.php', es_file('inc/Admin/FieldRenderer.php', ES_THEME) );
es_check( 'target: erfansanat/assets/admin/repeater.js', es_file('assets/admin/repeater.js', ES_THEME) );

es_pending( 'SC-01: سه آیتم ذخیره و دوباره خوانده شود.' );
es_pending( 'SC-02: حذف آیتم وسط داده بقیه را خراب نکند.' );

es_summary();
