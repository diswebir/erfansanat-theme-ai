<?php
/**
 * Builds a self-contained visual preview of the theme admin dashboard
 * (overview tab + settings tabs) with all fonts embedded as base64.
 * Output: preview/admin-dashboard.html
 */
require __DIR__ . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ThemeOptions;

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';
wp_set_current_user( 1 );

ob_start();
ThemeOptions::render_dashboard();
$dashboard = ob_get_clean();

$font_css    = (string) file_get_contents( ES_THEME . '/assets/admin/font.css' );
$options_css = (string) file_get_contents( ES_THEME . '/assets/admin/options.css' );
$dashicons   = (string) file_get_contents( ES_WP . '/wp-includes/css/dashicons.css' );

$b64 = static function ( $path ) {
	return 'data:font/woff2;base64,' . base64_encode( (string) file_get_contents( $path ) );
};

// Embed Vazirmatn subsets.
foreach ( array( 'arabic', 'latin-ext', 'latin' ) as $subset ) {
	$font_css = str_replace(
		'url("../fonts/vazirmatn-' . $subset . '.woff2")',
		'url("' . $b64( ES_THEME . '/assets/fonts/vazirmatn-' . $subset . '.woff2' ) . '")',
		$font_css
	);
}

// Embed dashicons (replace its @font-face src with the data URI).
$dashicons_data = $b64( ES_WP . '/wp-includes/fonts/dashicons.woff2' );
$dashicons      = preg_replace(
	'/@font-face\s*\{[^}]*\}/',
	'@font-face{font-family:dashicons;src:url("' . $dashicons_data . '") format("woff2");font-weight:400;font-style:normal;font-display:block;}',
	$dashicons,
	1
);

$body_reset = 'html{background:#f0f0f1;}body{margin:0;padding:24px;background:#f0f0f1;font-family:"Vazirmatn","Segoe UI",Tahoma,sans-serif;}';

$options_js  = (string) file_get_contents( ES_THEME . '/assets/admin/options.js' );
$repeater_js = (string) file_get_contents( ES_THEME . '/assets/admin/repeater.js' );
// Preview-only config: keep tab navigation working; neutralise the AJAX save.
$preview_js = 'window.ES_OPTIONS={ajaxUrl:"",nonce:"",action:"es_save_options"};' . $options_js . $repeater_js;

$html = '<!DOCTYPE html><html dir="rtl" lang="fa"><head><meta charset="utf-8">'
	. '<meta name="viewport" content="width=device-width,initial-scale=1">'
	. '<title>پیش‌نمایش داشبورد عرفان صنعت</title>'
	. '<style>' . $dashicons . $font_css . $options_css . $body_reset . '</style>'
	. '</head><body class="es-admin-vazirmatn wp-admin">' . $dashboard
	. '<script>' . $preview_js . '</script></body></html>';

$out_dir = dirname( __DIR__ ) . '/preview';
if ( ! is_dir( $out_dir ) ) {
	mkdir( $out_dir, 0755, true );
}
file_put_contents( $out_dir . '/admin-dashboard.html', $html );
echo 'Wrote ' . $out_dir . '/admin-dashboard.html (' . strlen( $html ) . " bytes)\n";
