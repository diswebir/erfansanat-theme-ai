<?php
/**
 * P07-T028 — لندینگ آریا
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-028.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T028', 'لندینگ آریا' );

es_check( 'target: erfansanat/page-templates/landing-arya.php', es_file('page-templates/landing-arya.php', ES_THEME) );

es_pending( 'SC-01: اسکرول منو به سکشن درست برود.' );
es_pending( 'SC-02: ویرایش متن هیرو دیده شود.' );

es_summary();
