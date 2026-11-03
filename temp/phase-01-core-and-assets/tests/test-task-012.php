<?php
/**
 * P01-T012 — یکپارچگی asset و بسته‌بندی
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T012', 'یکپارچگی asset و بسته‌بندی' );

es_check( 'target: temp/tools/package-theme.sh', es_file('tools/package-theme.sh', ES_ROOT) );
es_check( 'target: temp/phase-01-core-and-assets/tests/test-task-012.php', es_file('phase-01-core-and-assets/tests/test-task-012.php', ES_ROOT) );

es_pending( 'SC-01: unzip و فعال‌سازی پوسته بسته‌بندی‌شده موفق باشد.' );
es_pending( 'SC-02: asset شکسته صفر باشد.' );

es_summary();
