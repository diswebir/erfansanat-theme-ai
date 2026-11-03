<?php
/**
 * P08-T013 — فرم تماس با ما
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T013', 'فرم تماس با ما' );

es_check( 'target: erfansanat/template-parts/forms/contact.php', es_file('template-parts/forms/contact.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Forms/ContactForm.php', es_file('inc/Forms/ContactForm.php', ES_THEME) );

es_pending( 'SC-01: ارسال معتبر ذخیره شود.' );
es_pending( 'SC-02: honeypot/nonce نامعتبر رد شود.' );

es_summary();
