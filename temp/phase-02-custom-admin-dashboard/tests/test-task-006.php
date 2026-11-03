<?php
/**
 * P02-T006 — فیلدهای Media/Link/Icon
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T006', 'فیلدهای Media/Link/Icon' );

es_check( 'target: erfansanat/inc/Admin/FieldRenderer.php', es_file('inc/Admin/FieldRenderer.php', ES_THEME) );
es_check( 'target: erfansanat/assets/admin/media-field.js', es_file('assets/admin/media-field.js', ES_THEME) );

es_pending( 'SC-01: انتخاب/حذف رسانه ذخیره شود.' );
es_pending( 'SC-02: javascript: در لینک رد شود.' );

es_summary();
