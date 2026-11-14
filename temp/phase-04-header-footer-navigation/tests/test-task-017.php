<?php
/**
 * P04-T017 — Hreflang در head
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Hreflang;

es_task( 'P04-T017', 'Hreflang در head' );

es_check( 'target: inc/Core/Hreflang.php', es_file( 'inc/Core/Hreflang.php' ) );
es_check( 'boot در functions.php', es_contains( 'functions.php', 'Hreflang::boot()' ) );
es_check( 'AC-02: فرمت استاندارد link (نه meta قدیمی)', es_contains( 'inc/Core/Hreflang.php', 'rel="alternate"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function () {
		ob_start();
		Hreflang::render();
		return (string) ob_get_clean();
	};

	// ── Singular post with fa/en/ar URLs ──────────────────────────────────.
	$pid = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'hreflang t017' ) );
	update_post_meta( $pid, 'hrefmeta_fa', 'https://erfansanat.com' );
	update_post_meta( $pid, 'hrefmeta_en', 'https://erfansanat.com/en' );
	update_post_meta( $pid, 'hrefmeta_ar', 'https://erfansanat.com/ar' );

	$GLOBALS['wp_query'] = new WP_Query( array( 'p' => $pid, 'post_type' => 'post' ) );
	es_check( 'زمینه: is_singular برقرار است', is_singular() );

	$urls = Hreflang::current_urls();
	es_check( 'SC-01: آدرس fa درست', ( $urls['fa'] ?? '' ) === 'https://erfansanat.com' );
	es_check( 'SC-01: آدرس en درست', ( $urls['en'] ?? '' ) === 'https://erfansanat.com/en' );
	es_check( 'SC-01: آدرس ar درست', ( $urls['ar'] ?? '' ) === 'https://erfansanat.com/ar' );

	$out = $render();
	es_check( 'AC-01: تگ link hreflang=fa', false !== strpos( $out, '<link rel="alternate" hreflang="fa" href="https://erfansanat.com"' ) );
	es_check( 'AC-01: تگ link hreflang=en', false !== strpos( $out, '<link rel="alternate" hreflang="en" href="https://erfansanat.com/en"' ) );
	es_check( 'AC-01: تگ link hreflang=ar', false !== strpos( $out, '<link rel="alternate" hreflang="ar" href="https://erfansanat.com/ar"' ) );
	es_check( 'AC-01: x-default تولید می‌شود', false !== strpos( $out, 'hreflang="x-default"' ) );
	es_check( 'AC-02: متای قدیمی hreflang: تولید نمی‌شود', false === strpos( $out, '<meta name="hreflang' ) );

	// ── SC-02: empty values produce no tag ────────────────────────────────.
	delete_post_meta( $pid, 'hrefmeta_ar' );
	$urls = Hreflang::current_urls();
	es_check( 'SC-02: مقدار خالی ar حذف می‌شود', ! isset( $urls['ar'] ) );
	es_check( 'SC-02: تگ ar رندر نمی‌شود', false === strpos( $render(), 'hreflang="ar"' ) );

	delete_post_meta( $pid, 'hrefmeta_fa' );
	delete_post_meta( $pid, 'hrefmeta_en' );
	es_check( 'SC-02: بدون مقدار هیچ تگی نیست', '' === trim( $render() ) );

	// ── Term (taxonomy) context ───────────────────────────────────────────.
	$term = wp_insert_term( 'شهر hreflang', 'city', array( 'slug' => 'hreflang-t017' ) );
	$tid  = is_array( $term ) ? (int) $term['term_id'] : 0;
	update_term_meta( $tid, 'hrefmeta_fa', 'https://erfansanat.com/city/fa' );
	update_term_meta( $tid, 'hrefmeta_en', 'https://erfansanat.com/city/en' );
	$GLOBALS['wp_query'] = new WP_Query( array( 'city' => 'hreflang-t017' ) );
	es_check( 'زمینه: is_tax برقرار است', is_tax() );
	$urls = Hreflang::current_urls();
	es_check( 'SC-01: hreflang ترم fa', ( $urls['fa'] ?? '' ) === 'https://erfansanat.com/city/fa' );
	es_check( 'SC-01: hreflang ترم en', ( $urls['en'] ?? '' ) === 'https://erfansanat.com/city/en' );
	es_check( 'SC-02: ترم بدون ar تگ ar ندارد', false === strpos( $render(), 'hreflang="ar"' ) );

	// ── AC-03: defer to SEOPress / hreflangpch ────────────────────────────.
	$GLOBALS['wp_query'] = new WP_Query( array( 'p' => $pid, 'post_type' => 'post' ) );
	update_post_meta( $pid, 'hrefmeta_fa', 'https://erfansanat.com' );
	$orig_plugins        = get_option( 'active_plugins', array() );

	update_option( 'active_plugins', array( 'wp-seopress/seopress.php' ) );
	es_check( 'AC-03: با SEOPress فعال، تم خروجی نمی‌دهد', false === Hreflang::should_render() && '' === trim( $render() ) );

	update_option( 'active_plugins', array( 'hreflangpch/hreflangpch.php' ) );
	es_check( 'AC-03: با hreflangpch فعال، تم خروجی نمی‌دهد', false === Hreflang::should_render() && '' === trim( $render() ) );

	update_option( 'active_plugins', $orig_plugins );
	es_check( 'AC-03: بدون افزونه، تم فعال است', true === Hreflang::should_render() );

	add_filter( 'es_hreflang_enabled', '__return_false' );
	es_check( 'AC-03: فیلتر es_hreflang_enabled خاموش می‌کند', '' === trim( $render() ) );
	remove_filter( 'es_hreflang_enabled', '__return_false' );

	// Cleanup.
	wp_delete_post( $pid, true );
	if ( $tid ) {
		wp_delete_term( $tid, 'city' );
	}
	$GLOBALS['wp_query'] = new WP_Query();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
