<?php
/**
 * P05-T021 — رگرسیون جامع صفحه اصلی: کوئری/ترتیب/خاموش‌سازی/حالت خالی
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-021.php
 *
 * AC-01: query budget (<= 60 queries per full home render; measured baseline
 *        is 11 empty / 18 with fixtures — the budget catches N+1 regressions).
 * AC-02: no hardcoded product/project/post/service content (empty DB -> empty
 *        states, zero cards; with fixtures -> DB titles render).
 * AC-03 / SC-01: every section's empty state is safe; empty data never fatals.
 * SC-02: admin reorder + disable are honored in the output.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;
use ErfanSanat\Theme\Sections\Schema;
use ErfanSanat\Theme\Sections\SectionManager;

es_task( 'P05-T021', 'رگرسیون جامع صفحه اصلی' );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	global $wpdb;

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$es_types = array( 'product', 'projects', 'services', 'post' );
	$clean = static function () use ( $es_types ) {
		foreach ( $es_types as $pt ) {
			foreach ( get_posts( array( 'post_type' => $pt, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) ) as $id ) {
				wp_delete_post( $id, true );
			}
		}
	};
	$clean();

	$front_id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'خانه', 'post_name' => 'home' ) );
	$old_show = get_option( 'show_on_front' );
	$old_page = get_option( 'page_on_front' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front_id );

	$render = static function () use ( $front_id ) {
		Renderer::invalidate( $front_id );
		return Renderer::render( $front_id, 'home' );
	};

	// ── SC-01 / AC-03: empty data renders safely, all 13 sections ─────────.
	$empty = $render();
	es_check( 'SC-01: داده خالی fatal نمی‌دهد (خروجی تولید شد)', strlen( $empty ) > 1000 );
	es_check( 'AC-03: هر ۱۳ سکشن با داده خالی رندر می‌شوند', 13 === preg_match_all( '/data-es-section="/', $empty ) );
	es_check( 'AC-03: حالت خالی محصولات', false !== strpos( $empty, 'محصولی برای نمایش وجود ندارد' ) );
	es_check( 'AC-03: حالت خالی پروژه‌ها', false !== strpos( $empty, 'پروژه‌ای برای نمایش در این دسته وجود ندارد' ) );
	es_check( 'AC-03: حالت خالی خدمات', false !== strpos( $empty, 'خدمتی برای نمایش وجود ندارد' ) );
	es_check( 'AC-03: حالت خالی مقالات', false !== strpos( $empty, 'مقاله‌ای برای نمایش وجود ندارد' ) );

	// ── AC-02: no hardcoded product/project/post/service content ──────────.
	es_check( 'AC-02: هیچ کارت محصول هاردکد نیست', 0 === substr_count( $empty, 'data-product-item="true"' ) );
	es_check( 'AC-02: هیچ کارت پروژه هاردکد نیست', 0 === substr_count( $empty, 'data-project-item="true"' ) );
	es_check( 'AC-02: هیچ ردیف خدمت هاردکد نیست', 0 === preg_match_all( '/data-service-row="\d+"/', $empty ) );
	es_check( 'AC-02: هیچ کارت مقاله هاردکد نیست', 0 === substr_count( $empty, 'card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]' ) );
	es_check( 'AC-02: عنوان مقاله مرجع هاردکد نشده', false === strpos( $empty, 'آشنایی جامع با انواع تونل نوری' ) );

	// ── AC-01: query budget with empty data ───────────────────────────────.
	Renderer::invalidate( $front_id );
	$q0 = $wpdb->num_queries;
	$render();
	$empty_queries = $wpdb->num_queries - $q0;
	es_check( 'AC-01: بودجه کوئری داده خالی (<=60؛ واقعی ' . $empty_queries . ')', $empty_queries <= 60 );

	// ── Fixtures: products/projects/services/posts with thumbnails ────────.
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'img', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/x.jpg' );
	$cat = wp_insert_term( 'دسته تست', 'product_cat', array( 'slug' => 'test-cat' ) );
	for ( $i = 0; $i < 8; $i++ ) {
		$p = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول آزمایشی ' . $i ) );
		set_post_thumbnail( $p, $att );
		update_post_meta( $p, '_stock_status', 'instock' );
		update_post_meta( $p, '_price', '1000' );
		if ( ! is_wp_error( $cat ) ) { wp_set_object_terms( $p, array( (int) $cat['term_id'] ), 'product_cat' ); }
	}
	for ( $i = 0; $i < 6; $i++ ) {
		$j = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه آزمایشی ' . $i ) );
		set_post_thumbnail( $j, $att );
	}
	for ( $i = 0; $i < 5; $i++ ) {
		wp_insert_post( array( 'post_type' => 'services', 'post_status' => 'publish', 'post_title' => 'خدمت آزمایشی ' . $i, 'post_excerpt' => 'توضیح خدمت' ) );
	}
	for ( $i = 0; $i < 3; $i++ ) {
		$b = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله آزمایشی ' . $i, 'post_content' => 'متن مقاله برای زمان مطالعه' ) );
		set_post_thumbnail( $b, $att );
	}

	// ── AC-01: query budget with data ─────────────────────────────────────.
	Renderer::invalidate( $front_id );
	$q0 = $wpdb->num_queries;
	$full = Renderer::render( $front_id, 'home' );
	$full_queries = $wpdb->num_queries - $q0;
	es_check( 'AC-01: بودجه کوئری با داده (<=60؛ واقعی ' . $full_queries . ')', $full_queries <= 60 );

	// ── AC-02: content comes from the DB ──────────────────────────────────.
	es_check( 'AC-02: محصولات از دیتابیس می‌آیند', 8 === substr_count( $full, 'data-product-item="true"' ) && false !== strpos( $full, 'محصول آزمایشی 0' ) );
	es_check( 'AC-02: خدمات از دیتابیس می‌آیند', 5 === preg_match_all( '/data-service-row="\d+"/', $full ) && false !== strpos( $full, 'خدمت آزمایشی 0' ) );
	es_check( 'AC-02: مقالات از دیتابیس می‌آیند', false !== strpos( $full, 'مقاله آزمایشی 0' ) );
	es_check( 'AC-02: پروژه‌ها از دیتابیس می‌آیند', false !== strpos( $full, 'پروژه آزمایشی' ) );

	// ── SC-02: reorder (products first) + disable (about) ─────────────────.
	$config = Schema::defaults( 'home' );
	$products_idx = null;
	foreach ( $config as $i => $s ) {
		if ( 'products' === $s['type'] ) { $products_idx = $i; }
		if ( 'about' === $s['type'] ) { $config[ $i ]['enabled'] = false; }
	}
	$products_section = $config[ $products_idx ];
	array_splice( $config, $products_idx, 1 );
	array_unshift( $config, $products_section );
	SectionManager::save_sections( $front_id, $config, 'home' );
	$reordered = Renderer::render( $front_id, 'home' );
	$p_products = strpos( $reordered, 'data-es-section="products"' );
	$p_hero     = strpos( $reordered, 'data-es-section="hero"' );
	es_check( 'SC-02: تغییر ترتیب اعمال می‌شود (products قبل از hero)', false !== $p_products && false !== $p_hero && $p_products < $p_hero );
	es_check( 'SC-02: خاموش‌سازی about اعمال می‌شود', false === strpos( $reordered, 'data-es-section="about"' ) );
	es_check( 'SC-02: سکشن خاموش‌شده حذف می‌شود (۱۲ سکشن)', 12 === preg_match_all( '/data-es-section="/', $reordered ) );

	// Restore defaults + cleanup.
	delete_post_meta( $front_id, Schema::meta_key() );
	Renderer::invalidate( $front_id );
	$restored = Renderer::render( $front_id, 'home' );
	es_check( 'بازگردانی پیش‌فرض‌ها (۱۳ سکشن، about فعال)', 13 === preg_match_all( '/data-es-section="/', $restored ) && false !== strpos( $restored, 'data-es-section="about"' ) );

	$clean();
	if ( ! is_wp_error( $cat ) ) { wp_delete_term( (int) $cat['term_id'], 'product_cat' ); }
	wp_delete_post( $att, true );
	update_option( 'show_on_front', $old_show );
	update_option( 'page_on_front', $old_page );
	wp_delete_post( $front_id, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
