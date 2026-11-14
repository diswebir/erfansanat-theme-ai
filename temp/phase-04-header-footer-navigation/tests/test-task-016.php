<?php
/**
 * P04-T016 — CTA مشترک تماس
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-016.php
 *
 * The CTA is a template part (no wp_head/wp_footer), so it can be re-rendered
 * per option state. footer.php includes it globally (front page + shop).
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Store;

es_task( 'P04-T016', 'CTA مشترک تماس' );

es_check( 'target: cta-contact.php', es_file( 'template-parts/global/cta-contact.php' ) );
es_check( 'SC-01: footer.php بخش CTA را فراخوانی می‌کند', es_contains( 'footer.php', "get_template_part( 'template-parts/global/cta-contact' )" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$render = static function () {
		ob_start();
		get_template_part( 'template-parts/global/cta-contact' );
		return (string) ob_get_clean();
	};

	// Default render.
	$html = $render();
	es_check( 'AC-01: id=contact حفظ شده', false !== strpos( $html, 'id="contact"' ) );
	es_check( 'AC-01: کلاس scroll-mt-24 حفظ شده', false !== strpos( $html, 'scroll-mt-24' ) );
	es_check( 'SC-01: CTA رندر می‌شود', false !== strpos( $html, 'مشاوره مهندسی و استعلام قیمت' ) );
	es_check( 'AC-02: تیتر پیش‌فرض', false !== strpos( $html, 'روشنایی و هویت بصری متمایز' ) );
	es_check( 'AC-02: بخش برجسته تیتر (span سبز)', false !== strpos( $html, '<span class="text-emerald-700">شهر خود</span>' ) );
	es_check( 'AC-02: دکمه تماس از options', false !== strpos( $html, 'تماس مستقیم با کارشناسان فروش' ) );
	es_check( 'AC-02: دکمه پیش‌فاکتور از options', false !== strpos( $html, 'درخواست پیش‌فاکتور رسمی ستاد' ) );
	es_check( 'AC-02: تلفن از options (tel:)', false !== strpos( $html, 'href="tel:031-91091011"' ) );
	es_check( 'AC-02: نمایش تلفن با ارقام فارسی', false !== strpos( $html, '۰۳۱ - ۹۱۰۹۱۰۱۱' ) );
	es_check( 'AC-02: ایمیل از options (mailto:)', false !== strpos( $html, 'href="mailto:info@erfansanat.com"' ) );
	es_check( 'AC-02: ساعات پاسخگویی از options', false !== strpos( $html, 'شنبه تا پنجشنبه · ۷:۰۰ الی ۱۶:۳۰' ) );
	es_check( 'AC-02: لینک پیش‌فاکتور به برگه تماس (fallback)', false !== strpos( $html, '/contact' ) );

	// AC-02: content is editable.
	Store::set( 'cta_heading', 'تیتر آزمایشی سفارشی' );
	Store::set( 'cta_phone_label', 'دکمه تماس آزمایشی' );
	Store::set( 'contact_phone', '021-12345678' );
	Store::set( 'cta_quote_url', 'https://example.com/quote' );
	$html = $render();
	es_check( 'AC-02: تیتر قابل ویرایش', false !== strpos( $html, 'تیتر آزمایشی سفارشی' ) );
	es_check( 'AC-02: متن دکمه تماس قابل ویرایش', false !== strpos( $html, 'دکمه تماس آزمایشی' ) );
	es_check( 'AC-02: تلفن قابل ویرایش', false !== strpos( $html, 'href="tel:021-12345678"' ) );
	es_check( 'AC-02: لینک پیش‌فاکتور قابل ویرایش', false !== strpos( $html, 'href="https://example.com/quote"' ) );
	Store::delete( 'cta_heading' );
	Store::delete( 'cta_phone_label' );
	Store::delete( 'contact_phone' );
	Store::delete( 'cta_quote_url' );

	// AC-03 / SC-02: disabling removes the DOM entirely.
	Store::set( 'cta_enabled', false );
	$html = $render();
	es_check( 'AC-03: خاموش‌کردن CTA خروجی را حذف می‌کند', '' === trim( $html ) );
	es_check( 'SC-02: id=contact پس از خاموش‌سازی نیست', false === strpos( $html, 'id="contact"' ) );
	Store::delete( 'cta_enabled' );
	es_check( 'AC-03: پس از حذف کلید، CTA دوباره فعال است', false !== strpos( $render(), 'id="contact"' ) );

	// Escape: injected markup in heading is neutralized.
	Store::set( 'cta_heading', '<script>alert(1)</script>标题' );
	$html = $render();
	es_check( 'AC-02: خروجی escape می‌شود', false === strpos( $html, '<script>alert(1)</script>' ) );
	Store::delete( 'cta_heading' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
