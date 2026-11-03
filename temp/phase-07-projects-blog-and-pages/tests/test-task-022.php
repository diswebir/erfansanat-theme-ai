<?php
/**
 * P07-T022 — برگه مجوزها و افتخارات
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-022.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T022', 'برگه مجوزها و افتخارات' );

es_check( 'target: erfansanat/page-templates/licenses.php', es_file('page-templates/licenses.php', ES_THEME) );

es_pending( 'SC-01: افزودن گواهینامه جدید رندر شود.' );
es_pending( 'SC-02: کلیک تصویر بزرگ‌نمایی کند.' );

es_summary();
