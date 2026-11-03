<?php
/**
 * P04-T018 — Walker تولیدات شرکت
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T018', 'Walker تولیدات شرکت' );

es_check( 'target: erfansanat/inc/Core/NavWalkers.php', es_file('inc/Core/NavWalkers.php', ES_THEME) );

es_pending( 'SC-01: منوی اختصاص‌داده‌شده رندر شود.' );
es_pending( 'SC-02: fallback دسته‌های محصول کار کند.' );

es_summary();
