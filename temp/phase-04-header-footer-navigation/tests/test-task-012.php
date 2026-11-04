<?php
/**
 * P04-T012 — Markup مودال جستجو
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T012', 'Markup مودال جستجو' );

es_check( 'target: template-parts/header/search-modal.php', es_file( 'template-parts/header/search-modal.php' ) );
es_check( 'AC-01: data-search-modal-open در هدر هست', es_contains( 'template-parts/header/brand-row.php', 'data-search-modal-open="true"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}

	$render = static function () {
		ob_start();
		get_template_part( 'template-parts/header/search-modal' );
		return (string) ob_get_clean();
	};

	// AC-02: empty states are real messages, not placeholders.
	es_check( 'AC-02: پیام خالی محصولات واقعی است', es_contains( 'template-parts/header/search-modal.php', 'هنوز محصولی ثبت نشده است' ) );
	es_check( 'AC-02: پیام خالی پروژه‌ها واقعی است', es_contains( 'template-parts/header/search-modal.php', 'هنوز پروژه‌ای ثبت نشده است' ) );

	// Create real fixtures.
	$prod = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول جستجوی تست' ) );
	$proj = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه جستجوی تست' ) );

	$html = $render();

	// AC-01: preserved data attributes.
	es_check( 'AC-01: data-search-modal حفظ شده', false !== strpos( $html, 'data-search-modal="true"' ) );
	es_check( 'AC-01: data-search-modal-close حفظ شده', false !== strpos( $html, 'data-search-modal-close="true"' ) );
	es_check( 'AC-01: data-global-search-input حفظ شده', false !== strpos( $html, 'data-global-search-input="true"' ) );
	es_check( 'AC-01: ظروف نتایج محصولات/پروژه‌ها', false !== strpos( $html, 'data-global-search-products="true"' ) && false !== strpos( $html, 'data-global-search-projects="true"' ) );

	// AC-02: real DB results rendered with data-search-item.
	es_check( 'AC-02: محصول واقعی رندر می‌شود', false !== strpos( $html, 'محصول جستجوی تست' ) );
	es_check( 'AC-02: پروژه واقعی رندر می‌شود', false !== strpos( $html, 'پروژه جستجوی تست' ) );
	es_check( 'AC-02: data-search-item برای فیلتر کلاینت', substr_count( $html, 'data-search-item=' ) >= 2 );

	// AC-03: keyboard-accessible input + labelled close.
	es_check( 'AC-03: input از نوع search است', false !== strpos( $html, 'type="search" data-global-search-input' ) );
	es_check( 'AC-03: دکمه بستن aria-label دارد', false !== strpos( $html, 'aria-label="بستن جستجو"' ) );

	// SC-02: main.js moves focus to the input on open.
	$js = (string) file_get_contents( ES_THEME . '/assets/js/main.js' );
	es_check( 'SC-02: focus به input می‌رود (main.js)', false !== strpos( $js, 'searchInput.focus()' ) );
	es_check( 'SC-01: Escape مودال را می‌بندد (main.js)', false !== strpos( $js, 'closeSearchModal' ) );

	// Cleanup → our fixtures no longer appear.
	wp_delete_post( $prod, true );
	wp_delete_post( $proj, true );
	$html = $render();
	es_check( 'پس از حذف، نتیجه تست غایب است', false === strpos( $html, 'محصول جستجوی تست' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
