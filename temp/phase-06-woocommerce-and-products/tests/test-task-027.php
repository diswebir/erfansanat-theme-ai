<?php
/**
 * P06-T027 — صفحه auth/ورود پیامکی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-027.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T027', 'صفحه auth/ورود پیامکی' );

es_check( 'target: erfansanat/page-templates/auth.php', es_file('page-templates/auth.php', ES_THEME) );

es_pending( 'SC-01: ورود پیامکی آزمایشی shim موفق باشد.' );
es_pending( 'SC-02: redirect باز به دامنه خارجی نباشد.' );

es_summary();
