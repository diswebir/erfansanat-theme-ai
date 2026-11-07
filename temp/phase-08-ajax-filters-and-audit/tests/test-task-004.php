<?php
/**
 * P08-T004 — search modal AJAX wiring: hooks preserved, script enqueued,
 * debounce/abort/keyboard contracts present (behavior in the JSDOM suite).
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T004', 'اتصال JS مودال جستجو' );

es_check( 'target: assets/js/search.js', es_file( 'assets/js/search.js' ) );
es_check( 'target: template-parts/header/search-modal.php', es_file( 'template-parts/header/search-modal.php' ) );
es_check( 'AC-01: data-global-search-input حفظ شده', es_contains( 'template-parts/header/search-modal.php', 'data-global-search-input="true"' ) );
es_check( 'AC-01: کانتینرهای محصولات/پروژه‌ها حفظ شده', es_contains( 'template-parts/header/search-modal.php', 'data-global-search-products="true"' ) && es_contains( 'template-parts/header/search-modal.php', 'data-global-search-projects="true"' ) );
es_check( 'AC-01: data-search-modal/open/close حفظ شده', es_contains( 'template-parts/header/search-modal.php', 'data-search-modal="true"' ) && es_contains( 'template-parts/header/search-modal.php', 'data-search-modal-close="true"' ) );
es_check( 'enqueue با وابستگی es-main (ترتیب ES_AJAX)', es_contains( 'template-parts/header/search-modal.php', "'es-search'" ) && es_contains( 'template-parts/header/search-modal.php', "array( 'es-main' )" ) );
es_check( 'AC-02: abort درخواست قبلی در JS', es_contains( 'assets/js/search.js', 'AbortController' ) && es_contains( 'assets/js/search.js', '.abort(' ) );
es_check( 'AC-03: debounce تایپ', es_contains( 'assets/js/search.js', 'setTimeout' ) && es_contains( 'assets/js/search.js', 'clearTimeout' ) );
es_check( 'AC-03: کیبورد Arrow/Enter/Escape', es_contains( 'assets/js/search.js', 'ArrowDown' ) && es_contains( 'assets/js/search.js', 'ArrowUp' ) && es_contains( 'assets/js/search.js', 'Enter' ) && es_contains( 'assets/js/search.js', 'Escape' ) );
es_check( 'AC-02: گارد ترتیب درخواست‌ها (seq)', es_contains( 'assets/js/search.js', 'seq' ) );
es_check( 'مصرف endpointهای ثبت‌شده', es_contains( 'assets/js/search.js', 'es_product_search' ) && es_contains( 'assets/js/search.js', 'es_content_search' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	ob_start();
	include ES_THEME . '/template-parts/header/search-modal.php';
	$html = ob_get_clean();
	es_check( 'رندر مودال بدون fatal + اسکریپت enqueue شد', false !== strpos( $html, 'data-search-modal="true"' ) && wp_script_is( 'es-search', 'enqueued' ) );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
