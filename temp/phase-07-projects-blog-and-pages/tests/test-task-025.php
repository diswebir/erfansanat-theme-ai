<?php
/**
 * P07-T025 — برگه استخدام
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-025.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T025', 'برگه استخدام' );

es_check( 'target: erfansanat/page-templates/recruitment.php', es_file('page-templates/recruitment.php', ES_THEME) );

es_pending( 'SC-01: سوئیچ تب شغلی کار کند.' );
es_pending( 'SC-02: شرح شغل از تنظیمات بیاید.' );

es_summary();
