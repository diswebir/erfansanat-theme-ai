<?php
/**
 * P03-T007 — نقشه سازگاری ۱۸ ویژگی pa_*
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T007', 'نقشه سازگاری ۱۸ ویژگی pa_*' );

es_check( 'target: erfansanat/inc/Core/AttributeMap.php', es_file('inc/Core/AttributeMap.php', ES_THEME) );

es_pending( 'SC-01: نام ویژگی‌ها با DB برابر باشد.' );
es_pending( 'SC-02: ویژگی ناموجود fatal ندهد.' );

es_summary();
