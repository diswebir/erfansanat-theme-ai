<?php
/**
 * P04-T014 — بخش اطلاعات فوتر
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T014', 'بخش اطلاعات فوتر' );

es_check( 'target: erfansanat/template-parts/global/footer-info.php', es_file('template-parts/global/footer-info.php', ES_THEME) );

es_pending( 'SC-01: تغییر نشانی در فوتر دیده شود.' );
es_pending( 'SC-02: منوی خالی ستون را خراب نکند.' );

es_summary();
