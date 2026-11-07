<?php
/**
 * P07-T027 — single service: id=service-details, editable deliverables/steps,
 * no fatal for missing service, back-link to smartelement.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-027.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ServiceMetabox;
use ErfanSanat\Theme\Core\Menus;

es_task( 'P07-T027', 'تک‌خدمت + پارت جزئیات' );
es_check( 'target: single-services.php', es_file( 'single-services.php' ) );
es_check( 'target: template-parts/service/details.php', es_file( 'template-parts/service/details.php' ) );
es_check( 'AC-01: id=service-details در پارت', es_contains( 'template-parts/service/details.php', 'id="service-details"' ) );
es_check( 'AC-03: گارد پست ناموجود در قالب', es_contains( 'single-services.php', "get_post_type" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$render = static function ( $pid ) {
		$q = new WP_Query( array( 'p' => $pid, 'post_type' => 'services' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q;
		if ( $q->have_posts() ) { $q->the_post(); } else { $GLOBALS['post'] = null; }
		ob_start();
		include ES_THEME . '/single-services.php';
		return ob_get_clean();
	};

	// AC-03: missing service must not fatal.
	$missing = $render( 999999027 );
	es_check( 'AC-03: خدمت ناموجود fatal نمی‌دهد', is_string( $missing ) && strlen( $missing ) > 100 && false !== strpos( $missing, 'خدمت مورد نظر یافت نشد' ) );
	// locate_template uses require_once: only the first full render in this
	// process carries the footer; assert the global CTA there.
	es_check( 'مرجع: CTA تماس گلوبال فقط یک‌بار (فوتر)', 1 === substr_count( $missing, 'id="contact"' ) );

	// Smartelement page + a full service.
	$page_id = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'خدمات شرکت', 'post_name' => 'smartelement-027-' . uniqid() ) );
	add_filter( 'es_menu_page_url', static function ( $url, $slug ) use ( $page_id ) {
		return 'smartelement' === $slug ? get_permalink( $page_id ) : $url;
	}, 10, 2 );
	$sid = (int) wp_insert_post( array( 'post_type' => 'services', 'post_status' => 'publish', 'post_title' => 'نورپردازی میدان آزمایشی', 'post_excerpt' => 'خلاصه خدمت آزمایشی برای رندر کامل.', 'post_content' => '<p>متن کامل خدمت آزمایشی.</p>' ) );
	update_post_meta( $sid, '_es_service_badge', 'نشان خدمت آزمایشی' );
	update_post_meta( $sid, '_es_service_bullets', "قلم اجرایی یک\nقلم اجرایی دو\nقلم اجرایی سه" );
	update_post_meta( $sid, '_es_service_note', 'نکته مهم سفارشی آزمایشی.' );
	update_post_meta( $sid, '_es_service_steps', "مرحله سفارشی یک|توضیح یک\nمرحله سفارشی دو|توضیح دو" );

	$html = $render( $sid );
	es_check( 'SC-01: عنوان/خلاصه/نشان رندر می‌شوند', false !== strpos( $html, 'نورپردازی میدان آزمایشی' ) && false !== strpos( $html, 'خلاصه خدمت آزمایشی برای رندر کامل.' ) && false !== strpos( $html, 'نشان خدمت آزمایشی' ) );
	es_check( 'AC-01: سکشن service-details رندر می‌شود', false !== strpos( $html, 'id="service-details"' ) && false !== strpos( $html, 'اقلام اجرایی این خدمت' ) );
	es_check( 'AC-02: اقلام اجرایی ویرایش‌پذیر رندر می‌شوند', false !== strpos( $html, 'قلم اجرایی یک' ) && false !== strpos( $html, 'قلم اجرایی سه' ) && false !== strpos( $html, 'نکته مهم سفارشی آزمایشی.' ) );
	es_check( 'AC-02: مراحل اجرا ویرایش‌پذیر رندر می‌شوند', false !== strpos( $html, 'مرحله سفارشی یک' ) && false !== strpos( $html, 'توضیح دو' ) );
	es_check( 'SC-02: لینک بازگشت به smartelement درست است', false !== strpos( $html, 'href="' . esc_url( get_permalink( $page_id ) ) . '"' ) );

	// Metabox round-trip (AC-02 editing path).
	wp_set_current_user( 1 );
	$nonce = wp_create_nonce( 'es_service_details' );
	$_POST['es_service_details_nonce'] = $nonce;
	$_POST['es_service_details'] = array(
		'badge'   => 'نشان ذخیره‌شده',
		'bullets' => "ذخیره یک\nذخیره دو",
		'note'    => 'نکته ذخیره‌شده',
		'steps'   => "گام ذخیره|شرح گام",
	);
	ServiceMetabox::save( $sid );
	es_check( 'AC-02: متباکس اقلام/مراحل را ذخیره می‌کند', false !== strpos( (string) get_post_meta( $sid, '_es_service_bullets', true ), 'ذخیره یک' ) && false !== strpos( (string) get_post_meta( $sid, '_es_service_steps', true ), 'گام ذخیره' ) );
	unset( $_POST['es_service_details_nonce'] );
	$_POST['es_service_details']['badge'] = 'بدون nonce';
	ServiceMetabox::save( $sid );
	es_check( 'AC-02: ذخیره بدون nonce رد می‌شود', 'نشان ذخیره‌شده' === get_post_meta( $sid, '_es_service_badge', true ) );
	unset( $_POST['es_service_details'] );

	$edited = $render( $sid );
	es_check( 'AC-02: ویرایش متباکس در خروجی دیده می‌شود', false !== strpos( $edited, 'ذخیره یک' ) && false !== strpos( $edited, 'گام ذخیره' ) && false !== strpos( $edited, 'نشان ذخیره‌شده' ) );

	// Defaults: steps fall back to the reference path when meta empty.
	delete_post_meta( $sid, '_es_service_steps' );
	delete_post_meta( $sid, '_es_service_note' );
	$defaults = $render( $sid );
	es_check( 'پیش‌فرض: مسیر اجرای مرجع وقتی متا خالی است', false !== strpos( $defaults, 'بازدید و نیازسنجی' ) && false !== strpos( $defaults, 'تحویل و پشتیبانی' ) );

	wp_reset_postdata();
	wp_delete_post( $sid, true );
	wp_delete_post( $page_id, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
