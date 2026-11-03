<?php
/**
 * P07-T027 — تک‌خدمت
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-027.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T027', 'تک‌خدمت' );

es_check( 'target: erfansanat/single-services.php', es_file('single-services.php', ES_THEME) );
es_check( 'target: erfansanat/template-parts/service/details.php', es_file('template-parts/service/details.php', ES_THEME) );

es_pending( 'SC-01: یک خدمت کامل رندر شود.' );
es_pending( 'SC-02: لینک بازگشت به smartelement درست باشد.' );

es_summary();
