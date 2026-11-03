<?php
/**
 * P08-T021 — ممیزی ریسپانسیو و دسترس‌پذیری
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-021.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T021', 'ممیزی ریسپانسیو و دسترس‌پذیری' );

es_check( 'target: temp/phase-08-ajax-filters-and-audit/tests/test-task-021.php', es_file('phase-08-ajax-filters-and-audit/tests/test-task-021.php', ES_ROOT) );

es_pending( 'SC-01: اسنپ‌شات سه viewport پاس شود.' );
es_pending( 'SC-02: پیمایش کیبورد صفحات کلیدی ممکن باشد.' );

es_summary();
