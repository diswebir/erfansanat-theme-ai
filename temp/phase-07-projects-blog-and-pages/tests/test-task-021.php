<?php
/**
 * P07-T021 — برگه تماس با ما
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-021.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T021', 'برگه تماس با ما' );

es_check( 'target: erfansanat/page-templates/contact.php', es_file('page-templates/contact.php', ES_THEME) );

es_pending( 'SC-01: تغییر تلفن در صفحه دیده شود.' );
es_pending( 'SC-02: فرم بدون handler داده ذخیره نکند.' );

es_summary();
