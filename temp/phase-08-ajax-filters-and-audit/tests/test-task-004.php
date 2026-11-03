<?php
/**
 * P08-T004 — اتصال JS مودال جستجو
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T004', 'اتصال JS مودال جستجو' );

es_check( 'target: erfansanat/assets/js/search.js', es_file('assets/js/search.js', ES_THEME) );
es_check( 'target: erfansanat/template-parts/header/search-modal.php', es_file('template-parts/header/search-modal.php', ES_THEME) );

es_pending( 'SC-01: تایپ سریع فقط آخرین نتیجه را نشان دهد.' );
es_pending( 'SC-02: کیبورد نتیجه را انتخاب کند.' );

es_summary();
