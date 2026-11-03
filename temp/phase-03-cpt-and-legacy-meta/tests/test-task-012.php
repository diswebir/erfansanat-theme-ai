<?php
/**
 * P03-T012 — Codec متاهای Legacy
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T012', 'Codec متاهای Legacy' );

es_check( 'target: erfansanat/inc/Core/LegacyMeta.php', es_file('inc/Core/LegacyMeta.php', ES_THEME) );

es_pending( 'SC-01: round-tripe سه فرمت بدون تغییر معنایی باشد.' );
es_pending( 'SC-02: کلید تاریخی جدید ساخته نشود.' );

es_summary();
