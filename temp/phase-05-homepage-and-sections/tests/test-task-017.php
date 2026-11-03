<?php
/**
 * P05-T017 — سکشن پشتیبانی و گارانتی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T017', 'سکشن پشتیبانی و گارانتی' );

es_check( 'target: erfansanat/template-parts/home/section-support.php', es_file('template-parts/home/section-support.php', ES_THEME) );

es_pending( 'SC-01: تغییر متن پشتیبانی دیده شود.' );
es_pending( 'SC-02: دکمه تماس درست کار کند.' );

es_summary();
