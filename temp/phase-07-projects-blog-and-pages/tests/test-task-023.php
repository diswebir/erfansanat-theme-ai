<?php
/**
 * P07-T023 — برگه سوالات متداول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-023.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T023', 'برگه سوالات متداول' );

es_check( 'target: erfansanat/page-templates/faq.php', es_file('page-templates/faq.php', ES_THEME) );

es_pending( 'SC-01: باز/بسته‌شدن سوال کار کند.' );
es_pending( 'SC-02: جستجوی سوال نتایج را فیلتر کند.' );

es_summary();
