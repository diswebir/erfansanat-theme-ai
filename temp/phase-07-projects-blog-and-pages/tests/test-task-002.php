<?php
/**
 * P07-T002 — گالری تک‌پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T002', 'گالری تک‌پروژه' );

es_check( 'target: erfansanat/template-parts/project/gallery.php', es_file('template-parts/project/gallery.php', ES_THEME) );

es_pending( 'SC-01: پروژه با ۶ تصویر گالری کامل بدهد.' );
es_pending( 'SC-02: لایت‌باکس کار کند.' );

es_summary();
