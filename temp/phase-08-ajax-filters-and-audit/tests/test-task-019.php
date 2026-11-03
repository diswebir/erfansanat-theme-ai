<?php
/**
 * P08-T019 — ممیزی SEO و Schema
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T019', 'ممیزی SEO و Schema' );

es_check( 'target: erfansanat/inc/Core/Seo.php', es_file('inc/Core/Seo.php', ES_THEME) );
es_check( 'target: temp/phase-08-ajax-filters-and-audit/tests/test-task-019.php', es_file('phase-08-ajax-filters-and-audit/tests/test-task-019.php', ES_ROOT) );

es_pending( 'SC-01: خروجی head صفحه اصلی/محصول/مقاله validate شود.' );
es_pending( 'SC-02: متاهای rank_math قدیمی حفظ شوند.' );

es_summary();
