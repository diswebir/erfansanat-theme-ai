<?php
/**
 * P02-T011 — تب مگامنو
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Tabs\MegaMenuTab;
use ErfanSanat\Theme\Options\Store;

es_task( 'P02-T011', 'تب مگامنو' );

es_check( 'target: inc/Options/Tabs/MegaMenuTab.php', es_file( 'inc/Options/Tabs/MegaMenuTab.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	Store::reset();

	// Register the city taxonomy for this test (Phase 3 owns the real registration).
	$registered_here = false;
	if ( ! taxonomy_exists( 'city' ) ) {
		register_taxonomy( 'city', 'post', array( 'public' => true, 'rewrite' => array( 'slug' => 'city' ) ) );
		$registered_here = true;
	}

	// SC-02: a Persian term slug links correctly.
	$term = wp_insert_term( 'اصفهان', 'city', array( 'slug' => 'اصفهان' ) );
	$term_id = is_wp_error( $term ) ? 0 : (int) $term['term_id'];
	$links   = MegaMenuTab::term_links( 'city', 10 );
	$persian = null;
	foreach ( $links as $link ) {
		if ( 'اصفهان' === $link['name'] ) {
			$persian = $link;
		}
	}
	// WordPress stores Persian slugs percent-encoded; the link must contain the
	// term's actual slug regardless of its encoding (slug-agnostic).
	es_check( 'SC-02: ترم فارسی درست لینک می‌شود', null !== $persian && '' !== $persian['url'] && false !== strpos( strtolower( $persian['url'] ), strtolower( $persian['slug'] ) ) );

	// AC-02: term source is slug-agnostic (queried by taxonomy, returns the Persian term).
	es_check( 'AC-02: منبع ترم slug-agnostic است', null !== $persian && $persian['term_id'] === $term_id );

	// SC-01 / AC-01: item count is configurable and applied.
	for ( $i = 1; $i <= 4; $i++ ) {
		wp_insert_term( 'شهر آزمایشی ' . $i, 'city', array( 'slug' => 'test-city-' . $i ) );
	}
	Store::set( 'mega_projects_per_tab', 3 );
	es_check( 'SC-01: تغییر تعداد آیتم اعمال می‌شود', 3 === count( MegaMenuTab::get_terms_for( 'city', (int) es_option( 'mega_projects_per_tab' ) ) ) );
	es_check( 'AC-01: پیکربندی پروژه‌ها per_tab را دارد', 3 === MegaMenuTab::projects_config()['per_tab'] );
	Store::set( 'mega_products_per_tab', 4 );
	es_check( 'AC-01: پیکربندی محصولات per_tab را دارد', 4 === MegaMenuTab::products_config()['per_tab'] );

	// AC-03: empty menu has a safe fallback.
	$fallback = MegaMenuTab::menu_items( 'es_nonexistent_taxonomy', 6 );
	es_check( 'AC-03: منوی خالی fallback امن دارد', ! empty( $fallback ) && false !== strpos( $fallback[0]['url'], home_url() ) );

	// Cleanup terms + taxonomy.
	$all_terms = get_terms( array( 'taxonomy' => 'city', 'hide_empty' => false, 'fields' => 'ids' ) );
	if ( ! is_wp_error( $all_terms ) ) {
		foreach ( $all_terms as $tid ) {
			wp_delete_term( (int) $tid, 'city' );
		}
	}
	if ( $registered_here ) {
		unregister_taxonomy( 'city' );
	}
	Store::reset();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
