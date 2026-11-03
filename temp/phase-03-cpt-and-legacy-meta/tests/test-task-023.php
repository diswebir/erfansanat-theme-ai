<?php
/**
 * P03-T023 — متاباکس Hreflang پست و ترم
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-023.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T023', 'متاباکس Hreflang پست و ترم' );

es_check( 'target: erfansanat/inc/Admin/HreflangMetabox.php', es_file('inc/Admin/HreflangMetabox.php', ES_THEME) );

es_pending( 'SC-01: مقدار روی post و term round-trip شود.' );
es_pending( 'SC-02: مرتب‌سازی ستون کار کند.' );

es_summary();
