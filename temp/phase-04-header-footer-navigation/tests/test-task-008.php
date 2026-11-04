<?php
/**
 * P04-T008 — مگامنوی شرکتی
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Options\Store;

es_task( 'P04-T008', 'مگامنوی شرکتی' );

es_check( 'target: template-parts/header/mega-corporate.php', es_file( 'template-parts/header/mega-corporate.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$render = static function () {
		ob_start();
		get_template_part( 'template-parts/header/mega-corporate' );
		return (string) ob_get_clean();
	};

	$html = $render();

	// AC-01: corporate panel present (wrapper data-mega-menu-corporate is in the walker).
	es_check( 'AC-01: پنل شرکتی رندر می‌شود', false !== strpos( $html, 'data-desktop-mega-panel="corporate"' ) );

	// AC-02 / SC-01: links resolve to real pages; about link is correct.
	es_check( 'AC-02: لینک درباره‌ما صحیح است', false !== strpos( $html, esc_url( Menus::page_url( 'about' ) ) ) );
	es_check( 'AC-02: لینک مجوزها حاضر است', false !== strpos( $html, esc_url( Menus::page_url( 'licenses-and-honors' ) ) ) );
	es_check( 'AC-02: لینک تماس حاضر است', false !== strpos( $html, esc_url( Menus::page_url( 'contact' ) ) ) );
	es_check( 'AC-02: شش کارت لینک شرکتی', 6 === substr_count( $html, 'group/corp flex items-start' ) );

	// AC-03: title/text editable from options.
	Store::set( 'mega_corporate_title', 'عنوان شرکتی سفارشی' );
	Store::set( 'mega_corporate_text', 'متن شرکتی سفارشی برای آزمایش.' );
	$html = $render();
	es_check( 'AC-03: عنوان قابل ویرایش است', false !== strpos( $html, 'عنوان شرکتی سفارشی' ) );
	es_check( 'AC-03: متن قابل ویرایش است', false !== strpos( $html, 'متن شرکتی سفارشی برای آزمایش.' ) );
	Store::delete( 'mega_corporate_title' );
	Store::delete( 'mega_corporate_text' );

	// SC-02: empty image → no <img>, stats fallback present.
	$html = $render();
	es_check( 'SC-02: بدون تصویر img رندر نمی‌شود', false === strpos( $html, '<img ' ) );
	es_check( 'SC-02: بلوک آمار fallback حاضر است', false !== strpos( $html, 'پروژه شهری' ) && false !== strpos( $html, 'گارانتی تعویض' ) );

	// AC-03: a set image renders.
	$att_id = wp_insert_attachment(
		array( 'post_title' => 'تصویر شرکتی', 'post_mime_type' => 'image/jpeg', 'post_status' => 'inherit' ),
		'es-corp-test.jpg'
	);
	wp_update_attachment_metadata( $att_id, array( 'width' => 400, 'height' => 250, 'file' => 'es-corp-test.jpg' ) );
	Store::set( 'mega_corporate_image_id', $att_id );
	$html = $render();
	es_check( 'AC-03: تصویر سفارشی رندر می‌شود', 1 === preg_match( '/<img src="[^"]+" alt="[^"]*" class="mt-3.5 w-full rounded-xl/', $html ) );
	Store::delete( 'mega_corporate_image_id' );
	wp_delete_attachment( $att_id, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
