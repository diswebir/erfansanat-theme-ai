<?php
/**
 * P05-T019 — سکشن پیش‌نمایش وبلاگ (جدیدترین مقالات)
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T019', 'سکشن پیش‌نمایش وبلاگ' );

es_check( 'target: بخش وبلاگ ساخته شده', es_file( 'template-parts/sections/home/section-blog.php' ) );
es_check( 'AC-01: id=blog-preview در قالب', es_contains( 'template-parts/sections/home/section-blog.php', 'id="blog-preview"' ) );
es_check( 'AC-02: WP_Query نوشته‌های منتشرشده', es_contains( 'template-parts/sections/home/section-blog.php', "'post_status'         => 'publish'" ) );
es_check( 'کارت استاندارد مقاله بازاستفاده می‌شود', es_contains( 'template-parts/sections/home/section-blog.php', 'cards/card-post' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'blog-preview', 'type' => 'blog-preview', 'enabled' => true, 'fields' => $fields ), 'home' );
	};
	$card_count = static function ( $html ) {
		return substr_count( $html, 'card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]' );
	};

	// Clean slate: remove any pre-existing posts (e.g. the install sample
	// post) so card counts are deterministic for this test.
	foreach ( get_posts( array( 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) ) as $es_old ) {
		wp_delete_post( $es_old, true );
	}

	// Fixtures: 5 published posts with ascending dates + 1 draft.
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'post-img', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/post.jpg' );
	$titles = array( 'مقاله قدیمی', 'مقاله میانی', 'مقاله تازه یک', 'مقاله تازه دو', 'مقاله تازه سه' );
	$ids = array();
	foreach ( $titles as $i => $title ) {
		$date = sprintf( '2026-%02d-01 10:00:00', $i + 1 );
		$p = wp_insert_post( array(
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => $title,
			'post_content'  => 'محتوای آزمایشی مقاله برای تست زمان مطالعه.',
			'post_date'     => $date,
			'post_date_gmt' => $date,
		) );
		set_post_thumbnail( $p, $att );
		$ids[] = $p;
	}
	$draft = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'draft', 'post_title' => 'مقاله پیش‌نویس مخفی' ) );

	// ── AC-01 / SC-01: three latest posts render ──────────────────────────.
	$html = $render( array( 'count' => 3 ) );
	es_check( 'AC-01: id=blog-preview حفظ شده', false !== strpos( $html, 'id="blog-preview"' ) );
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="blog-preview"' ) );
	es_check( 'SC-01: سه مقاله آخر رندر می‌شوند', 3 === $card_count( $html ) );
	es_check( 'AC-02: جدیدترین مقاله اول می‌آید', strpos( $html, 'مقاله تازه سه' ) < strpos( $html, 'مقاله تازه دو' ) );
	es_check( 'AC-02: مقالات قدیمی‌تر با count=3 حذف می‌شوند', false === strpos( $html, 'مقاله قدیمی' ) && false === strpos( $html, 'مقاله میانی' ) );

	// ── SC-02: draft never shown ──────────────────────────────────────────.
	es_check( 'SC-02: مقاله پیش‌نویس نمایش داده نمی‌شود', false === strpos( $html, 'مقاله پیش‌نویس مخفی' ) );
	$html_all = $render( array( 'count' => 12 ) );
	es_check( 'SC-02: حتی با count بالا پیش‌نویس نمی‌آید', false === strpos( $html_all, 'مقاله پیش‌نویس مخفی' ) && 5 === $card_count( $html_all ) );

	// ── AC-03: count follows setting (clamped) ────────────────────────────.
	es_check( 'AC-03: count=1 یک کارت', 1 === $card_count( $render( array( 'count' => 1 ) ) ) );
	es_check( 'AC-03: count=0 به ۱ کلمپ می‌شود', 1 === $card_count( $render( array( 'count' => 0 ) ) ) );
	es_check( 'AC-03: count=99 به ۱۲ کلمپ (۵ موجود)', 5 === $card_count( $render( array( 'count' => 99 ) ) ) );

	// ── Archive button + grid + escaping ──────────────────────────────────.
	es_check( 'گرید مرجع حفظ شده', false !== strpos( $html, 'grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4' ) );
	es_check( 'دکمه آرشیو مقالات', false !== strpos( $html, 'مشاهده آرشیو مقالات' ) );
	$blog_page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'blog', 'post_title' => 'وبلاگ' ) );
	$html_blog = $render();
	es_check( 'لینک آرشیو به صفحه blog', false !== strpos( $html_blog, 'href="' . esc_url( get_permalink( $blog_page ) ) . '"' ) );
	wp_delete_post( $blog_page, true );
	$html_esc = $render( array( 'heading' => '<b>bold</b>' ) );
	es_check( 'عنوان escape می‌شود', false === strpos( $html_esc, '<b>bold</b>' ) && false !== strpos( $html_esc, '&lt;b&gt;' ) );

	// ── Empty state ───────────────────────────────────────────────────────.
	foreach ( $ids as $p ) { wp_delete_post( $p, true ); }
	wp_delete_post( $draft, true );
	$html_empty = $render();
	es_check( 'حالت خالی DOM را نمی‌شکند', false !== strpos( $html_empty, 'id="blog-preview"' ) && false !== strpos( $html_empty, 'مقاله‌ای برای نمایش وجود ندارد' ) );

	wp_delete_post( $att, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
