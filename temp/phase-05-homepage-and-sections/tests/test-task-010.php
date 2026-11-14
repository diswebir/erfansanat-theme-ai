<?php
/**
 * P05-T010 — سکشن درباره ما + آمار
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T010', 'سکشن درباره ما + آمار' );

es_check( 'target: بخش درباره ساخته شده', es_file( 'template-parts/sections/home/section-about.php' ) );
es_check( 'AC-01: id=about در قالب', es_contains( 'template-parts/sections/home/section-about.php', 'id="about"' ) );
es_check( 'AC-03: قلاب data-counter-target در قالب', es_contains( 'template-parts/sections/home/section-about.php', 'data-counter-target' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'about', 'type' => 'about', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// ── AC-01: id + classes + default render ──────────────────────────────.
	$html = $render();
	es_check( 'AC-01: id=about حفظ شده', false !== strpos( $html, 'id="about"' ) );
	es_check( 'AC-01: کلاس‌های بخش حفظ شده', false !== strpos( $html, 'relative z-10 py-12 sm:py-16 lg:py-20' ) );
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="about"' ) );
	es_check( 'متن معرفی پیش‌فرض رندر می‌شود', false !== strpos( $html, 'شرکت دانش‌بنیان عرفان صنعت اصفهان' ) );
	es_check( 'تصویر پیش‌فرض کارخانه fallback دارد', false !== strpos( $html, 'images/factory.jpg' ) );
	es_check( 'آمار پیش‌فرض رندر می‌شود (۸۵۰+)', false !== strpos( $html, '۸۵۰+' ) && false !== strpos( $html, 'پروژه تکمیل‌شده' ) );

	// ── AC-03 / SC-02: counters carry numeric target + suffix for main.js ─.
	es_check( 'AC-03: data-counter-target عددی (ASCII) است', 1 === preg_match( '/data-counter-target="850"/', $html ) );
	es_check( 'AC-03: data-counter-suffix موجود است', false !== strpos( $html, 'data-counter-suffix="+"' ) );
	es_check( 'SC-02: هر چهار شمارنده قلاب انیمیشن دارند', 4 === substr_count( $html, 'data-counter-target=' ) );

	// ── AC-02 / SC-01: editable heading/content/stats ─────────────────────.
	$html2 = $render( array(
		'heading' => 'عنوان سفارشی درباره',
		'content' => '<p>متن معرفی سفارشی شرکت.</p>',
		'stats'   => array(
			array( 'number' => '۵۰۰+', 'label' => 'پروژه موفق' ),
			array( 'number' => '۳۰', 'label' => 'سال تجربه' ),
		),
	) );
	es_check( 'AC-02: عنوان قابل ویرایش است', false !== strpos( $html2, 'عنوان سفارشی درباره' ) );
	es_check( 'AC-02: متن قابل ویرایش است', false !== strpos( $html2, 'متن معرفی سفارشی شرکت' ) );
	es_check( 'SC-01: تغییر عدد آمار در خروجی دیده می‌شود', false !== strpos( $html2, '۵۰۰+' ) && false !== strpos( $html2, 'data-counter-target="500"' ) );
	es_check( 'SC-01: عدد بدون پسوند، پسوند پیش‌فرض می‌گیرد', false !== strpos( $html2, 'data-counter-target="30"' ) && false !== strpos( $html2, '۳۰+' ) );
	es_check( 'AC-02: برچسب آمار قابل ویرایش است', false !== strpos( $html2, 'پروژه موفق' ) && false !== strpos( $html2, 'سال تجربه' ) );

	// ── Custom image ──────────────────────────────────────────────────────.
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'about-img', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/about-custom.jpg' );
	$html3 = $render( array( 'image' => $att ) );
	es_check( 'AC-02: تصویر سفارشی اعمال می‌شود', false !== strpos( $html3, 'about-custom.jpg' ) && false === strpos( $html3, 'images/factory.jpg' ) );

	// ── Escaping: heading escaped, content kses ───────────────────────────.
	$html4 = $render( array( 'heading' => '<b>bold</b>', 'content' => '<p>ok</p><script>alert(1)</script>' ) );
	es_check( 'عنوان escape می‌شود', false === strpos( $html4, '<b>bold</b>' ) && false !== strpos( $html4, '&lt;b&gt;' ) );
	es_check( 'متن rich با wp_kses_post پاکسازی می‌شود', false === strpos( $html4, '<script>alert(1)</script>' ) && false !== strpos( $html4, '<p>ok</p>' ) );

	wp_delete_post( $att, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
