<?php
/**
 * P07-T024 — برگه گارانتی و خدمات پس از فروش
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-024.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T024', 'برگه گارانتی و خدمات پس از فروش' );

es_check( 'target: erfansanat/page-templates/warranty.php', es_file('page-templates/warranty.php', ES_THEME) );

es_pending( 'SC-01: ویرایش مدت گارانتی در خروجی دیده شود.' );

es_summary();
