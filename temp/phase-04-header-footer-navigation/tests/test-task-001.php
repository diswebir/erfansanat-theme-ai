<?php
/**
 * P04-T001 — ثبت چهار موقعیت منو
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T001', 'ثبت چهار موقعیت منو' );

es_check( 'target: erfansanat/inc/Core/Menus.php', es_file('inc/Core/Menus.php', ES_THEME) );

es_pending( 'SC-01: get_registered_nav_menus هر چهار کلید را داشته باشد.' );
es_pending( 'SC-02: اختصاص منو از ادمین کار کند.' );

es_summary();
