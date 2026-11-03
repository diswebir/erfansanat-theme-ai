<?php
/**
 * P07-T014 — اشتراک‌گذاری تک‌نوشته
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T014', 'اشتراک‌گذاری تک‌نوشته' );

es_check( 'target: erfansanat/template-parts/post/share.php', es_file('template-parts/post/share.php', ES_THEME) );

es_pending( 'SC-01: کپی لینک در مرورگر کار کند.' );
es_pending( 'SC-02: لینک شبکه اجتماعی درست ساخته شود.' );

es_summary();
