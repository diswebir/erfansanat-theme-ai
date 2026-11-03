<?php
/**
 * P07-T026 — برگه خدمات smartelement
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-026.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T026', 'برگه خدمات smartelement' );

es_check( 'target: erfansanat/page-templates/services.php', es_file('page-templates/services.php', ES_THEME) );

es_pending( 'SC-01: پنج خدمت در smartelement لیست شوند.' );
es_pending( 'SC-02: لینک تک‌خدمت درست باشد.' );

es_summary();
