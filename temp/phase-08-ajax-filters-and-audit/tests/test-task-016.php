<?php
/**
 * P08-T016 — فرم بازخورد مشتری
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T016', 'فرم بازخورد مشتری' );

es_check( 'target: erfansanat/template-parts/forms/feedback.php', es_file('template-parts/forms/feedback.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Forms/FeedbackForm.php', es_file('inc/Forms/FeedbackForm.php', ES_THEME) );

es_pending( 'SC-01: بازخورد معتبر ذخیره شود.' );
es_pending( 'SC-02: امتیاز خارج از بازه رد شود.' );

es_summary();
