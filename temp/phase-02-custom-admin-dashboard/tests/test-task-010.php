<?php
/**
 * P02-T010 — تب هدر/اعلان/لوگو
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T010', 'تب هدر/اعلان/لوگو' );

es_check( 'target: erfansanat/inc/Options/Tabs/HeaderTab.php', es_file('inc/Options/Tabs/HeaderTab.php', ES_THEME) );

es_pending( 'SC-01: تغییر متن اعلان در فرانت‌اند دیده شود.' );
es_pending( 'SC-02: حذف لوگو مقدار پیش‌فرض را برگرداند.' );

es_summary();
