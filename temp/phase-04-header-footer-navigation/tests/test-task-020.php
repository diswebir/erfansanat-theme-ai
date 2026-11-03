<?php
/**
 * P04-T020 — Parity و دسترس‌پذیری ناوبری
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-020.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T020', 'Parity و دسترس‌پذیری ناوبری' );

es_check( 'target: temp/phase-04-header-footer-navigation/tests/test-task-020.php', es_file('phase-04-header-footer-navigation/tests/test-task-020.php', ES_ROOT) );

es_pending( 'SC-01: اسنپ‌شات DOM هدر/فوتر پاس شود.' );
es_pending( 'SC-02: پیمایش کامل با کیبورد ممکن باشد.' );

es_summary();
