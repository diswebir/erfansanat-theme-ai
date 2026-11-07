<?php
/**
 * P05-T004 — کارت استاندارد مقاله
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T004', 'کارت استاندارد مقاله' );

es_check( 'target: cards/card-post.php', es_file( 'template-parts/cards/card-post.php' ) );
es_check( 'AC-02: تاریخ از Calendar::post_date (سازگار با WP-Parsidate)', es_contains( 'template-parts/cards/card-post.php', 'Calendar::post_date(' ) && es_contains( 'inc/Core/Calendar.php', 'get_the_date(' ) );
es_check( 'AC-03: خلاصه با wp_trim_words کنترل طول می‌شود', es_contains( 'template-parts/cards/card-post.php', 'wp_trim_words(' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function ( $post_id ) {
		ob_start();
		get_template_part( 'template-parts/cards/card-post', null, array( 'post_id' => $post_id ) );
		return (string) ob_get_clean();
	};

	// ── SC-01: a real Persian article ─────────────────────────────────────.
	$cat = es_insert_term( 'مجله علمی', 'category', array( 'slug' => 'p05-card-' . uniqid() ) );
	$pid = wp_insert_post( array(
		'post_type'     => 'post',
		'post_status'   => 'publish',
		'post_title'    => 'آشنایی جامع با انواع تونل نوری',
		'post_content'  => str_repeat( 'تونل نوری یکی از جذاب‌ترین سازه‌های نورپردازی شهری است. ', 40 ),
		'post_excerpt'  => 'تونل نوری یکی از جذاب‌ترین سازه‌های نورپردازی شهری است و انواع مختلفی دارد.',
		'post_date'     => '2025-08-03 10:00:00',
	) );
	wp_set_object_terms( $pid, array( (int) $cat['term_id'] ), 'category' );

	$html = $render( $pid );
	es_check( 'SC-01: مقاله فارسی رندر می‌شود', false !== strpos( $html, 'آشنایی جامع با انواع تونل نوری' ) );
	es_check( 'AC-01: کلاس‌های کارت وبلاگ حفظ شده', false !== strpos( $html, 'card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]' ) );
	es_check( 'AC-01: ساختار تصویر/بدنه/فوتر کارت', false !== strpos( $html, 'relative h-52 overflow-hidden' ) && false !== strpos( $html, 'mt-auto flex items-center justify-between border-t border-slate-100 pt-4' ) );
	es_check( 'دسته مقاله نمایش داده می‌شود', false !== strpos( $html, 'مجله علمی' ) );
	es_check( 'AC-02: تاریخ شمسی دقیق رندر می‌شود', false !== strpos( $html, '۱۲ مرداد ۱۴۰۴' ) );
	es_check( 'زمان مطالعه نمایش داده می‌شود', 1 === preg_match( '/[۰-۹]+ دقیقه مطالعه/u', $html ) );
	es_check( 'خلاصه نمایش داده می‌شود', false !== strpos( $html, 'جذاب‌ترین سازه‌های نورپردازی' ) );
	es_check( 'نویسنده نمایش داده می‌شود', false !== strpos( $html, 'ادامه مطلب' ) );
	es_check( 'لینک = permalink مقاله', false !== strpos( $html, 'href="' . esc_url( get_permalink( $pid ) ) . '"' ) );
	es_check( 'SC-02: بدون تصویر شاخص fallback دارد', false !== strpos( $html, 'images/light-tunnel.jpg' ) );

	// ── AC-03: excerpt escaped + length-controlled ────────────────────────.
	$long = wp_insert_post( array(
		'post_type'    => 'post',
		'post_status'  => 'publish',
		'post_title'   => 'مقاله خلاصه بلند',
		'post_excerpt' => '<script>alert(1)</script> ' . str_repeat( 'واژه ', 80 ),
	) );
	$html = $render( $long );
	es_check( 'AC-03: خلاصه escape می‌شود', false === strpos( $html, '<script>alert(1)</script>' ) );
	es_check( 'AC-03: خلاصه بلند کوتاه می‌شود', false !== strpos( $html, '…' ) && substr_count( $html, 'واژه' ) < 80 );

	// ── Featured badge for sticky post ────────────────────────────────────.
	stick_post( $pid );
	$html = $render( $pid );
	es_check( 'نشان ویژه برای مقاله چسبان', false !== strpos( $html, '★ ویژه' ) );
	unstick_post( $pid );
	$html = $render( $pid );
	es_check( 'بدون چسبان نشان ویژه نیست', false === strpos( $html, '★ ویژه' ) );

	// Non-post renders nothing.
	$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'برگه' ) );
	es_check( 'نوع غیرمقاله خروجی ندارد', '' === trim( $render( $page ) ) );

	// Cleanup.
	foreach ( array( $pid, $long, $page ) as $id ) { wp_delete_post( $id, true ); }
	wp_delete_term( $cat['term_id'], 'category' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
