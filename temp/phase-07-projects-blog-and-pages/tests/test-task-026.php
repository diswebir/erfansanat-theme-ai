<?php
/**
 * P07-T026 — services page (smartelement): list from services CPT, editable
 * cooperation path/CTA, no standalone archive.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-026.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;

es_task( 'P07-T026', 'برگه خدمات (smartelement)' );
es_check( 'target: page-templates/services.php', es_file( 'page-templates/services.php' ) );
es_check( 'AC-01: کوئری services CPT در قالب', 1 === preg_match( "/'post_type'\s*=>\s*'services'/", (string) file_get_contents( ES_THEME . '/page-templates/services.php' ) ) );
es_check( 'مرجع: id=services-list حفظ شده', es_contains( 'page-templates/services.php', 'id="services-list"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$fields = Schema::fields();
	es_check( 'AC-02: مسیر همکاری Repeater', isset( $fields['services_path'] ) && 'repeater' === $fields['services_path']['type'] );
	es_check( 'AC-02: عنوان/دکمه CTA قابل ویرایش', isset( $fields['services_cta_text'], $fields['services_cta_url'] ) );

	$pto = get_post_type_object( 'services' );
	es_check( 'AC-03: آرشیو مستقل services وجود ندارد', $pto && ! $pto->has_archive && ! get_post_type_archive_link( 'services' ) );

	$page_id = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'خدمات', 'post_name' => 'smartelement-026-' . uniqid() ) );
	update_post_meta( $page_id, '_wp_page_template', 'page-templates/services.php' );
	$render = static function ( $post_id ) {
		$q = new WP_Query( array( 'page_id' => $post_id, 'post_type' => 'page' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
		ob_start();
		include ES_THEME . '/page-templates/services.php';
		return ob_get_clean();
	};

	// SC-01: five services from the CPT.
	$ids = array();
	$titles = array( 'نورپردازی میدان', 'نورپردازی خیابان و بلوار', 'نورپردازی پل', 'نورپردازی المان‌های شهری', 'نورپردازی ساختمان' );
	foreach ( $titles as $i => $title ) {
		$sid = (int) wp_insert_post( array( 'post_type' => 'services', 'post_status' => 'publish', 'post_title' => $title, 'post_excerpt' => 'خلاصه خدمت ' . $title, 'menu_order' => $i, 'post_name' => 'svc-026-' . $i . '-' . uniqid() ) );
		update_post_meta( $sid, '_es_service_badge', 'نشان ' . $title );
		update_post_meta( $sid, '_es_service_bullets', "مزیت یک\nمزیت دو" );
		$ids[] = $sid;
	}
	Store::delete( 'services_path' ); Store::delete( 'services_cta_text' ); Store::delete( 'services_cta_url' );
	$html = $render( $page_id );
	es_check( 'SC-01: پنج خدمت در smartelement لیست می‌شوند', 5 === substr_count( $html, 'جزئیات این خدمت' ) );
	$all_listed = true; $order_ok = true; $prev = -1;
	foreach ( $titles as $t ) {
		$p = strpos( $html, '<h2 class="text-2xl font-extrabold text-slate-900 lg:text-3xl">' . esc_html( $t ) . '</h2>' );
		if ( false === $p ) { $all_listed = false; }
		elseif ( $p < $prev ) { $order_ok = false; } else { $prev = $p; }
	}
	es_check( 'AC-01: عنوان/ترتیب from CPT (menu_order)', $all_listed && $order_ok );
	es_check( 'AC-01: نشان/خلاصه/مزیت‌های خدمت رندر می‌شوند', false !== strpos( $html, 'نشان نورپردازی میدان' ) && false !== strpos( $html, 'خلاصه خدمت نورپردازی میدان' ) && false !== strpos( $html, 'مزیت یک' ) );
	$link_ok = true;
	foreach ( $ids as $sid ) { if ( false === strpos( $html, 'href="' . esc_url( get_permalink( $sid ) ) . '"' ) ) { $link_ok = false; } }
	es_check( 'SC-02: لینک تک‌خدمت درست است', $link_ok );
	es_check( 'مرجع: چیپس فهرست خدمات در هیرو', false !== strpos( $html, 'فهرست خدمات' ) && 5 === substr_count( $html, 'rounded-md bg-emerald-50 text-[10.5px]' ) );
	es_check( 'AC-02: مسیر همکاری پیش‌فرض ۵ مرحله', false !== strpos( $html, 'مسیر همکاری با ما؛' ) && false !== strpos( $html, 'بازدید و مشاوره رایگان' ) && false !== strpos( $html, 'تحویل و پشتیبانی' ) );

	// SC/AC-02: edit path + CTA.
	Store::set( 'services_path', array( array( 'title' => 'مرحله سفارشی همکاری', 'description' => 'توضیح مرحله سفارشی.' ) ) );
	Store::set( 'services_cta_text', 'شروع همکاری آزمایشی' );
	Store::set( 'services_cta_url', 'https://example.test/quote/' );
	$edited = $render( $page_id );
	es_check( 'AC-02: مسیر همکاری ویرایش‌شده رندر می‌شود', false !== strpos( $edited, 'مرحله سفارشی همکاری' ) && false === strpos( $edited, 'بازدید و مشاوره رایگان' ) );
	es_check( 'AC-02: CTA ویرایش‌شده رندر می‌شود', false !== strpos( $edited, 'شروع همکاری آزمایشی' ) && false !== strpos( $edited, 'https://example.test/quote/' ) );

	// Empty CPT → no list rows, page still renders.
	foreach ( $ids as $sid ) { wp_delete_post( $sid, true ); }
	$empty = $render( $page_id );
	es_check( 'خدمات خالی: ردیفی رندر نمی‌شود ولی صفحه سالم است', 0 === substr_count( $empty, 'جزئیات این خدمت' ) && false !== strpos( $empty, 'id="services-list"' ) );

	Store::delete( 'services_path' ); Store::delete( 'services_cta_text' ); Store::delete( 'services_cta_url' );
	wp_reset_postdata();
	wp_delete_post( $page_id, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
