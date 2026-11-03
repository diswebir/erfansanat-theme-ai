<?php
/**
 * P02-T014 — تب تنظیمات آرشیو
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Tabs\ArchiveTab;
use ErfanSanat\Theme\Options\Store;
use ErfanSanat\Theme\Options\Schema;

es_task( 'P02-T014', 'تب تنظیمات آرشیو' );

es_check( 'target: inc/Options/Tabs/ArchiveTab.php', es_file( 'inc/Options/Tabs/ArchiveTab.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	Store::reset();

	// AC-01: schema defines posts_per_page + pagination type.
	es_check( 'AC-01: shop_posts_per_page از نوع number', 'number' === Schema::get_field( 'shop_posts_per_page' )['type'] );
	es_check( 'AC-01: pagination_type از نوع select', 'select' === Schema::get_field( 'pagination_type' )['type'] );
	es_check( 'AC-01: hook pre_get_posts ثبت شده', false !== has_action( 'pre_get_posts', 'ErfanSanat\\Theme\\Options\\Tabs\\ArchiveTab::apply_to_query' ) );

	// AC-02: invalid value falls back to the schema default.
	Store::set( 'shop_posts_per_page', 0 );
	es_check( 'AC-02: مقدار نامعتبر به پیش‌فرض برمی‌گردد', (int) Schema::default( 'shop_posts_per_page' ) === ArchiveTab::posts_per_page_for( 'shop_posts_per_page' ) );
	Store::reset();

	// SC-01 / AC-03: the main archive query picks up the configured card count.
	$original_main = isset( $GLOBALS['wp_the_query'] ) ? $GLOBALS['wp_the_query'] : null;

	Store::set( 'blog_posts_per_page', 5 );
	$query           = new WP_Query();
	$query->is_archive = true;
	$GLOBALS['wp_the_query'] = $query;
	ArchiveTab::apply_to_query( $query );
	es_check( 'SC-01: تعداد کارت آرشیو عوض می‌شود', 5 === (int) $query->get( 'posts_per_page' ) );

	Store::set( 'blog_posts_per_page', 8 );
	$query2           = new WP_Query();
	$query2->is_archive = true;
	$GLOBALS['wp_the_query'] = $query2;
	ArchiveTab::apply_to_query( $query2 );
	es_check( 'SC-01: تغییر تعداد دوباره اعمال می‌شود', 8 === (int) $query2->get( 'posts_per_page' ) );

	if ( null !== $original_main ) {
		$GLOBALS['wp_the_query'] = $original_main;
	}

	// SC-02: the default shop sort/filter is applied.
	Store::set( 'shop_default_sort', 'price_asc' );
	$sort = ArchiveTab::shop_sort_args();
	es_check( 'SC-02: فیلتر پیش‌فرض (مرتب‌سازی قیمت) اعمال می‌شود', 'meta_value_num' === $sort['orderby'] && '_price' === $sort['meta_key'] && 'ASC' === $sort['order'] );
	Store::set( 'shop_default_sort', 'latest' );
	es_check( 'SC-02: مرتب‌سازی پیش‌فرض جدیدترین', 'date' === ArchiveTab::shop_sort_args()['orderby'] );

	Store::reset();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
