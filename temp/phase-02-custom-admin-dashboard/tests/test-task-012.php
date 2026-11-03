<?php
/**
 * P02-T012 — تب فوتر/تماس/شبکه‌های اجتماعی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T012', 'تب فوتر/تماس/شبکه‌های اجتماعی' );

es_check( 'target: erfansanat/inc/Options/Tabs/FooterTab.php', es_file('inc/Options/Tabs/FooterTab.php', ES_THEME) );

es_pending( 'SC-01: تغییر تلفن در فوتر/CTA دیده شود.' );
es_pending( 'SC-02: لینک خالی رندر نشود.' );

es_summary();
