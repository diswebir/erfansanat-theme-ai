<?php
/**
 * P06-T020 — آرشیو برچسب/برند محصول
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-020.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T020', 'آرشیو برچسب/برند' );

es_check( 'target: taxonomy-product_tag.php', es_file( 'woocommerce/taxonomy-product_tag.php', ES_THEME ) );
es_check( 'target: taxonomy-product_brand.php', es_file( 'woocommerce/taxonomy-product_brand.php', ES_THEME ) );
es_check( 'AC-03: هر دو از shell استاندارد استفاده می‌کنند', es_contains( 'woocommerce/taxonomy-product_tag.php', "product', 'archive-shell'" ) && es_contains( 'woocommerce/taxonomy-product_brand.php', "product', 'archive-shell'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$mkprod = static function ( $title, $slug ) {
		return (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug ) );
	};
	$tag   = wp_insert_term( 'ضدآب', 'product_tag', array( 'slug' => 'waterproof', 'description' => 'محصولات ضدآب IP68.' ) );
	$brand = wp_insert_term( 'عرفان صنعت', 'product_brand', array( 'slug' => 'erfansanat-brand', 'description' => 'تولیدات کارخانه عرفان صنعت.' ) );
	$tid   = (int) $tag['term_id'];
	$bid   = (int) $brand['term_id'];

	$p1 = $mkprod( 'ریسه ضدآب', 'waterproof-string' );
	$p2 = $mkprod( 'پوینت ضدآب', 'waterproof-point' );
	wp_set_object_terms( $p1, array( $tid ), 'product_tag' );
	wp_set_object_terms( $p2, array( $tid ), 'product_tag' );
	wp_set_object_terms( $p1, array( $bid ), 'product_brand' );
	wp_set_object_terms( $p2, array( $bid ), 'product_brand' );

	es_check( 'AC-01: اسلاگ product_tag حفظ شده', taxonomy_exists( 'product_tag' ) && get_term_by( 'slug', 'waterproof', 'product_tag' ) instanceof WP_Term );
	es_check( 'AC-01: اسلاگ product_brand حفظ شده', taxonomy_exists( 'product_brand' ) && get_term_by( 'slug', 'erfansanat-brand', 'product_brand' ) instanceof WP_Term );

	// SC-01: tag archive renders.
	$GLOBALS['wp_query']->queried_object    = get_term( $tid, 'product_tag' );
	$GLOBALS['wp_query']->queried_object_id = $tid;
	$GLOBALS['wp_query']->is_tax            = true;
	$GLOBALS['wp_query']->query_vars['taxonomy'] = 'product_tag';
	$GLOBALS['wp_query']->query_vars['term']     = 'waterproof';
	ob_start();
	include ES_THEME . '/woocommerce/taxonomy-product_tag.php';
	$html_tag = (string) ob_get_clean();
	es_check( 'SC-01: آرشیو برچسب رندر می‌شود (200)', false !== strpos( $html_tag, 'id="archive"' ) && false !== strpos( $html_tag, 'data-products-archive="true"' ) );
	es_check( 'AC-02: نام/توضیحات برچسب در hero', false !== strpos( $html_tag, 'ضدآب' ) && false !== strpos( $html_tag, 'محصولات ضدآب IP68.' ) );
	es_check( 'AC-03: کارت استاندارد محصول', 2 === substr_count( $html_tag, 'data-product-item="true"' ) );
	$routed_tag = apply_filters( 'template_include', '/default.php' );
	es_check( 'AC-01: routing → taxonomy-product_tag.php', false !== strpos( $routed_tag, 'woocommerce/taxonomy-product_tag.php' ) );

	// SC-01: brand archive renders (shell with brand query, avoids double header).
	$GLOBALS['wp_query']->queried_object    = get_term( $bid, 'product_brand' );
	$GLOBALS['wp_query']->queried_object_id = $bid;
	$GLOBALS['wp_query']->query_vars['taxonomy'] = 'product_brand';
	$GLOBALS['wp_query']->query_vars['term']     = 'erfansanat-brand';
	$routed_brand = apply_filters( 'template_include', '/default.php' );
	es_check( 'AC-01: routing → taxonomy-product_brand.php', false !== strpos( $routed_brand, 'woocommerce/taxonomy-product_brand.php' ) );
	$brand_q = new WP_Query( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'tax_query' => array( array( 'taxonomy' => 'product_brand', 'field' => 'term_id', 'terms' => $bid ) ) ) );
	ob_start();
	get_template_part( 'template-parts/archive/product', 'archive-shell', array( 'query' => $brand_q, 'heading_gradient' => 'عرفان صنعت', 'toolbar_title' => 'محصولات برند عرفان صنعت', 'show_hero' => false ) );
	$html_brand = (string) ob_get_clean();
	es_check( 'SC-01: آرشیو برند کارت‌ها را رندر می‌کند', 2 === substr_count( $html_brand, 'data-product-item="true"' ) );
	es_check( 'AC-02: عنوان برند درست', false !== strpos( $html_brand, 'محصولات برند عرفان صنعت' ) );

	foreach ( array( $p1, $p2 ) as $p ) {
		wp_delete_post( $p, true );
	}
	wp_delete_term( $tid, 'product_tag' );
	wp_delete_term( $bid, 'product_brand' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
