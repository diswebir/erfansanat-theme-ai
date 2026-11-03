<?php
/**
 * P02-T015 — SectionManager: schema و ذخیره متا
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T015', 'SectionManager: schema و ذخیره متا' );

es_check( 'target: erfansanat/inc/Sections/SectionManager.php', es_file('inc/Sections/SectionManager.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Sections/Schema.php', es_file('inc/Sections/Schema.php', ES_THEME) );

es_pending( 'SC-01: ذخیره دو سکشن و خواندن مجدد پایدار باشد.' );
es_pending( 'SC-02: سکشن تکراری ID یکتا بگیرد.' );

es_summary();
