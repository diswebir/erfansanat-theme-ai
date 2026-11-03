<?php
/**
 * P04-T016 — CTA مشترک تماس
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T016', 'CTA مشترک تماس' );

es_check( 'target: erfansanat/template-parts/global/cta-contact.php', es_file('template-parts/global/cta-contact.php', ES_THEME) );

es_pending( 'SC-01: CTA در صفحه اصلی و shop رندر شود.' );
es_pending( 'SC-02: خاموش‌کردن آن DOM را حذف کند.' );

es_summary();
