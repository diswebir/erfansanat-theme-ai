<?php
/**
 * P07-T010 — آرشیو دسته/عمومی وبلاگ
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T010', 'آرشیو دسته/عمومی وبلاگ' );

es_check( 'target: erfansanat/category.php', es_file('category.php', ES_THEME) );
es_check( 'target: erfansanat/archive.php', es_file('archive.php', ES_THEME) );

es_pending( 'SC-01: دسته مجله علمی رندر شود.' );
es_pending( 'SC-02: آرشیو تاریخ/نویسنده fatal ندهد.' );

es_summary();
