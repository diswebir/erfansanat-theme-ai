<?php
/**
 * P07-T013 — TOC تک‌نوشته
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T013', 'TOC تک‌نوشته' );

es_check( 'target: erfansanat/template-parts/post/toc.php', es_file('template-parts/post/toc.php', ES_THEME) );

es_pending( 'SC-01: کلیک آیتم TOC به هدینگ برود.' );
es_pending( 'SC-02: مقاله بدون هدینگ TOC خالی ندهد.' );

es_summary();
