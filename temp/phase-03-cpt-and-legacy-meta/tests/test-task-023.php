<?php
/**
 * P03-T023 — متاباکس Hreflang پست و ترم
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-023.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\HreflangMetabox;

es_task( 'P03-T023', 'متاباکس Hreflang پست و ترم' );

es_check( 'target: inc/Admin/HreflangMetabox.php', es_file( 'inc/Admin/HreflangMetabox.php' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';

	wp_set_current_user( 1 );
	unset( $_POST['es_hreflang'], $_POST['es_hreflang_nonce'] );
	$pid   = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه hreflang' ) );
	$nonce = wp_create_nonce( 'es_hreflang' );

	// AC-01 / SC-01: all four keys round-trip on a post.
	$_POST['es_hreflang'] = array(
		'hrefmeta_fa'    => 'https://erfansanat.com/fa/light-tunnel/',
		'hrefmeta_en'    => 'https://erfansanat.com/en/light-tunnel/',
		'hrefmeta_ar'    => 'https://erfansanat.com/ar/light-tunnel/',
		'same_key_hrefl' => 'light-tunnel-001',
	);
	$_POST['es_hreflang_nonce'] = $nonce;
	HreflangMetabox::save_post_meta( $pid );
	es_check( 'AC-01: hrefmeta_fa ذخیره شد', 'https://erfansanat.com/fa/light-tunnel/' === get_post_meta( $pid, 'hrefmeta_fa', true ) );
	es_check( 'AC-01: hrefmeta_en ذخیره شد', 'https://erfansanat.com/en/light-tunnel/' === get_post_meta( $pid, 'hrefmeta_en', true ) );
	es_check( 'AC-01: hrefmeta_ar ذخیره شد', 'https://erfansanat.com/ar/light-tunnel/' === get_post_meta( $pid, 'hrefmeta_ar', true ) );
	es_check( 'AC-01: same_key_hrefl ذخیره شد', 'light-tunnel-001' === get_post_meta( $pid, 'same_key_hrefl', true ) );

	// AC-02: an invalid URL is rejected (keeps the previous value).
	$_POST['es_hreflang'] = array( 'hrefmeta_en' => 'javascript:alert(1)' );
	HreflangMetabox::save_post_meta( $pid );
	es_check( 'AC-02: URL نامعتبر رد می‌شود', 'https://erfansanat.com/en/light-tunnel/' === get_post_meta( $pid, 'hrefmeta_en', true ) );

	// SC-01: term round-trip (city taxonomy).
	$term = wp_insert_term( 'اصفهان', 'city', array( 'slug' => 'hreflang-city-t023' ) );
	$_POST['es_hreflang'] = array(
		'hrefmeta_fa'    => 'https://erfansanat.com/city/اصفهان/',
		'hrefmeta_en'    => 'https://erfansanat.com/en/city/isfahan/',
		'same_key_hrefl' => 'city-isfahan',
	);
	HreflangMetabox::save_term_meta( $term['term_id'] );
	es_check( 'SC-01: مقدار روی ترم round-trip می‌شود', 'https://erfansanat.com/en/city/isfahan/' === get_term_meta( $term['term_id'], 'hrefmeta_en', true ) && 'city-isfahan' === get_term_meta( $term['term_id'], 'same_key_hrefl', true ) );

	// AC-03: same_key_hrefl column is sortable for projects.
	$sortable = apply_filters( 'manage_edit-projects_sortable_columns', array() );
	es_check( 'AC-03: ستون same_key_hrefl sortable است', isset( $sortable['same_key_hrefl'] ) );

	// SC-02: sorting by the hreflang key orders posts by the meta value.
	$p2 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه دوم' ) );
	update_post_meta( $pid, 'same_key_hrefl', 'b-key' );
	update_post_meta( $p2, 'same_key_hrefl', 'a-key' );
	$query = new WP_Query();
	$query->set( 'post_type', 'projects' );
	$query->set( 'orderby', 'same_key_hrefl' );
	$query->set( 'order', 'ASC' );
	$GLOBALS['wp_the_query'] = $query;
	HreflangMetabox::apply_sort( $query );
	$query->get_posts();
	$sorted = wp_list_pluck( $query->posts, 'ID' );
	es_check( 'SC-02: مرتب‌سازی ستون کار می‌کند', array( $p2, $pid ) === array_slice( $sorted, 0, 2 ) );

	// Nonce guard.
	unset( $_POST['es_hreflang_nonce'] );
	$_POST['es_hreflang'] = array( 'same_key_hrefl' => 'HACK' );
	HreflangMetabox::save_post_meta( $pid );
	es_check( 'بدون nonce ذخیره نمی‌شود', 'b-key' === get_post_meta( $pid, 'same_key_hrefl', true ) );

	// Metabox renders the four fields.
	ob_start();
	HreflangMetabox::render_metabox( get_post( $pid ) );
	$html = ob_get_clean();
	es_check( 'متاباکس: هر چهار فیلد رندر می‌شود', false !== strpos( $html, 'es_hreflang[hrefmeta_fa]' ) && false !== strpos( $html, 'es_hreflang[same_key_hrefl]' ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $p2, true );
	wp_delete_term( $term['term_id'], 'city' );
	unset( $_POST['es_hreflang'], $_POST['es_hreflang_nonce'] );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
