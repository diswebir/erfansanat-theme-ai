<?php
/**
 * P05-T002 — کارت استاندارد پروژه
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T002', 'کارت استاندارد پروژه' );

es_check( 'target: erfansanat/template-parts/cards/card-project.php', es_file('template-parts/cards/card-project.php', ES_THEME) );

es_pending( 'SC-01: کارت یک پروژه واقعی رندر شود.' );
es_pending( 'SC-02: data-attrها با متا/ترم برابر باشند.' );

es_summary();
