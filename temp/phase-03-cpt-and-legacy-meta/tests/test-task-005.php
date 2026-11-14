<?php
/**
 * P03-T005 — حفظ rewrite محصول و product_cat
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Rewrites;

es_task( 'P03-T005', 'حفظ rewrite محصول و product_cat' );

es_check( 'target: inc/Core/Rewrites.php', es_file( 'inc/Core/Rewrites.php' ) );
es_check( 'AC-03: از فیلتر args استفاده می‌شود (نه ثبت مجدد)', es_contains( 'inc/Core/Rewrites.php', 'register_post_type_args' ) && es_contains( 'inc/Core/Rewrites.php', 'register_taxonomy_args' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// The theme itself never registers product (WooCommerce owns it).
	es_check( 'AC-03: قالب product را دوباره ثبت نمی‌کند', ! post_type_exists( 'product' ) );

	// AC-01: product single rewrite forced to /product/ with_front=false.
	$bad_product = array( 'public' => true, 'rewrite' => array( 'slug' => 'wrong-base', 'with_front' => true ) );
	$fixed       = apply_filters( 'register_post_type_args', $bad_product, 'product' );
	es_check( 'AC-01: slug محصول = product', 'product' === $fixed['rewrite']['slug'] );
	es_check( 'AC-01: with_front محصول = false', false === $fixed['rewrite']['with_front'] );

	// AC-01: page (shop) args are left untouched.
	$page_args = array( 'public' => true, 'rewrite' => array( 'slug' => 'shop' ) );
	es_check( 'AC-01: آرشیو shop دست‌نخورده می‌ماند', apply_filters( 'register_post_type_args', $page_args, 'page' ) === $page_args );

	// AC-02/03: product_cat slug stays product_cat (not product-category), with_front=true.
	$bad_cat = array( 'hierarchical' => false, 'rewrite' => array( 'slug' => 'product-category', 'with_front' => false ) );
	$fixed_cat = apply_filters( 'register_taxonomy_args', $bad_cat, 'product_cat' );
	es_check( 'AC-02: slug دسته = product_cat', 'product_cat' === $fixed_cat['rewrite']['slug'] );
	es_check( 'AC-02: with_front دسته = true', true === $fixed_cat['rewrite']['with_front'] );
	es_check( 'AC-02: دسته hierarchical می‌ماند', true === $fixed_cat['hierarchical'] );
	es_check( 'AC-03: product-category جایگزین نمی‌شود', 'product-category' !== $fixed_cat['rewrite']['slug'] );

	// SC-01/02: register stubs with WRONG args; WP applies our filter → correct URLs.
	register_post_type( 'product', array( 'public' => true, 'rewrite' => array( 'slug' => 'wrong-base', 'with_front' => true ) ) );
	register_taxonomy( 'product_cat', 'product', array( 'hierarchical' => false, 'rewrite' => array( 'slug' => 'product-category', 'with_front' => false ) ) );

	$prod = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول تست', 'post_name' => 'test-product-t005' ) );
	es_check( 'SC-01: لینک محصول /product/slug/', false !== strpos( get_permalink( $prod ), '/product/test-product-t005/' ) );

	$parent = wp_insert_term( 'روشنایی', 'product_cat', array( 'slug' => 'lighting-t005' ) );
	$child  = wp_insert_term( 'خیابانی', 'product_cat', array( 'slug' => 'street-t005', 'parent' => $parent['term_id'] ) );
	$plink  = get_term_link( (int) $parent['term_id'], 'product_cat' );
	$clink  = get_term_link( (int) $child['term_id'], 'product_cat' );
	es_check( 'SC-01: لینک دسته /product_cat/', ! is_wp_error( $plink ) && false !== strpos( $plink, '/product_cat/lighting-t005/' ) );
	es_check( 'SC-02: ساختار سلسله‌مراتبی دسته حفظ می‌شود', ! is_wp_error( $clink ) && false !== strpos( $clink, 'lighting-t005/street-t005' ) );

	wp_delete_post( $prod, true );
	wp_delete_term( $child['term_id'], 'product_cat' );
	wp_delete_term( $parent['term_id'], 'product_cat' );
	unregister_taxonomy( 'product_cat' );
	unregister_post_type( 'product' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
