<?php
/**
 * P08-T012 — Schema و ذخیره‌گاه فرم‌ها
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T012', 'Schema و ذخیره‌گاه فرم‌ها' );

es_check( 'target: erfansanat/inc/Forms/Schema.php', es_file('inc/Forms/Schema.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Forms/Entries.php', es_file('inc/Forms/Entries.php', ES_THEME) );

es_pending( 'SC-01: ثبت ورودی آزمایشی ذخیره شود.' );
es_pending( 'SC-02: کاربر غیرمجاز لیست را نبیند.' );

es_summary();
