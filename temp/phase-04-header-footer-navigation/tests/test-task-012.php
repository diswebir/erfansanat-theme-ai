<?php
/**
 * P04-T012 — Markup مودال جستجو
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T012', 'Markup مودال جستجو' );

es_check( 'target: erfansanat/template-parts/header/search-modal.php', es_file('template-parts/header/search-modal.php', ES_THEME) );

es_pending( 'SC-01: باز/بسته‌شدن مودال کار کند.' );
es_pending( 'SC-02: focus به input برود.' );

es_summary();
