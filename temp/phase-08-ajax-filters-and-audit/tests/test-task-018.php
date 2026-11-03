<?php
/**
 * P08-T018 — ایمیل، Honeypot و Rate-limit
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T018', 'ایمیل، Honeypot و Rate-limit' );

es_check( 'target: erfansanat/inc/Forms/Mailer.php', es_file('inc/Forms/Mailer.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Forms/SpamGuard.php', es_file('inc/Forms/SpamGuard.php', ES_THEME) );

es_pending( 'SC-01: ارسال پشت‌سرهم محدود شود.' );
es_pending( 'SC-02: ایمیل نامعتبر رد شود.' );
es_pending( 'SC-03: ثبت ورودی حتی با خطای ایمیل حفظ شود.' );

es_summary();
