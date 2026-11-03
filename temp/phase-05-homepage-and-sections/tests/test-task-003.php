<?php
/**
 * P05-T003 — کارت استاندارد محصول
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T003', 'کارت استاندارد محصول' );

es_check( 'target: erfansanat/template-parts/cards/card-product.php', es_file('template-parts/cards/card-product.php', ES_THEME) );

es_pending( 'SC-01: محصول آنلاین و استعلامی درست رندر شوند.' );
es_pending( 'SC-02: data-product-price با قیمت واقعی برابر باشد.' );

es_summary();
