<?php
/**
 * P07-T010 — category and general blog archives, real term/date/author context.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T010', 'آرشیو دسته/عمومی وبلاگ' );
es_check( 'target: category.php', es_file( 'category.php' ) );
es_check( 'target: archive.php', es_file( 'archive.php' ) );
es_check( 'AC-01: عنوان از ترم واقعی، نه عنوان undefined مرجع', es_contains( 'category.php', 'get_queried_object()' ) && es_contains( 'category.php', '$es_term->name' ) );
es_check( 'AC-02: توضیحات ترم به shell منتقل می‌شود', es_contains( 'category.php', '$es_term->description' ) && es_contains( 'template-parts/archive/blog-hero.php', 'data-term-description="true"' ) );
es_check( 'AC-03: کارت استاندارد مقاله', es_contains( 'template-parts/archive/blog-shell.php', "get_template_part( 'template-parts/cards/card-post'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$cat = es_insert_term( 'مجله علمی', 'category', array( 'slug' => 'magazine-010-' . uniqid(), 'description' => 'راهنمای فنی و استانداردهای نورپردازی شهری' ) )['term_id'];
	$other = es_insert_term( 'اخبار دیگر', 'category', array( 'slug' => 'news-010-' . uniqid() ) )['term_id'];
	$ids = array();
	for ( $i = 1; $i <= 2; $i++ ) {
		$id = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله دسته ' . $i, 'post_name' => 'cat-010-' . $i . '-' . uniqid(), 'post_date' => '2025-08-03 10:00:00' ) );
		wp_set_object_terms( $id, array( (int) $cat ), 'category' );
		$ids[] = $id;
	}
	$outside = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله خارج از دسته', 'post_name' => 'outside-010-' . uniqid() ) );
	wp_set_object_terms( $outside, array( (int) $other ), 'category' );
	$draft = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'draft', 'post_title' => 'پیش‌نویس محرمانه دسته', 'post_name' => 'draft-010-' . uniqid() ) );
	wp_set_object_terms( $draft, array( (int) $cat ), 'category' );
	$all = array_merge( $ids, array( $outside, $draft ) );
	$scope = static function ( $args ) use ( $all ) { $args['post__in'] = $all; return $args; };
	add_filter( 'es_blog_category_query_args', $scope );
	add_filter( 'es_blog_archive_query_args', $scope );

	$set_query = static function ( $args ) {
		$q = new WP_Query( $args );
		$GLOBALS['wp_query'] = $q;
		$GLOBALS['wp_the_query'] = $q;
		return $q;
	};
	$grid_of = static function ( $html ) {
		$a = strpos( $html, 'data-blog-grid="true"' );
		$b = strpos( $html, 'data-pagination-root="true"' );
		return false !== $a && false !== $b ? substr( $html, $a, $b - $a ) : '';
	};
	$set_query( array( 'cat' => (int) $cat, 'post_type' => 'post', 'post_status' => 'publish' ) );
	ob_start();
	include ES_THEME . '/category.php';
	$html = ob_get_clean();
	$grid = $grid_of( $html );
	es_check( 'SC-01: دسته فارسی مجله علمی رندر شد', is_category() && get_queried_object()->term_id === $cat && false !== strpos( $html, '<h1' ) && false !== strpos( $html, 'مجله علمی' ) );
	es_check( 'AC-01: عنوان ترم یک بار در هیرو/تولبار و بدون undefined', false !== strpos( $html, 'آرشیو مطالب «مجله علمی»' ) && false === strpos( $html, 'undefined' ) );
	es_check( 'AC-02: توضیح واقعی ترم رندر شد', false !== strpos( $html, 'data-term-description="true"' ) && false !== strpos( $html, 'راهنمای فنی و استانداردهای نورپردازی شهری' ) );
	es_check( 'AC-03: فقط دو کارت منتشرشده همین دسته', 2 === substr_count( $grid, 'card-light group flex h-full' ) && false === strpos( $grid, 'مقاله خارج از دسته' ) && false === strpos( $grid, 'پیش‌نویس محرمانه دسته' ) );
	es_check( 'SC-02: لینک دسته واقعی است', false !== strpos( $html, esc_url( get_category_link( $cat ) ) ) );

	$set_query( array( 'year' => 2025, 'monthnum' => 8, 'day' => 3, 'post_type' => 'post', 'post_status' => 'publish' ) );
	ob_start();
	include ES_THEME . '/archive.php';
	$date_html = ob_get_clean();
	es_check( 'SC-02: آرشیو تاریخ ایمن و دارای کارت است', is_date() && false !== strpos( $date_html, 'id="articles"' ) && 2 === substr_count( $grid_of( $date_html ), 'card-light group flex h-full' ) && false === strpos( $date_html, 'undefined' ) );

	$set_query( array( 'author' => 1, 'post_type' => 'post', 'post_status' => 'publish' ) );
	ob_start();
	include ES_THEME . '/archive.php';
	$author_html = ob_get_clean();
	$author = get_userdata( 1 );
	es_check( 'SC-02: آرشیو نویسنده عنوان درست و بدون fatal', is_author() && false !== strpos( $author_html, esc_html( $author->display_name ) ) && false !== strpos( $author_html, 'id="articles"' ) && false === strpos( $author_html, 'undefined' ) );

	foreach ( $all as $id ) { wp_delete_post( $id, true ); }
	wp_delete_term( $cat, 'category' );
	wp_delete_term( $other, 'category' );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
