<?php
/**
 * P05-T017 — سکشن پشتیبانی فنی و گارانتی تعویض
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T017', 'سکشن پشتیبانی فنی و گارانتی تعویض' );

es_check( 'target: بخش پشتیبانی ساخته شده', es_file( 'template-parts/sections/home/section-support.php' ) );
es_check( 'AC-02: لینک گارانتی از Menus::page_url', es_contains( 'template-parts/sections/home/section-support.php', "Menus::page_url( 'refund_returns' )" ) );
es_check( 'SC-02: ساخت لینک tel با نرمال‌سازی صحیح', es_contains( 'template-parts/sections/home/section-support.php', "'tel:' . \$es_tel" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'support', 'type' => 'support', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// ── AC-03: reference classes + map preserved ──────────────────────────.
	$html = $render();
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="support"' ) );
	es_check( 'AC-03: کارت گرادیان سبز مرجع', false !== strpos( $html, 'rounded-[2.25rem] border-0 bg-gradient-to-l from-[#064e3b] via-[#065f46] to-[#047857]' ) );
	es_check( 'AC-03: کلاس باکس آمار حفظ شده', 3 === substr_count( $html, 'rounded-xl bg-white/10 px-3.5 py-3 backdrop-blur-xs' ) );
	es_check( 'AC-03: نقشه ایران با مرکزیت اصفهان', false !== strpos( $html, 'viewBox="0 0 680 620"' ) && false !== strpos( $html, 'اصفهان (ESE)' ) );
	es_check( 'AC-03: نشان شبکه خدمات پس از فروش', false !== strpos( $html, 'شبکه خدمات پس از فروش ESE' ) );

	// ── AC-02: warranty stat links to refund_returns ──────────────────────.
	$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => 'refund_returns', 'post_title' => 'شرایط گارانتی و بازگشت' ) );
	$html_p = $render();
	$page_url = get_permalink( $page );
	es_check( 'AC-02: لینک گارانتی به صفحه refund_returns', false !== strpos( $html_p, 'href="' . esc_url( $page_url ) . '"' ) );
	es_check( 'AC-02: عنوان آمار گارانتی', false !== strpos( $html_p, '۲۴ ماه گارانتی' ) );
	wp_delete_post( $page, true );
	$html_np = $render();
	es_check( 'AC-02: بدون صفحه → fallback home_url/refund_returns', false !== strpos( $html_np, 'href="' . esc_url( home_url( '/refund_returns/' ) ) . '"' ) );

	// ── AC-01 / SC-01: editable heading/content/phone ─────────────────────.
	$html_ed = $render( array(
		'heading' => 'پشتیبانی سفارشی من',
		'content' => '<p>متن پشتیبانی کاملاً جدید برای تست.</p>',
		'phone'   => '021-12345678',
	) );
	es_check( 'AC-01: عنوان قابل ویرایش', false !== strpos( $html_ed, 'پشتیبانی سفارشی من' ) );
	es_check( 'SC-01: تغییر متن پشتیبانی دیده می‌شود', false !== strpos( $html_ed, 'متن پشتیبانی کاملاً جدید برای تست' ) );
	es_check( 'AC-01: تلفن قابل ویرایش (نمایش فارسی)', false !== strpos( $html_ed, '۰۲۱-۱۲۳۴۵۶۷۸' ) );

	// ── SC-02: contact button tel link is correct ─────────────────────────.
	es_check( 'SC-02: لینک تماس tel: نرمال‌شده', false !== strpos( $html_ed, 'href="tel:02112345678"' ) );
	es_check( 'SC-02: پیش‌فرض tel:03191091011', false !== strpos( $html, 'href="tel:03191091011"' ) );
	es_check( 'SC-02: نمایش فارسی پیش‌فرض', false !== strpos( $html, '۰۳۱-۹۱۰۹۱۰۱۱' ) );
	es_check( 'SC-02: فقط یک tel: در لینک', false === strpos( $html, 'tel:tel:' ) );

	// ── Escaping ──────────────────────────────────────────────────────────.
	$html_esc = $render( array( 'heading' => '<b>bold</b>', 'content' => '<p>ok</p><script>alert(1)</script>' ) );
	es_check( 'عنوان escape می‌شود', false === strpos( $html_esc, '<b>bold</b>' ) && false !== strpos( $html_esc, '&lt;b&gt;' ) );
	es_check( 'متن rich با wp_kses_post (script حذف)', false === strpos( $html_esc, '<script>' ) && false !== strpos( $html_esc, '<p>ok</p>' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
