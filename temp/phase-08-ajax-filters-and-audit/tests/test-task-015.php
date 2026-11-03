<?php
/**
 * P08-T015 — فرم استخدام
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T015', 'فرم استخدام' );

es_check( 'target: erfansanat/template-parts/forms/recruitment.php', es_file('template-parts/forms/recruitment.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Forms/RecruitmentForm.php', es_file('inc/Forms/RecruitmentForm.php', ES_THEME) );

es_pending( 'SC-01: درخواست شغلی با رزومه مجاز ثبت شود.' );
es_pending( 'SC-02: فایل php/exe رد شود.' );

es_summary();
