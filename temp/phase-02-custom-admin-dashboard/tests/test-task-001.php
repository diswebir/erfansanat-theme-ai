<?php
/**
 * P02-T001 — Schema و ذخیره‌گاه آپشن‌ها
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T001', 'Schema و ذخیره‌گاه آپشن‌ها' );

es_check( 'target: erfansanat/inc/Options/Schema.php', es_file('inc/Options/Schema.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Options/Store.php', es_file('inc/Options/Store.php', ES_THEME) );

es_pending( 'SC-01: خواندن/نوشتن/حذف یک کلید round-trip شود.' );
es_pending( 'SC-02: آپشن خراب به پیش‌فرض امن fallback کند.' );

es_summary();
