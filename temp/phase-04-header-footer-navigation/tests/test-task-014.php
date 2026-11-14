<?php
/**
 * P04-T014 — بخش اطلاعات فوتر
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Store;

es_task( 'P04-T014', 'بخش اطلاعات فوتر' );

es_check( 'target: template-parts/global/footer-info.php', es_file( 'template-parts/global/footer-info.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$render = static function () {
		ob_start();
		get_template_part( 'template-parts/global/footer-info' );
		return (string) ob_get_clean();
	};

	// Ensure no menu assigned (SC-02 baseline).
	$old_locs = get_theme_mod( 'nav_menu_locations', array() );
	set_theme_mod( 'nav_menu_locations', array() );

	$html = $render();

	// AC-01: the info columns render with their headings.
	es_check( 'AC-01: ستون دسترسی سریع', false !== strpos( $html, 'دسترسی سریع' ) );
	es_check( 'AC-01: ستون دسته‌بندی محصولات', false !== strpos( $html, 'دسته‌بندی محصولات' ) );
	es_check( 'AC-01: ستون اطلاعات تماس', false !== strpos( $html, 'اطلاعات تماس کارخانه' ) );
	es_check( 'AC-01: نشانی از options می‌آید', false !== strpos( $html, 'شهرک صنعتی برق و الکترونیک' ) );
	es_check( 'AC-01: لینک‌های دسترسی سریع پیش‌فرض', false !== strpos( $html, 'فروشگاه محصولات' ) );

	// AC-02: grid/spacing classes preserved.
	es_check( 'AC-02: کلاس grid اصلی حفظ شده', false !== strpos( $html, 'grid gap-10 pb-14 md:grid-cols-2 lg:grid-cols-12' ) );
	es_check( 'AC-02: ستون‌های lg:col-span حفظ شده', false !== strpos( $html, 'lg:col-span-4' ) && false !== strpos( $html, 'lg:col-span-2' ) && false !== strpos( $html, 'lg:col-span-3' ) );

	// SC-02: empty menu does not break columns (fallback links render).
	es_check( 'SC-02: منوی خالی ستون را خراب نمی‌کند', false !== strpos( $html, 'دسترسی سریع' ) && false !== strpos( $html, '<a href=' ) );

	// SC-01: changing the address in options changes the footer.
	Store::set( 'contact_address', 'نشانی سفارشی آزمایشی، پلاک ۹۹' );
	$html = $render();
	es_check( 'SC-01: تغییر نشانی در فوتر دیده می‌شود', false !== strpos( $html, 'نشانی سفارشی آزمایشی، پلاک ۹۹' ) );
	Store::delete( 'contact_address' );

	// AC-03: output is escaped (no raw HTML injection from options).
	Store::set( 'contact_address', 'نشانی <b>تست</b><script>x</script>' );
	$html = $render();
	es_check( 'AC-03: HTML نشانی escape می‌شود', false !== strpos( $html, '&lt;b&gt;' ) && false === strpos( $html, '<script>x</script>' ) );
	Store::delete( 'contact_address' );

	set_theme_mod( 'nav_menu_locations', $old_locs );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
