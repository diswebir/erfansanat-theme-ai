<?php
/**
 * P08-T019 — SEO audit: single title/canonical, valid JSON-LD
 * (Organization/Product/Article), non-conflicting hreflang, rank_math meta
 * preserved when an SEO plugin is active.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Seo;

ob_start();

es_task( 'P08-T019', 'ممیزی SEO و Schema' );

es_check( 'target: inc/Core/Seo.php', es_file( 'inc/Core/Seo.php' ) );
es_check( 'boot در functions.php', es_contains( 'functions.php', 'Core\\Seo::boot' ) );
es_check( 'AC-01: header.php تگ title دستی ندارد', ! es_contains( 'header.php', '<title>' ) );
es_check( 'AC-01: title-tag در Theme فعال است', es_contains( 'inc/Core/Theme.php', "add_theme_support( 'title-tag' )" ) );
es_check( 'AC-01: canonical فقط بدون پلاگین SEO', es_contains( 'inc/Core/Seo.php', 'plugin_active()' ) );
es_check( 'AC-02: سازنده‌های Schema سه‌گانه', es_contains( 'inc/Core/Seo.php', "=> 'Organization'" ) && es_contains( 'inc/Core/Seo.php', "=> 'Product'" ) && es_contains( 'inc/Core/Seo.php', "=> 'Article'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';

	// Pure builders (AC-02): valid JSON-LD, @context + @type present.
	$es_org = Seo::organization_schema( array( 'name' => 'عرفان صنعت', 'url' => home_url( '/' ) ) );
	es_check( 'AC-02: Organization معتبر', isset( $es_org['@context'], $es_org['@type'], $es_org['name'], $es_org['url'] ) && 'Organization' === $es_org['@type'] );
	$es_article = Seo::article_schema( array(
		'headline'      => 'مقاله تست',
		'url'           => home_url( '/post-1' ),
		'datePublished' => '2026-11-01T00:00:00+00:00',
		'author'        => 'نویسنده',
	) );
	es_check( 'AC-02: Article معتبر', isset( $es_article['@context'], $es_article['@type'], $es_article['headline'], $es_article['datePublished'], $es_article['author'] ) && 'Article' === $es_article['@type'] );
	$es_product = Seo::product_schema( array(
		'name'       => 'چراغ تست',
		'url'        => home_url( '/product-1' ),
		'price'      => '150000',
		'currency'   => 'IRR',
		'availability' => 'InStock',
	) );
	es_check( 'AC-02: Product با offers معتبر', isset( $es_product['@type'], $es_product['offers']['@type'], $es_product['offers']['price'] ) && 'Product' === $es_product['@type'] && 'Offer' === $es_product['offers']['@type'] );
	es_check( 'AC-02: JSON همه سازنده‌ها encode معتبر', null !== json_encode( array( $es_org, $es_article, $es_product ), JSON_UNESCAPED_UNICODE ) );

	// Head emulation helper: swap the global query, capture wp_head, restore.
	$es_render_head = static function ( array $es_args ) {
		global $wp_query;
		$es_saved        = $wp_query;
		$es_q            = new WP_Query( $es_args );
		$GLOBALS['wp_query'] = $es_q;
		$es_posts        = $es_q->posts ?? array();
		$GLOBALS['post'] = $es_posts[0] ?? null; // get_the_title/get_permalink/get_post_type read the global.
		Seo::reset_printed();
		ob_start();
		do_action( 'wp_head' );
		$es_html = (string) ob_get_clean();
		$GLOBALS['wp_query'] = $es_saved;
		unset( $GLOBALS['post'] );
		return $es_html;
	};
	$es_count = static function ( string $es_html, string $es_needle ): int {
		return substr_count( $es_html, $es_needle );
	};
	$es_ld_types = static function ( string $es_html ): array {
		$es_types = array();
		if ( preg_match_all( '/application\/ld\+json[^>]*>(.*?)<\/script>/s', $es_html, $es_m ) ) {
			foreach ( $es_m[1] as $es_raw ) {
				$es_data = json_decode( trim( $es_raw ), true );
				if ( is_array( $es_data ) && isset( $es_data['@type'] ) ) {
					$es_types[] = (string) $es_data['@type'];
				}
			}
		}
		return $es_types;
	};

	// Fixtures: front page + article post.
	$es_page_id = (int) wp_insert_post( array( 'post_title' => 'T019 خانه', 'post_status' => 'publish', 'post_type' => 'page' ) );
	$es_post_id = (int) wp_insert_post( array( 'post_title' => 'T019 مقاله', 'post_status' => 'publish', 'post_type' => 'post', 'post_date' => '2026-11-01 10:00:00' ) );
	$es_show    = get_option( 'show_on_front' );
	$es_pof     = get_option( 'page_on_front' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $es_page_id );

	// SC-01: home head — exactly one canonical (core owns singular/front), one Organization JSON-LD.
	$es_home = $es_render_head( array( 'page_id' => $es_page_id ) );
	$es_c    = $es_count( $es_home, 'rel="canonical"' );
	es_check( "SC-01: صفحه اصلی دقیقاً یک canonical (got=$es_c)", 1 === $es_c );
	es_check( 'SC-01: صفحه اصلی JSON-LD سازمان دارد', array( 'Organization' ) === array_values( array_intersect( $es_ld_types( $es_home ), array( 'Organization', 'Article', 'Product' ) ) ) );
	// AC-01: rendering twice never duplicates the canonical/JSON-LD blocks.
	$es_home2 = $es_render_head( array( 'page_id' => $es_page_id ) );
	es_check( 'AC-01: رندر تکراری canonical/JSON-LD تکراری نمی‌سازد', 1 === $es_count( $es_home2, 'rel="canonical"' ) && 1 === $es_count( $es_home2, 'application/ld+json' ) );

	// AC-01: theme fills the canonical gap on term archives (core prints none).
	$es_cat_term = get_term_by( 'name', 'T019 دسته', 'category' ); // Idempotent: leftovers from aborted runs.
	$es_cat_id   = $es_cat_term ? (int) $es_cat_term->term_id : (int) wp_insert_term( 'T019 دسته', 'category' )['term_id'];
	$es_cat_q   = new WP_Query( array( 'cat' => $es_cat_id ) );
	$GLOBALS['wp_query'] = $es_cat_q;
	$GLOBALS['post']     = null;
	$es_term_url  = Seo::canonical_url();
	$GLOBALS['wp_query'] = new WP_Query( array() );
	es_check( 'AC-01: آرشیو ترم canonical از تم', false !== strpos( (string) $es_term_url, 'category' ) && ! is_wp_error( $es_term_url ) );

	// SC-01: article head — canonical = permalink + valid Article JSON-LD.
	$es_art = $es_render_head( array( 'p' => $es_post_id, 'post_type' => 'post' ) );
	es_check( 'SC-01: مقاله دقیقاً یک canonical به پیوند یکتا', 1 === $es_count( $es_art, 'rel="canonical"' ) && false !== strpos( $es_art, esc_url( (string) get_permalink( $es_post_id ) ) ) );
	$es_art_types = $es_ld_types( $es_art );
	es_check( 'SC-01: مقاله Article JSON-LD معتبر', in_array( 'Article', $es_art_types, true ) );

	// SC-01: product head via the es_seo_product_data integration filter.
	$es_prod_filter = static function () {
		return array( 'name' => 'چراغ حبابی', 'url' => home_url( '/product/charm' ), 'price' => '150000', 'currency' => 'IRR', 'availability' => 'InStock' );
	};
	add_filter( 'es_seo_product_data', $es_prod_filter );
	$es_prod = $es_render_head( array( 'p' => $es_post_id, 'post_type' => 'post' ) ); // singular context; product data comes from the filter.
	remove_filter( 'es_seo_product_data', $es_prod_filter );
	$es_prod_types = $es_ld_types( $es_prod );
	es_check( 'SC-01: محصول Product JSON-LD با قیمت', in_array( 'Product', $es_prod_types, true ) && false !== strpos( $es_prod, '"price":"150000"' ) );

	// AC-03: hreflang tags unique + valid langs (delegated to Core\Hreflang).
	$es_href_post = (int) wp_insert_post( array( 'post_title' => 'T019 hreflang', 'post_status' => 'publish', 'post_type' => 'post' ) );
	update_post_meta( $es_href_post, 'hrefmeta_fa', home_url( '/fa/t019' ) );
	update_post_meta( $es_href_post, 'hrefmeta_en', home_url( '/en/t019' ) );
	update_post_meta( $es_href_post, 'hrefmeta_ar', home_url( '/ar/t019' ) );
	$es_h_html = $es_render_head( array( 'p' => $es_href_post, 'post_type' => 'post' ) );
	preg_match_all( '/hreflang="([^"]+)"/', $es_h_html, $es_langs );
	$es_lang_list = $es_langs[1] ?? array();
	es_check( 'AC-03: hreflang زبان‌های معتبر بدون تکرار', ! empty( $es_lang_list ) && count( $es_lang_list ) === count( array_unique( $es_lang_list ) ) && array( 'fa', 'en', 'ar', 'x-default' ) === $es_lang_list );

	// SC-02: with an active SEO plugin the theme prints no canonical but
	// foreign (rank_math) meta stays untouched.
	add_filter( 'es_seo_plugin_active', '__return_true' );
	$es_rank_printed = false;
	$es_rank_hook    = static function () use ( &$es_rank_printed ) {
		$es_rank_printed = true;
		echo '<meta name="rank_math_template" content="t019" />' . "\n";
	};
	add_action( 'wp_head', $es_rank_hook, 15 );
	$es_plugin_head = $es_render_head( array( 'p' => $es_post_id, 'post_type' => 'post' ) );
	remove_action( 'wp_head', $es_rank_hook, 15 );
	remove_filter( 'es_seo_plugin_active', '__return_true' );
	es_check( 'SC-02: پلاگین فعال → هیچ canonical (تم + هسته حذف)', 0 === $es_count( $es_plugin_head, 'rel="canonical"' ) );
	es_check( 'SC-02: متای rank_math حفظ می‌شود', $es_rank_printed && false !== strpos( $es_plugin_head, 'rank_math_template' ) );

	// Teardown.
	update_option( 'show_on_front', $es_show );
	update_option( 'page_on_front', $es_pof );
	wp_delete_term( $es_cat_id, 'category' );
	wp_delete_post( $es_page_id, true );
	wp_delete_post( $es_post_id, true );
	wp_delete_post( $es_href_post, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
