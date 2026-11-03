<?php
/**
 * P05-T018 — سکشن مشتریان و همکاران
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T018', 'سکشن مشتریان و همکاران' );

es_check( 'target: erfansanat/template-parts/home/section-clients.php', es_file('template-parts/home/section-clients.php', ES_THEME) );

es_pending( 'SC-01: لوگوی جدید اضافه شود.' );
es_pending( 'SC-02: مشتری بدون لینک فقط تصویر باشد.' );

es_summary();
