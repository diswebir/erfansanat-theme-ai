<?php
/**
 * P04-T008 — مگامنوی شرکتی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T008', 'مگامنوی شرکتی' );

es_check( 'target: erfansanat/template-parts/header/mega-corporate.php', es_file('template-parts/header/mega-corporate.php', ES_THEME) );

es_pending( 'SC-01: لینک درباره‌ما صحیح باشد.' );
es_pending( 'SC-02: خالی‌بودن تصویر fallback داشته باشد.' );

es_summary();
