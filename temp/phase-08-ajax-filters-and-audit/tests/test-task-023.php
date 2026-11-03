<?php
/**
 * P08-T023 — ترتیب غیرفعال‌سازی افزونه‌ها و Rollback
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-023.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T023', 'ترتیب غیرفعال‌سازی افزونه‌ها و Rollback' );

es_check( 'target: temp/DEACTIVATION_RUNBOOK.md', es_file('DEACTIVATION_RUNBOOK.md', ES_ROOT) );

es_pending( 'SC-01: سناریوی rollback روی استیجینگ تست شود.' );
es_pending( 'SC-02: پس از خاموش‌کردن Jet* صفحات کلیدی 200 بدهند.' );

es_summary();
