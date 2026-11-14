<?php
/**
 * P05-T011 — سکشن بومی‌سازی سازه‌های نوری
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T011', 'سکشن بومی‌سازی سازه‌های نوری' );

es_check( 'target: بخش بومی‌سازی ساخته شده', es_file( 'template-parts/sections/home/section-localization.php' ) );
es_check( 'AC-02: گرادیان سبز مرجع حفظ شده', es_contains( 'template-parts/sections/home/section-localization.php', 'bg-gradient-to-l from-[#064e3b]' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'localization', 'type' => 'localization', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// ── AC-02 / AC-03: default render keeps DOM even with empty fields ────.
	$html = $render();
	es_check( 'AC-02: DOM مرجع (کارت گرادیان) حفظ شده', false !== strpos( $html, 'rounded-[2.25rem] border-0 bg-gradient-to-l' ) );
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="localization"' ) );
	es_check( 'AC-03: فیلد خالی سکشن را حذف نمی‌کند', false !== strpos( $html, 'data-es-section="localization"' ) && false !== strpos( $html, 'مرجع بومی‌سازی' ) );
	es_check( 'متن پیش‌فرض رندر می‌شود', false !== strpos( $html, 'خطوط تمام‌اتوماتیک تزریق پلیمر' ) );
	es_check( 'تصویر پیش‌فرض fallback دارد', false !== strpos( $html, 'images/hero.jpg' ) );
	es_check( 'AC-01: لیست مزایا (نشان‌ها) رندر می‌شود', false !== strpos( $html, 'ISO 9001:2015' ) && false !== strpos( $html, 'درجه حفاظت IP68' ) && false !== strpos( $html, 'ایران‌کد GS1' ) );
	es_check( 'سال تأسیس صحیح است', false !== strpos( $html, '۱۳۸۰ خورشیدی' ) );

	// ── AC-01 / SC-01: editable text ──────────────────────────────────────.
	$html2 = $render( array( 'heading' => 'عنوان بومی‌سازی سفارشی', 'content' => 'متن سفارشی بومی‌سازی شرکت.' ) );
	es_check( 'SC-01: ویرایش عنوان در خروجی دیده می‌شود', false !== strpos( $html2, 'عنوان بومی‌سازی سفارشی' ) );
	es_check( 'SC-01: ویرایش متن در خروجی دیده می‌شود', false !== strpos( $html2, 'متن سفارشی بومی‌سازی شرکت' ) );

	// ── AC-01 / SC-02: editable image ─────────────────────────────────────.
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'loc-img', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/localization-custom.jpg' );
	$html3 = $render( array( 'image' => $att ) );
	es_check( 'SC-02: تصویر جایگزین رندر می‌شود', false !== strpos( $html3, 'localization-custom.jpg' ) && false === strpos( $html3, 'images/hero.jpg' ) );

	// ── AC-01: badges filterable ──────────────────────────────────────────.
	add_filter( 'es_localization_badges', static function () {
		return array( array( 'title' => 'نشان سفارشی', 'subtitle' => 'زیرنشان' ) );
	} );
	$html4 = $render();
	es_check( 'AC-01: لیست مزایا قابل ویرایش (فیلتر) است', false !== strpos( $html4, 'نشان سفارشی' ) && false === strpos( $html4, 'ISO 9001:2015' ) );
	remove_all_filters( 'es_localization_badges' );

	// ── Escaping ──────────────────────────────────────────────────────────.
	$html5 = $render( array( 'heading' => '<b>bold</b>' ) );
	es_check( 'عنوان escape می‌شود', false === strpos( $html5, '<b>bold</b>' ) && false !== strpos( $html5, '&lt;b&gt;' ) );

	wp_delete_post( $att, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
