<?php
/**
 * P02-T017 — SectionManager: ویرایش محتوای سکشن
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T017', 'SectionManager: ویرایش محتوای سکشن' );

es_check( 'target: erfansanat/inc/Sections/SectionManager.php', es_file('inc/Sections/SectionManager.php', ES_THEME) );

es_pending( 'SC-01: ویرایش عنوان/تصویر یک سکشن ذخیره شود.' );
es_pending( 'SC-02: Repeater مشتری‌ها درست ذخیره شود.' );

es_summary();
