<?php
/**
 * P07-T006 — آرشیو پروژه‌ها
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T006', 'آرشیو پروژه‌ها' );

es_check( 'target: erfansanat/archive-projects.php', es_file('archive-projects.php', ES_THEME) );

es_pending( 'SC-01: /projects/ کارت‌ها را رندر کند.' );
es_pending( 'SC-02: صفحه دوم آرشیو کار کند.' );

es_summary();
