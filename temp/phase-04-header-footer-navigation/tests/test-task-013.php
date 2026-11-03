<?php
/**
 * P04-T013 — Markup مودال سبد خرید
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T013', 'Markup مودال سبد خرید' );

es_check( 'target: erfansanat/template-parts/header/cart-modal.php', es_file('template-parts/header/cart-modal.php', ES_THEME) );

es_pending( 'SC-01: سبد خالی پیام درست بدهد.' );
es_pending( 'SC-02: باز/بسته‌شدن مودال کار کند.' );

es_summary();
