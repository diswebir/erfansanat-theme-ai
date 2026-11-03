<?php
/**
 * P07-T020 — برگه درباره ما
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-020.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T020', 'برگه درباره ما' );

es_check( 'target: erfansanat/page-templates/about.php', es_file('page-templates/about.php', ES_THEME) );

es_pending( 'SC-01: ویرایش متن درباره در خروجی دیده شود.' );
es_pending( 'SC-02: تصویر کارخانه جایگزین شود.' );

es_summary();
