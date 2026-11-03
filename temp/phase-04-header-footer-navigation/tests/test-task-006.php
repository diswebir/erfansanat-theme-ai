<?php
/**
 * P04-T006 — مگامنوی محصولات
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T006', 'مگامنوی محصولات' );

es_check( 'target: erfansanat/template-parts/header/mega-products.php', es_file('template-parts/header/mega-products.php', ES_THEME) );

es_pending( 'SC-01: تب‌ها با JS مرجع کار کنند.' );
es_pending( 'SC-02: ترم فارسی لینک درست بگیرد.' );

es_summary();
