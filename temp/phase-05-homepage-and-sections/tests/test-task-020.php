<?php
/**
 * P05-T020 — یکپارچه‌سازی CTA/فوتر صفحه اصلی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-020.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T020', 'یکپارچه‌سازی CTA/فوتر صفحه اصلی' );

es_check( 'target: erfansanat/front-page.php', es_file('front-page.php', ES_THEME) );

es_pending( 'SC-01: اسنپ‌شات انتهای صفحه parity داشته باشد.' );
es_pending( 'SC-02: فوتر دوبار رندر نشود.' );

es_summary();
