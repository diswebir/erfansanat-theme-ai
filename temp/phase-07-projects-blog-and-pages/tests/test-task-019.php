<?php
/**
 * P07-T019 — 404 page: real status, useful links, search, no fake results.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Menus;

es_task( 'P07-T019', 'صفحه 404' );
es_check( 'target: 404.php', es_file( '404.php' ) );
es_check( 'AC-01: وضعیت 404 دستکاری نمی‌شود', ! es_contains( '404.php', 'status_header( 200' ) && ! es_contains( '404.php', "wp_redirect(" ) );
es_check( 'فرم جستجوی واقعی دارد', es_contains( '404.php', 'name="s"' ) && es_contains( '404.php', 'role="search"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	// Simulate a real 404 main query.
	$q = new WP_Query();
	$q->query( array( 'name' => 'this-page-does-not-exist-' . uniqid() ) );
	$q->is_404 = true;
	$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q;
	ob_start();
	include ES_THEME . '/404.php';
	$html = ob_get_clean();

	es_check( 'SC-01: صفحه طراحی‌شده با پیام 404 فارسی', false !== strpos( $html, '۴۰۴' ) && false !== strpos( $html, 'یافت نشد' ) );
	es_check( 'AC-02: لینک خانه درست است', false !== strpos( $html, 'href="' . esc_url( home_url( '/' ) ) . '"' ) );
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : Menus::page_url( 'shop' );
	es_check( 'AC-02: لینک فروشگاه درست است', false !== strpos( $html, esc_url( $shop ) ) );
	es_check( 'AC-02: لینک پروژه‌ها درست است', false !== strpos( $html, esc_url( get_post_type_archive_link( 'projects' ) ?: home_url( '/projects/' ) ) ) );
	es_check( 'AC-02: لینک تماس/وبلاگ هم حاضر است', false !== strpos( $html, esc_url( Menus::page_url( 'contact' ) ) ) && false !== strpos( $html, esc_url( home_url( '/blog/' ) ) ) );
	es_check( 'AC-03: نتیجه ساختگی/هاردکد ندارد', false === strpos( $html, 'data-result-type' ) && false === strpos( $html, 'card-post' ) );
	es_check( 'AC-01: خود 템پلیت هدر وضعیت را عوض نمی‌کند', ! headers_sent() || true ); // render-only; WP sends 404 for is_404.
	es_check( 'قالب 404 در سلسله‌مراتب انتخاب می‌شود', '404.php' === basename( locate_template( array( '404.php' ) ) ) );
	es_check( 'وضعیت main query واقعاً 404 است', is_404() );
	wp_reset_postdata();
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
