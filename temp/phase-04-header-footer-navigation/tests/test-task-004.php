<?php
/**
 * P04-T004 — ردیف برند و اکشن‌های هدر
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T004', 'ردیف برند و اکشن‌های هدر' );

es_check( 'target: erfansanat/template-parts/header/brand-row.php', es_file('template-parts/header/brand-row.php', ES_THEME) );

es_pending( 'SC-01: لوگوی سفارشی رندر شود.' );
es_pending( 'SC-02: شمارنده سبد با تعداد آیتم برابر باشد.' );

es_summary();
