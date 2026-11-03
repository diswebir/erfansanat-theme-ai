<?php
/**
 * P04-T005 — Walker ناوبری دسکتاپ
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T005', 'Walker ناوبری دسکتاپ' );

es_check( 'target: erfansanat/inc/Core/NavWalkers.php', es_file('inc/Core/NavWalkers.php', ES_THEME) );

es_pending( 'SC-01: منوی سه‌سطحی DOM مرجع را تولید کند.' );
es_pending( 'SC-02: منوی خالی fallback نشان دهد.' );

es_summary();
