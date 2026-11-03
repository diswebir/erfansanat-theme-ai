<?php
/**
 * P07-T011 — کارت مقاله و فیلتر کلاینت
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T011', 'کارت مقاله و فیلتر کلاینت' );

es_check( 'target: erfansanat/template-parts/cards/card-post.php', es_file('template-parts/cards/card-post.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/archive/blog-filter.php', es_file('template-parts/archive/blog-filter.php', ES_THEME) );

es_pending( 'SC-01: جستجوی عنوان مقاله کارت‌ها را فیلتر کند.' );
es_pending( 'SC-02: دسته مقاله درست لینک شود.' );

es_summary();
