<?php
/**
 * P08-T017 — پنل ورودی‌های فرم
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T017', 'پنل ورودی‌های فرم' );

es_check( 'target: erfansanat/inc/Admin/FormEntries.php', es_file('inc/Admin/FormEntries.php', ES_THEME) );

es_pending( 'SC-01: ادمین ورودی را read/unread کند.' );
es_pending( 'SC-02: subscriber دسترسی نگیرد.' );

es_summary();
