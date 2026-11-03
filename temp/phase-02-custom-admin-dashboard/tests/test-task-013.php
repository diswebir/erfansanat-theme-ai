<?php
/**
 * P02-T013 — تب برند/رنگ و اسکریپت سفارشی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T013', 'تب برند/رنگ و اسکریپت سفارشی' );

es_check( 'target: erfansanat/inc/Options/Tabs/BrandTab.php', es_file('inc/Options/Tabs/BrandTab.php', ES_THEME) );

es_pending( 'SC-01: رنگ نامعتبر رد شود.' );
es_pending( 'SC-02: کد سفارش‌داده‌شده فقط در محل مجاز چاپ شود.' );

es_summary();
