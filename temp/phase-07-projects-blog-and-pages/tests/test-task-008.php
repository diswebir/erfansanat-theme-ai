<?php
/**
 * P07-T008 — آرشیو شهر
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T008', 'آرشیو شهر' );

es_check( 'target: erfansanat/taxonomy-city.php', es_file('taxonomy-city.php', ES_THEME) );

es_pending( 'SC-01: ترم فارسی اصفهان رندر شود.' );
es_pending( 'SC-02: شهر بدون پروژه پیام خالی بدهد.' );

es_summary();
