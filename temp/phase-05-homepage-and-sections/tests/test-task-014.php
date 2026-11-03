<?php
/**
 * P05-T014 — CTA کاتالوگ جامع
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T014', 'CTA کاتالوگ جامع' );

es_check( 'target: erfansanat/template-parts/home/section-catalog.php', es_file('template-parts/home/section-catalog.php', ES_THEME) );

es_pending( 'SC-01: فایل انتخابی لینک درست بگیرد.' );
es_pending( 'SC-02: حذف فایل دکمه را حذف کند.' );

es_summary();
