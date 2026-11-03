<?php
/**
 * P04-T009 — Shell منوی موبایل Off-canvas
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T009', 'Shell منوی موبایل Off-canvas' );

es_check( 'target: erfansanat/template-parts/header/mobile-menu.php', es_file('template-parts/header/mobile-menu.php', ES_THEME) );

es_pending( 'SC-01: باز کردن منو focus را به drawer ببرد.' );
es_pending( 'SC-02: Escape منو را ببندد.' );

es_summary();
