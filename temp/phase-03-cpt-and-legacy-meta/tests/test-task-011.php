<?php
/**
 * P03-T011 — ستون‌ها و فیلترهای ادمین پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T011', 'ستون‌ها و فیلترهای ادمین پروژه' );

es_check( 'target: erfansanat/inc/Admin/ProjectColumns.php', es_file('inc/Admin/ProjectColumns.php', ES_THEME) );

es_pending( 'SC-01: فیلتر شهر فقط پروژه‌های همان شهر را نشان دهد.' );
es_pending( 'SC-02: مرتب‌سازی کلید hreflang کار کند.' );

es_summary();
