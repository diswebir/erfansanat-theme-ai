<?php
/**
 * P08-T014 — فرم استعلام قیمت پاپ‌آپ
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T014', 'فرم استعلام قیمت پاپ‌آپ' );

es_check( 'target: erfansanat/template-parts/forms/quote.php', es_file('template-parts/forms/quote.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Forms/QuoteForm.php', es_file('inc/Forms/QuoteForm.php', ES_THEME) );

es_pending( 'SC-01: استعلام محصول ثبت و به محصول لینک شود.' );
es_pending( 'SC-02: شناسه نامعتبر رد شود.' );

es_summary();
