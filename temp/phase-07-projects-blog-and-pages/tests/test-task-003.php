<?php
/**
 * P07-T003 — متادیتای تک‌پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T003', 'متادیتای تک‌پروژه' );

es_check( 'target: erfansanat/template-parts/project/meta.php', es_file('template-parts/project/meta.php', ES_THEME) );

es_pending( 'SC-01: تاریخ 1404/01/01 درست نمایش داده شود.' );
es_pending( 'SC-02: شهر فارسی لینک صحیح بگیرد.' );

es_summary();
