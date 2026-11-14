<?php
/**
 * P03-T006 — حفظ product_tag/product_brand
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T006', 'حفظ product_tag/product_brand' );

es_check( 'target: inc/Core/Rewrites.php', es_file( 'inc/Core/Rewrites.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// AC-03: the theme does not register these taxonomies itself.
	es_check( 'AC-03: قالب product_tag را ثبت نمی‌کند', ! taxonomy_exists( 'product_tag' ) );
	es_check( 'AC-03: قالب product_brand را ثبت نمی‌کند', ! taxonomy_exists( 'product_brand' ) );

	// AC-01: product-tag base with with_front=false.
	$bad_tag = array( 'rewrite' => array( 'slug' => 'wrong-tag', 'with_front' => true ) );
	$fixed   = apply_filters( 'register_taxonomy_args', $bad_tag, 'product_tag' );
	es_check( 'AC-01: slug برچسب = product-tag', 'product-tag' === $fixed['rewrite']['slug'] );
	es_check( 'AC-01: with_front برچسب = false', false === $fixed['rewrite']['with_front'] );

	// AC-02: product_brand stays hierarchical; its slug is not altered.
	$bad_brand = array( 'hierarchical' => false, 'rewrite' => array( 'slug' => 'brand', 'with_front' => false ) );
	$fixed_b   = apply_filters( 'register_taxonomy_args', $bad_brand, 'product_brand' );
	es_check( 'AC-02: برند hierarchical می‌ماند', true === $fixed_b['hierarchical'] );
	es_check( 'AC-02: اسلاگ برند دست‌نخورده', 'brand' === $fixed_b['rewrite']['slug'] );

	// Unrelated taxonomies are untouched.
	$cat_args = array( 'rewrite' => array( 'slug' => 'category' ) );
	es_check( 'AC-03: تکسونومی‌های دیگر دست‌نخورده', apply_filters( 'register_taxonomy_args', $cat_args, 'category' ) === $cat_args );

	// SC-01: brand/tag archives resolve (no 404 / WP_Error).
	register_taxonomy( 'product_tag', 'product', array( 'rewrite' => array( 'slug' => 'wrong-tag', 'with_front' => true ) ) );
	register_taxonomy( 'product_brand', 'product', array( 'hierarchical' => false, 'rewrite' => array( 'slug' => 'brand' ) ) );
	$tag   = wp_insert_term( 'ریسه', 'product_tag', array( 'slug' => 'tag-t006' ) );
	$brand = wp_insert_term( 'عرفان', 'product_brand', array( 'slug' => 'brand-t006' ) );
	$tlink = get_term_link( (int) $tag['term_id'], 'product_tag' );
	$blink = get_term_link( (int) $brand['term_id'], 'product_brand' );
	es_check( 'SC-01: آرشیو برچسب resolve می‌شود', ! is_wp_error( $tlink ) && false !== strpos( $tlink, '/product-tag/tag-t006/' ) );
	es_check( 'SC-01: آرشیو برند resolve می‌شود', ! is_wp_error( $blink ) && false !== strpos( $blink, '/brand/brand-t006/' ) );

	wp_delete_term( $tag['term_id'], 'product_tag' );
	wp_delete_term( $brand['term_id'], 'product_brand' );
	unregister_taxonomy( 'product_tag' );
	unregister_taxonomy( 'product_brand' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
