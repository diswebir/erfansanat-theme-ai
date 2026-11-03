<?php
/**
 * P05-T015 — سکشن خدمات تخصصی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T015', 'سکشن خدمات تخصصی' );

es_check( 'target: erfansanat/template-parts/home/section-services.php', es_file('template-parts/home/section-services.php', ES_THEME) );

es_pending( 'SC-01: پنج خدمت تب درست بسازند.' );
es_pending( 'SC-02: کلیک تب محتوا را عوض کند.' );

es_summary();
