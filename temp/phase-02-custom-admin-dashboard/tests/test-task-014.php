<?php
/**
 * P02-T014 — تب تنظیمات آرشیو
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T014', 'تب تنظیمات آرشیو' );

es_check( 'target: erfansanat/inc/Options/Tabs/ArchiveTab.php', es_file('inc/Options/Tabs/ArchiveTab.php', ES_THEME) );

es_pending( 'SC-01: تعداد کارت آرشیو عوض شود.' );
es_pending( 'SC-02: فیلتر پیش‌فرض اعمال شود.' );

es_summary();
