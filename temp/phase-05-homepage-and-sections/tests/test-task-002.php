<?php
/**
 * P05-T002 — کارت استاندارد پروژه
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P05-T002', 'کارت استاندارد پروژه' );

es_check( 'target: cards/card-project.php', es_file( 'template-parts/cards/card-project.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}
	if ( ! post_type_exists( 'projects' ) ) {
		register_post_type( 'projects', array( 'public' => true ) );
	}
	if ( ! taxonomy_exists( 'project_type' ) ) {
		register_taxonomy( 'project_type', 'projects', array( 'public' => true ) );
	}
	if ( ! taxonomy_exists( 'city' ) ) {
		register_taxonomy( 'city', 'projects', array( 'public' => true ) );
	}

	$render = static function ( $project_id ) {
		ob_start();
		get_template_part( 'template-parts/cards/card-project', null, array( 'project_id' => $project_id ) );
		return (string) ob_get_clean();
	};
	$attr = static function ( $html, $name ) {
		return preg_match( '/data-project-' . $name . '="([^"]*)"/', $html, $m ) ? $m[1] : null;
	};

	// ── SC-01 / SC-02: a real project with terms + meta ───────────────────.
	$pid   = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'اجرای پروژه نیم هرم نوری اهواز' ) );
	$t1    = wp_insert_term( 'المان‌های نورانی', 'project_type' );
	$t2    = wp_insert_term( 'نورپردازی شهری', 'project_type' );
	$city  = wp_insert_term( 'اهواز', 'city' );
	wp_set_object_terms( $pid, array( (int) $t1['term_id'], (int) $t2['term_id'] ), 'project_type' );
	wp_set_object_terms( $pid, array( (int) $city['term_id'] ), 'city' );
	update_post_meta( $pid, 'number2-viewc', 1580 );
	update_post_meta( $pid, 'project_rating', '4.95' );
	update_post_meta( $pid, 'project_client', 'سازمان زیباسازی شهرداری اهواز' );
	update_post_meta( $pid, 'finish-pro-time', '1403/11/15' );
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'تصویر پروژه', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/ahvaz-project.jpg' );
	set_post_thumbnail( $pid, $att );

	$html = $render( $pid );
	es_check( 'SC-01: کارت پروژه واقعی رندر می‌شود', false !== strpos( $html, 'data-project-item="true"' ) && false !== strpos( $html, 'اجرای پروژه نیم هرم نوری اهواز' ) );

	// AC-01: all data attributes present.
	foreach ( array( 'item', 'type', 'types', 'location', 'views', 'rating', 'search-text' ) as $a ) {
		es_check( 'AC-01: data-project-' . $a . ' حاضر است', false !== strpos( $html, 'data-project-' . $a . '=' ) );
	}

	// SC-02: data attributes equal meta/terms.
	es_check( 'SC-02: type = اولین ترم نوع', 'المان‌های نورانی' === $attr( $html, 'type' ) );
	es_check( 'SC-02: types = همه نوع‌ها با |', 'المان‌های نورانی|نورپردازی شهری' === $attr( $html, 'types' ) );
	es_check( 'SC-02: location = ترم شهر', 'اهواز' === $attr( $html, 'location' ) );
	es_check( 'SC-02: views = متای شمارنده', '1580' === $attr( $html, 'views' ) );
	es_check( 'SC-02: rating = متای امتیاز', '4.95' === $attr( $html, 'rating' ) );
	$search = (string) $attr( $html, 'search-text' );
	es_check( 'SC-02: search-text شامل عنوان/شهر/نوع/کارفرما', false !== strpos( $search, 'نیم هرم' ) && false !== strpos( $search, 'اهواز' ) && false !== strpos( $search, 'المان‌های نورانی' ) && false !== strpos( $search, 'سازمان زیباسازی' ) );

	// AC-02: link/image/city from DB.
	es_check( 'AC-02: لینک = permalink پروژه', false !== strpos( $html, 'href="' . esc_url( get_permalink( $pid ) ) . '"' ) );
	es_check( 'AC-02: تصویر از رسانه پروژه', false !== strpos( $html, 'ahvaz-project.jpg' ) );
	es_check( 'AC-02: شهر از DB نمایش داده می‌شود', false !== strpos( $html, 'اهواز' ) );
	es_check( 'نمایش ارقام فارسی بازدید/امتیاز', false !== strpos( $html, '۱۵۸۰' ) && false !== strpos( $html, '۴.۹۵' ) );

	// ── AC-03: minimal project (no terms/optional meta) does not break ───.
	$bare = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه بدون داده' ) );
	$html = $render( $bare );
	es_check( 'AC-03: کارت بدون داده اضافی DOM را نمی‌شکند', false !== strpos( $html, 'data-project-item="true"' ) && false !== strpos( $html, 'پروژه بدون داده' ) );
	es_check( 'AC-03: rating پیش‌فرض 0 است', '0' === $attr( $html, 'rating' ) );
	es_check( 'AC-03: بدون شهر/نوع/تاریخ span اضافی نیست', null === $attr( $html, 'location' ) || '' === $attr( $html, 'location' ) );
	es_check( 'AC-03: تصویر fallback تم استفاده می‌شود', false !== strpos( $html, 'images/light-element.jpg' ) );

	// Non-project post type renders nothing.
	$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'برگه' ) );
	es_check( 'AC-03: نوع پست غیرپروژه خروجی ندارد', '' === trim( $render( $page ) ) );

	// Cleanup.
	wp_delete_post( $pid, true );
	wp_delete_post( $bare, true );
	wp_delete_post( $page, true );
	wp_delete_post( $att, true );
	wp_delete_term( $t1['term_id'], 'project_type' );
	wp_delete_term( $t2['term_id'], 'project_type' );
	wp_delete_term( $city['term_id'], 'city' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
