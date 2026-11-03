<?php
/**
 * P05-T007 — Markup و JS هیرو
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T007', 'Markup و JS هیرو' );

es_check( 'target: erfansanat/template-parts/home/section-hero.php', es_file('template-parts/home/section-hero.php', ES_THEME) );

es_pending( 'SC-01: سوئیچ صحنه‌ها کار کند.' );
es_pending( 'SC-02: ویدیو مودال باز/بسته شود.' );

es_summary();
