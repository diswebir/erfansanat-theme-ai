<?php
/**
 * P04-T015 — Footer پایانی و کپی‌رایت
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T015', 'Footer پایانی و کپی‌رایت' );

es_check( 'target: erfansanat/footer.php', es_file('footer.php', ES_THEME) );

es_pending( 'SC-01: سورس footer با مرجع parity داشته باشد.' );
es_pending( 'SC-02: لینک خالی رندر نشود.' );

es_summary();
