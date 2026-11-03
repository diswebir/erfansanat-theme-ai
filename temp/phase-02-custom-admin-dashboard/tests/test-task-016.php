<?php
/**
 * P02-T016 — SectionManager: UI ترتیب و فعال‌سازی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P02-T016', 'SectionManager: UI ترتیب و فعال‌سازی' );

es_check( 'target: erfansanat/inc/Admin/SectionMetabox.php', es_file('inc/Admin/SectionMetabox.php', ES_THEME) );
es_check( 'target: erfansanat/assets/admin/sections.js', es_file('assets/admin/sections.js', ES_THEME) );

es_pending( 'SC-01: جابجایی دو سکشن در خروجی اعمال شود.' );
es_pending( 'SC-02: ذخیره غیرمجاز رد شود.' );

es_summary();
