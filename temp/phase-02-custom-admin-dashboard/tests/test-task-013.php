<?php
/**
 * P02-T013 — تب برند/رنگ و اسکریپت سفارشی
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Tabs\BrandTab;
use ErfanSanat\Theme\Options\Store;
use ErfanSanat\Theme\Options\Sanitizer;

es_task( 'P02-T013', 'تب برند/رنگ و اسکریپت سفارشی' );

es_check( 'target: inc/Options/Tabs/BrandTab.php', es_file( 'inc/Options/Tabs/BrandTab.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	Store::reset();

	// AC-01: only the allowlisted CSS variables are produced.
	$vars = BrandTab::color_variables();
	es_check( 'AC-01: فقط CSS variableهای مجاز تولید می‌شوند', array_keys( $vars ) === array_values( BrandTab::variable_map() ) );
	$all_hex = true;
	foreach ( $vars as $hex ) {
		$all_hex = $all_hex && 1 === preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $hex );
	}
	es_check( 'AC-01: همه مقادیر hex معتبر هستند', $all_hex );
	es_check( 'AC-01: خروجی CSS شامل :root است', false !== strpos( BrandTab::css_variables(), ':root{' ) );

	// SC-01: an invalid color is rejected / falls back to default.
	Store::set( 'brand_primary', 'notacolor' );
	es_check( 'SC-01: رنگ نامعتبر به پیش‌فرض برمی‌گردد', '#047857' === BrandTab::color_variables()['--es-brand-primary'] );
	es_check( 'SC-01: Sanitizer رنگ نامعتبر را رد می‌کند', is_wp_error( Sanitizer::sanitize_field( array( 'type' => 'color' ), 'notacolor' ) ) );
	Store::reset();

	// SC-02: custom code prints only in its designated location (front-end).
	Store::set( 'custom_header_script', '<script>/*es-head-code*/</script>' );
	Store::set( 'custom_footer_script', '<script>/*es-footer-code*/</script>' );

	ob_start();
	BrandTab::head_output();
	$head = ob_get_clean();
	es_check( 'SC-02: کد هدر فقط در wp_head چاپ می‌شود', false !== strpos( $head, '/*es-head-code*/' ) && false === strpos( $head, '/*es-footer-code*/' ) );

	ob_start();
	BrandTab::footer_output();
	$footer = ob_get_clean();
	es_check( 'SC-02: کد فوتر فقط در wp_footer چاپ می‌شود', false !== strpos( $footer, '/*es-footer-code*/' ) && false === strpos( $footer, '/*es-head-code*/' ) );

	// AC-02: custom code survives sanitization (admin-only field).
	$code = '<script>console.log(1)</script>';
	es_check( 'AC-02: فیلد code برای نقش مجاز حفظ می‌شود', Sanitizer::sanitize_field( array( 'type' => 'code' ), $code ) === $code );

	// AC-03: nothing leaks into wp-admin.
	if ( ! defined( 'WP_ADMIN' ) ) {
		define( 'WP_ADMIN', true );
	}
	ob_start();
	BrandTab::head_output();
	BrandTab::footer_output();
	$admin_output = ob_get_clean();
	es_check( 'AC-03: تزریق CSS/JS به admin نشت نمی‌کند', '' === $admin_output );

	Store::reset();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
