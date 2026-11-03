<?php
/**
 * P07-T017 — دیدگاه‌های وبلاگ
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T017', 'دیدگاه‌های وبلاگ' );

es_check( 'target: erfansanat/comments.php', es_file('comments.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/post/comments.php', es_file('template-parts/post/comments.php', ES_THEME) );

es_pending( 'SC-01: ثبت دیدگاه آزمایشی و پاسخ مدیر کار کند.' );
es_pending( 'SC-02: دیدگاه بسته پیام درست بدهد.' );

es_summary();
