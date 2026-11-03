<?php
/**
 * P02-T018 — SectionManager: API رندر و کش
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T018', 'SectionManager: API رندر و کش' );

es_check( 'target: erfansanat/inc/Sections/Renderer.php', es_file('inc/Sections/Renderer.php', ES_THEME) );

es_pending( 'SC-01: ترتیب خروجی با متا برابر باشد.' );
es_pending( 'SC-02: سکشن ناشناخته نادیده گرفته شود.' );

es_summary();
