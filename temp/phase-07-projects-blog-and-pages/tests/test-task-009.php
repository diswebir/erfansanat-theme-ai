<?php
/**
 * P07-T009 — main blog archive, real published posts and functional DOM controls.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Store;

es_task( 'P07-T009', 'آرشیو اصلی وبلاگ' );
es_check( 'target: home.php', es_file( 'home.php' ) );
es_check( 'target: blog archive shell', es_file( 'template-parts/archive/blog-shell.php' ) );
es_check( 'AC-01: id=articles و قرارداد مرجع', es_contains( 'template-parts/archive/blog-shell.php', 'id="articles"' ) && es_contains( 'template-parts/archive/blog-shell.php', 'data-blog-archive="true"' ) );
es_check( 'AC-02: کوئری post منتشرشده', 1 === preg_match( '/[\'\"]post_type[\'\"]\s*=>\s*[\'\"]post[\'\"]/', (string) file_get_contents( ES_THEME . '/home.php' ) ) && 1 === preg_match( '/[\'\"]post_status[\'\"]\s*=>\s*[\'\"]publish[\'\"]/', (string) file_get_contents( ES_THEME . '/home.php' ) ) );
es_check( 'AC-03: جستجو و صفحه‌بندی واقعی', es_contains( 'template-parts/archive/blog-hero.php', 'data-blog-search="true"' ) && es_contains( 'template-parts/archive/blog-shell.php', "get_template_part( 'template-parts/archive/pagination'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$old_options   = get_option( Store::OPTION, array() );
	$old_front     = get_option( 'show_on_front' );
	$old_page      = get_option( 'page_on_front' );
	$old_blog      = get_option( 'page_for_posts' );
	$old_permalink = get_option( 'permalink_structure' );
	$cat = es_insert_term( 'مجله علمی', 'category', array( 'slug' => 'magazine-' . uniqid() ) )['term_id'];
	$news = es_insert_term( 'اخبار و رویداد ها', 'category', array( 'slug' => 'news-' . uniqid() ) )['term_id'];
	$ids = array();
	for ( $i = 1; $i <= 7; $i++ ) {
		$id = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله آزمایشی ' . $i, 'post_excerpt' => 'راهنمای نورپردازی شهری شماره ' . $i, 'post_name' => 'blog-009-' . $i . '-' . uniqid() ) );
		wp_set_object_terms( $id, array( 1 === $i ? (int) $news : (int) $cat ), 'category' );
		$ids[] = $id;
	}
	$draft = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'draft', 'post_title' => 'مقاله محرمانه پیش‌نویس', 'post_name' => 'draft-009-' . uniqid() ) );
	$page = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'برگه نباید در وبلاگ باشد', 'post_name' => 'page-009-' . uniqid() ) );
	$all = array_merge( $ids, array( $draft, $page ) );
	$filter = static function ( $args ) use ( $all ) { $args['post__in'] = $all; $args['orderby'] = 'post__in'; return $args; };
	add_filter( 'es_blog_archive_query_args', $filter );
	update_option( Store::OPTION, array_merge( is_array( $old_options ) ? $old_options : array(), array( 'blog_posts_per_page' => 4 ) ) );

	$query = new WP_Query( apply_filters( 'es_blog_archive_query_args', array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => -1, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) ) );
	ob_start();
	get_template_part( 'template-parts/archive/blog-shell', null, array( 'query' => $query, 'per_page' => 4 ) );
	$html = (string) ob_get_clean();
	$start = strpos( $html, 'data-blog-grid="true"' );
	$end = strpos( $html, 'data-pagination-root="true"' );
	$grid = substr( $html, $start, $end - $start );
	es_check( 'SC-01: هفت کارت مقاله واقعی رندر شد', 7 === substr_count( $grid, 'card-light group flex h-full' ) );
	es_check( 'AC-02: پیش‌نویس و برگه حذف شدند', false === strpos( $grid, 'مقاله محرمانه پیش‌نویس' ) && false === strpos( $grid, 'برگه نباید در وبلاگ باشد' ) );
	es_check( 'AC-01: عنوان و گرید مرجع', false !== strpos( $html, 'اخبار و مقالات' ) && false !== strpos( $html, 'data-blog-heading="true"' ) && false !== strpos( $html, 'grid gap-6 sm:grid-cols-2 lg:grid-cols-2' ) );
	es_check( 'AC-03: دسته‌های واقعی و دکمه همه', false !== strpos( $html, 'data-blog-cat-btn="همه"' ) && false !== strpos( $html, 'data-blog-cat-btn="مجله علمی"' ) && false !== strpos( $html, 'data-blog-cat-btn="اخبار و رویداد ها"' ) );
	es_check( 'AC-03: متن قابل جستجوی هر کارت', 7 === substr_count( $grid, 'data-blog-search-text=' ) && false !== strpos( $grid, 'راهنمای نورپردازی شهری شماره 1' ) );
	es_check( 'SC-02: صفحه‌بندی ۲ صفحه با اندازه ۴', 2 === substr_count( $html, 'data-page-btn=' ) && false !== strpos( $html, 'data-per-page="4"' ) );
	es_check( 'AC-03: صفحه قبل/بعد و بازنشانی فیلتر', false !== strpos( $html, 'data-page-prev="true"' ) && false !== strpos( $html, 'data-page-next="true"' ) && false !== strpos( $html, 'data-blog-reset="true"' ) );
	es_check( 'منتخب سردبیر از نوشته واقعی منتشرشده', false !== strpos( $html, '★ منتخب سردبیر' ) && false !== strpos( $html, esc_url( get_permalink( $ids[0] ) ) ) );

	// Real WordPress posts page resolves /blog/ and uses home.php.
	$front = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'خانه آزمایشی', 'post_name' => 'home-009-' . uniqid() ) );
	$blog = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'وبلاگ', 'post_name' => 'blog' ) );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front );
	update_option( 'page_for_posts', $blog );
	update_option( 'permalink_structure', '/%postname%/' );
	$GLOBALS['wp_rewrite']->init();
	flush_rewrite_rules( false );
	$_SERVER['REQUEST_URI'] = '/blog/';
	$_SERVER['REQUEST_METHOD'] = 'GET';
	$_SERVER['PATH_INFO'] = '';
	$_SERVER['PHP_SELF'] = '/index.php';
	$_SERVER['QUERY_STRING'] = '';
	wp();
	$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
	es_check( 'SC-01: مسیر /blog/ آرشیو نوشته است', is_home() && 'home.php' === basename( locate_template( array( 'home.php', 'index.php' ), false ) ) );
	ob_start();
	include ES_THEME . '/home.php';
	$full = (string) ob_get_clean();
	es_check( 'SC-01: قالب کامل وبلاگ کارت‌های فیکسچر را دارد', false !== strpos( $full, 'id="articles"' ) && false !== strpos( $full, 'مقاله آزمایشی 1' ) && false === strpos( $full, 'مقاله محرمانه پیش‌نویس' ) );

	remove_filter( 'es_blog_archive_query_args', $filter );
	add_filter( 'es_blog_archive_query_args', static function ( $args ) { $args['post__in'] = array( 0 ); return $args; } );
	$empty = new WP_Query( apply_filters( 'es_blog_archive_query_args', array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => -1 ) ) );
	ob_start();
	get_template_part( 'template-parts/archive/blog-shell', null, array( 'query' => $empty ) );
	$empty_html = (string) ob_get_clean();
	es_check( 'حالت خالی وبلاگ ایمن است', false !== strpos( $empty_html, 'data-blog-empty="true"' ) && false === strpos( $empty_html, 'data-blog-item="true"' ) && false === strpos( $empty_html, '★ منتخب سردبیر' ) );

	foreach ( array_merge( $all, array( $front, $blog ) ) as $id ) { wp_delete_post( $id, true ); }
	wp_delete_term( $cat, 'category' );
	wp_delete_term( $news, 'category' );
	update_option( Store::OPTION, $old_options );
	update_option( 'show_on_front', $old_front );
	update_option( 'page_on_front', $old_page );
	update_option( 'page_for_posts', $old_blog );
	update_option( 'permalink_structure', $old_permalink );
	$GLOBALS['wp_rewrite']->init();
	flush_rewrite_rules( false );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
