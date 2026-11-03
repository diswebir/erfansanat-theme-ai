<?php
/**
 * P05-T004 — کارت استاندارد مقاله
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T004', 'کارت استاندارد مقاله' );

es_check( 'target: erfansanat/template-parts/cards/card-post.php', es_file('template-parts/cards/card-post.php', ES_THEME) );

es_pending( 'SC-01: مقاله فارسی درست رندر شود.' );
es_pending( 'SC-02: تصویر شاخص نبود fallback داشته باشد.' );

es_summary();
