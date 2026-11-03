<?php
/**
 * P07-T012 — Shell تک‌نوشته
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T012', 'Shell تک‌نوشته' );

es_check( 'target: erfansanat/single.php', es_file('single.php', ES_THEME) );

es_pending( 'SC-01: یک مقاله کامل رندر شود.' );
es_pending( 'SC-02: نوشته رمزدار فرم درست بدهد.' );

es_summary();
