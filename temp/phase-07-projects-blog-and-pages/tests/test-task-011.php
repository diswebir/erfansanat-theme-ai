<?php
/**
 * P07-T011 — article card parity, real client filter metadata and Shamsi dates.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-011.php
 * DOM: node temp/phase-07-projects-blog-and-pages/tests/test-task-011-dom.mjs
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Calendar;

es_task( 'P07-T011', 'کارت مقاله و فیلتر کلاینت' );
es_check( 'target: card-post.php', es_file( 'template-parts/cards/card-post.php' ) );
es_check( 'target: blog-filter.php', es_file( 'template-parts/archive/blog-filter.php' ) );
es_check( 'AC-01: قرارداد data-search-item و کلاس مرجع', es_contains( 'template-parts/cards/card-post.php', 'data-search-item' ) && es_contains( 'template-parts/cards/card-post.php', 'card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]' ) );
es_check( 'AC-02: فیلتر مشترک در هیرو و تولبار استفاده می‌شود', es_contains( 'template-parts/archive/blog-hero.php', "get_template_part( 'template-parts/archive/blog-filter'" ) && es_contains( 'template-parts/archive/blog-shell.php', "get_template_part( 'template-parts/archive/blog-filter'" ) );
es_check( 'AC-03: تاریخ شمسی واقعی با پشتیبانی WP-Parsidate', es_file( 'inc/Core/Calendar.php' ) && es_contains( 'inc/Core/Calendar.php', 'get_the_date(' ) && es_contains( 'template-parts/cards/card-post.php', 'Calendar::post_date(' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	es_check( 'تاریخ مرجع: ۲۰۲۵-۰۸-۰۳ = ۱۲ مرداد ۱۴۰۴', '۱۲ مرداد ۱۴۰۴' === Calendar::format( new DateTimeImmutable( '2025-08-03' ), 'j F Y' ) );
	es_check( 'تاریخ عددی: ۱۴۰۴/۰۵/۱۲', '۱۴۰۴/۰۵/۱۲' === Calendar::format( new DateTimeImmutable( '2025-08-03' ), 'Y/m/d' ) );
	es_check( 'نوروز ۱۴۰۳', '۱۴۰۳/۰۱/۰۱' === Calendar::format( new DateTimeImmutable( '2024-03-20' ), 'Y/m/d' ) );
	es_check( 'پایان سال کبیسه ۱۴۰۳', '۱۴۰۳/۱۲/۳۰' === Calendar::format( new DateTimeImmutable( '2025-03-20' ), 'Y/m/d' ) );

	$cat = es_insert_term( 'مجله علمی', 'category', array( 'slug' => 'card-011-' . uniqid() ) )['term_id'];
	$pid = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'راهنمای تونل نوری', 'post_excerpt' => 'راهنمای خرید تجهیزات نورپردازی', 'post_content' => 'متن کامل مقاله', 'post_date' => '2025-08-03 10:00:00', 'post_name' => 'card-011-' . uniqid() ) );
	wp_set_object_terms( $pid, array( (int) $cat ), 'category' );
	wp_set_post_tags( $pid, array( 'تونل نوری' ) );
	ob_start();
	get_template_part( 'template-parts/cards/card-post', null, array( 'post_id' => $pid, 'archive_item' => true ) );
	$html = ob_get_clean();
	es_check( 'AC-01: پوشش آیتم آرشیو و متن جستجو', false !== strpos( $html, 'data-blog-item="true"' ) && false !== strpos( $html, 'data-blog-category="مجله علمی"' ) && false !== strpos( $html, 'راهنمای خرید تجهیزات نورپردازی' ) );
	es_check( 'AC-01: کارت اصلی یک لینک با کلاس مرجع است', 1 === substr_count( $html, 'card-light group flex h-full' ) && false !== strpos( $html, esc_url( get_permalink( $pid ) ) ) );
	es_check( 'AC-03: تاریخ کارت دقیقاً شمسی است', false !== strpos( $html, '۱۲ مرداد ۱۴۰۴' ) && false === strpos( $html, 'August' ) );
	es_check( 'جستجو با ی/ک عربی و حالت کوچک انگلیسی', false !== strpos( $html, 'راهنمای تونل نوری' ) && false !== strpos( $html, 'راهنماي تونل نوري' ) );
	ob_start();
	get_template_part( 'template-parts/cards/card-post', null, array( 'post_id' => $pid, 'search_result' => true ) );
	$search_html = ob_get_clean();
	es_check( 'data-search-item فقط برای نتیجه جستجوی سراسری', false !== strpos( $search_html, 'data-search-item=' ) && false === strpos( $html, 'data-search-item=' ) );

	ob_start();
	get_template_part( 'template-parts/archive/blog-filter', null, array( 'categories' => array( get_term( $cat ) ), 'context' => 'category', 'term' => get_term( $cat ), 'blog_url' => home_url( '/blog/' ), 'variant' => 'toolbar' ) );
	$filter = ob_get_clean();
	es_check( 'SC-02: لینک دسته واقعی و فعال', false !== strpos( $filter, esc_url( get_category_link( $cat ) ) ) && false !== strpos( $filter, 'bg-emerald-700 text-white' ) );
	es_check( 'AC-02: بازنشانی فیلتر واقعی', false !== strpos( $filter, 'data-blog-reset="true"' ) );

	wp_delete_post( $pid, true );
	wp_delete_term( $cat, 'category' );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
