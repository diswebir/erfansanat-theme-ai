<?php
/**
 * P05-T014 — سکشن CTA کاتالوگ جامع
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T014', 'سکشن CTA کاتالوگ جامع' );

es_check( 'target: بخش کاتالوگ ساخته شده', es_file( 'template-parts/sections/home/section-catalog.php' ) );
es_check( 'AC-01: فیلد file_id در قالب', es_contains( 'template-parts/sections/home/section-catalog.php', 'file_id' ) );
es_check( 'AC-02: لینک با esc_url ایمن می‌شود', es_contains( 'template-parts/sections/home/section-catalog.php', 'esc_url( $es_file_url )' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'catalog', 'type' => 'catalog', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// ── AC-03 / SC-02: no file -> CTA button removed ──────────────────────.
	$html0 = $render();
	es_check( 'AC-03: بدون فایل دکمه دانلود نیست', false === strpos( $html0, 'download class=' ) && false === strpos( $html0, '<a href=' ) );
	es_check( 'AC-03: بدون فایل سکشن خاموش نمی‌شود (DOM هست)', false !== strpos( $html0, 'data-es-section="catalog"' ) );
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html0, 'data-es-section="catalog"' ) );

	// ── SC-01: selected file gets the correct link ────────────────────────.
	$file = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'catalog-1404', 'post_mime_type' => 'application/pdf' ) );
	update_post_meta( $file, '_wp_attached_file', '2026/11/catalog-1404.pdf' );
	$html = $render( array( 'file_id' => $file ) );
	$file_url = wp_get_attachment_url( $file );
	es_check( 'SC-01: فایل انتخابی لینک درست می‌گیرد', false !== strpos( $html, 'href="' . esc_url( $file_url ) . '"' ) );
	es_check( 'SC-01: دکمه دانلود ظاهر می‌شود', false !== strpos( $html, 'download class=' ) );
	es_check( 'AC-01: متن دکمه پیش‌فرض رندر می‌شود', false !== strpos( $html, 'دانلود کاتالوگ' ) );
	es_check( 'AC-02: لینک دانلود ایمن (esc_url) است', false !== strpos( $html, 'href="' . esc_url( $file_url ) . '"' ) && false === strpos( $html, 'href="javascript' ) );

	// ── AC-01: editable heading/description/button label ──────────────────.
	$html2 = $render( array(
		'file_id'      => $file,
		'heading'      => 'کاتالوگ سفارشی',
		'description'  => 'توضیح سفارشی کاتالوگ.',
		'button_label' => 'دریافت فایل سفارشی',
	) );
	es_check( 'AC-01: عنوان قابل ویرایش است', false !== strpos( $html2, 'کاتالوگ سفارشی' ) );
	es_check( 'AC-01: توضیح قابل ویرایش است', false !== strpos( $html2, 'توضیح سفارشی کاتالوگ' ) );
	es_check( 'AC-01: متن دکمه قابل ویرایش است', false !== strpos( $html2, 'دریافت فایل سفارشی' ) );

	// ── SC-02: removing the file removes the button again ─────────────────.
	$html3 = $render( array( 'file_id' => 0 ) );
	es_check( 'SC-02: حذف فایل دکمه را حذف می‌کند', false === strpos( $html3, 'download class=' ) );

	// ── Escaping ──────────────────────────────────────────────────────────.
	$html4 = $render( array( 'heading' => '<b>bold</b>' ) );
	es_check( 'عنوان escape می‌شود', false === strpos( $html4, '<b>bold</b>' ) && false !== strpos( $html4, '&lt;b&gt;' ) );

	wp_delete_post( $file, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
