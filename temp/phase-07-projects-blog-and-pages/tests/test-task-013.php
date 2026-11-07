<?php
/**
 * P07-T013 — single-post TOC from real h2/h3 headings, unique anchors.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-013.php
 * DOM: node temp/phase-07-projects-blog-and-pages/tests/test-task-013-dom.mjs
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T013', 'TOC تک‌نوشته' );
es_check( 'target: template-parts/post/toc.php', es_file( 'template-parts/post/toc.php' ) );
es_check( 'وابستگی T012: آماده‌سازی پیش از محتوا', es_contains( 'single.php', "get_template_part( 'template-parts/post/toc', null, array( 'prepare' => true ) )" ) );
es_check( 'AC-04: کارت هماهنگ با مرجع', es_contains( 'template-parts/post/toc.php', 'rounded-3xl border border-emerald-100 bg-white p-6 shadow-xs' ) && es_contains( 'template-parts/post/toc.php', 'سرفصل‌های مقاله' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$pid = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله فهرست‌دار', 'post_content' => '<h1>عنوان اصلی</h1><h2>سر فصل یک</h2><p>متن</p><h3>زیربخش</h3><h2>سر فصل یک</h2><h4>نباید فهرست شود</h4>', 'post_name' => 'toc-013-' . uniqid() ) );
	$q = new WP_Query( array( 'p' => $pid, 'post_type' => 'post' ) );
	$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
	get_template_part( 'template-parts/post/toc', null, array( 'prepare' => true ) );
	$filtered = es_post_toc_filter( '<h1>عنوان اصلی</h1><h2>سر فصل یک</h2><p>متن</p><h3>زیربخش</h3><h2>سر فصل یک</h2><h4>نباید فهرست شود</h4>' );
	preg_match_all( '/<h([23])[^>]*id="([^"]+)"/', $filtered, $m );
	es_check( 'AC-01: فقط h2/h3 فهرست/شناسه‌دار شدند', array( '2', '3', '2' ) === $m[1] && false === strpos( $filtered, '<h1 id' ) && false === strpos( $filtered, '<h4 id' ) );
	es_check( 'AC-02: شناسه‌ها یکتا هستند', 3 === count( array_unique( $m[2] ) ) );
	ob_start(); get_template_part( 'template-parts/post/toc' ); $toc = ob_get_clean();
	es_check( 'AC-02: لینک لنگر برای هر شناسه', 3 === substr_count( $toc, 'href="#' ) && foreach_ok( $m[2], $toc ) );
	es_check( 'عنوان تکراری دو لنگر جدا دارد', substr_count( $toc, 'سر فصل یک' ) === 2 );
	es_check( 'h3 تورفتگی دارد', false !== strpos( $toc, 'pr-3' ) );

	$existing = es_post_toc_filter( '<h2 id="custom-id">عنوان موجود</h2>' );
	es_check( 'شناسه موجود حفظ می‌شود', false !== strpos( $existing, 'id="custom-id"' ) );

	wp_update_post( array( 'ID' => $pid, 'post_content' => '<p>بدون هدینگ</p>' ) );
	get_template_part( 'template-parts/post/toc', null, array( 'prepare' => true ) );
	ob_start(); get_template_part( 'template-parts/post/toc' ); $empty = ob_get_clean();
	es_check( 'AC-03: بدون هدینگ هیچ TOC ساخته نمی‌شود', '' === trim( $empty ) );

	add_shortcode( 'es_toc_heading', static function () { return '<h2>هدینگ شورت‌کد</h2>'; } );
	wp_update_post( array( 'ID' => $pid, 'post_content' => '[es_toc_heading]' ) );
	get_template_part( 'template-parts/post/toc', null, array( 'prepare' => true ) );
	$sc = apply_filters( 'the_content', '[es_toc_heading]' );
	ob_start(); get_template_part( 'template-parts/post/toc' ); $sc_toc = ob_get_clean();
	es_check( 'هدینگ شورت‌کد بعد از رندر فهرست می‌شود', false !== strpos( $sc, '<h2' ) && false !== strpos( $sc_toc, 'هدینگ شورت‌کد' ) );
	remove_shortcode( 'es_toc_heading' );

	wp_update_post( array( 'ID' => $pid, 'post_password' => 'secret', 'post_content' => '<h2>محرمانه</h2>' ) );
	get_template_part( 'template-parts/post/toc', null, array( 'prepare' => true ) );
	ob_start(); get_template_part( 'template-parts/post/toc' ); $protected = ob_get_clean();
	es_check( 'نوشته رمزدار TOC نمی‌دهد', '' === trim( $protected ) );

	wp_update_post( array( 'ID' => $pid, 'post_password' => '', 'post_content' => '<h2>بخش نهایی</h2><p>متن</p>' ) );
	ob_start(); include ES_THEME . '/single.php'; $single = ob_get_clean();
	preg_match_all( '/href="#([^"]+)"/', $single, $toc_hrefs );
	preg_match_all( '/<h[23][^>]*id="([^"]+)"/', $single, $toc_ids );
	es_check( 'SC-01: تک‌نوشته کامل TOC و لنگر هدف دارد', false !== strpos( $single, 'سرفصل‌های مقاله' ) && ! empty( $toc_hrefs[1] ) && ! array_diff( $toc_hrefs[1], $toc_ids[1] ) );
	wp_reset_postdata();
	wp_delete_post( $pid, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
function foreach_ok( $ids, $html ) { foreach ( $ids as $id ) { if ( false === strpos( $html, 'href="#' . $id . '"' ) ) { return false; } } return true; }
es_summary();
