<?php
/**
 * P07-T001 — Shell تک‌پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T001', 'Shell تک‌پروژه' );

es_check( 'target: erfansanat/single-projects.php', es_file('single-projects.php', ES_THEME) );

es_pending( 'SC-01: یک پروژه واقعی رندر شود.' );
es_pending( 'SC-02: DOM سکشن‌های اصلی parity داشته باشد.' );

es_summary();
