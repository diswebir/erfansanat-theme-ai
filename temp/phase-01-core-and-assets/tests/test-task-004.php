<?php
/**
 * P01-T004 — چرخه عمر و Theme Supports
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T004', 'چرخه عمر و Theme Supports' );

es_check( 'target: erfansanat/inc/Core/Theme.php', es_file('inc/Core/Theme.php', ES_THEME) );

es_pending( 'SC-01: current_theme_supports پس از فعال‌سازی true باشد.' );
es_pending( 'SC-02: تغییر پوسته رفتن/برگشتن بدون notice باشد.' );

es_summary();
