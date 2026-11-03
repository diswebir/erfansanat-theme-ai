<?php
/**
 * P06-T021 — Toolbar مرتب‌سازی و صفحه‌بندی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-021.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T021', 'Toolbar مرتب‌سازی و صفحه‌بندی' );

es_check( 'target: erfansanat/template-parts/archive/toolbar.php', es_file('template-parts/archive/toolbar.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/archive/pagination.php', es_file('template-parts/archive/pagination.php', ES_THEME) );

es_pending( 'SC-01: صفحه دوم آرشیو کارت درست بدهد.' );
es_pending( 'SC-02: مرتب‌سازی نزولی قیمت کار کند.' );

es_summary();
