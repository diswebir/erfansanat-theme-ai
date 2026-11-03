<?php
/**
 * P05-T008 — سکشن انواع پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T008', 'سکشن انواع پروژه' );

es_check( 'target: erfansanat/template-parts/home/section-project-types.php', es_file('template-parts/home/section-project-types.php', ES_THEME) );

es_pending( 'SC-01: کلیک کارت به آرشیو نوع برود.' );
es_pending( 'SC-02: ترم فارسی درست لینک شود.' );

es_summary();
