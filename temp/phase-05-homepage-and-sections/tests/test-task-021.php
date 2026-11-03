<?php
/**
 * P05-T021 — رگرسیون کوئری/ترتیب/حالت خالی صفحه اصلی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-021.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T021', 'رگرسیون کوئری/ترتیب/حالت خالی صفحه اصلی' );

es_check( 'target: temp/phase-05-homepage-and-sections/tests/test-task-021.php', es_file('phase-05-homepage-and-sections/tests/test-task-021.php', ES_ROOT) );

es_pending( 'SC-01: با داده خالی صفحه fatal ندهد.' );
es_pending( 'SC-02: تغییر ترتیب/خاموش‌سازی در خروجی اعمال شود.' );

es_summary();
