<?php
/**
 * P01-T008 — انتقال CSS و فونت‌ها
 * Run: php temp/phase-01-core-and-assets/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T008', 'انتقال CSS و فونت‌ها' );

$css_src = ES_MIRROR . '/assets/css/styles.css';
$css_dst = ES_THEME . '/assets/css/styles.css';
es_check( 'target: assets/css/styles.css', is_file( $css_dst ) );
es_check( 'AC-01/SC-02: hash CSS یکسان', is_file( $css_dst ) && hash_file( 'sha256', $css_src ) === hash_file( 'sha256', $css_dst ) );

$fonts = array( 'vazirmatn-arabic.woff2', 'vazirmatn-latin.woff2', 'vazirmatn-latin-ext.woff2' );
foreach ( $fonts as $font ) {
	$src = ES_MIRROR . '/assets/fonts/' . $font;
	$dst = ES_THEME . '/assets/fonts/' . $font;
	es_check( "AC-01: فونت {$font} کپی شده", is_file( $dst ) );
	es_check( "AC-01/SC-02: hash فونت {$font} یکسان", is_file( $dst ) && hash_file( 'sha256', $src ) === hash_file( 'sha256', $dst ) );
}

$css = is_file( $css_dst ) ? (string) file_get_contents( $css_dst ) : '';
es_check( 'AC-03: فونت‌ها CDN ندارند', false === strpos( $css, 'https://cdn' ) && false === strpos( $css, 'http://cdn' ) );

// AC-02: relative font url(...) paths resolve from assets/css/.
preg_match_all( '/url\(\s*[\'"]?(\.\.\/fonts\/[^\'")]+)[\'"]?\s*\)/', $css, $matches );
$resolve_ok = ! empty( $matches[1] );
foreach ( $matches[1] as $rel ) {
	$path = realpath( ES_THEME . '/assets/css/' . $rel );
	$resolve_ok = $resolve_ok && $path && is_file( $path );
}
es_check( 'AC-02: url(...) فونت‌ها resolve می‌شوند', $resolve_ok );

// SC-01: HTTP 200 report produced by temp/tools/check-assets-http.sh.
$report_path = ES_ROOT . '/temp/asset-http-report.json';
$report_ok = false;
if ( is_file( $report_path ) ) {
	$report = json_decode( (string) file_get_contents( $report_path ), true );
	$wanted = array( 'assets/css/styles.css', 'assets/fonts/vazirmatn-arabic.woff2', 'assets/fonts/vazirmatn-latin.woff2', 'assets/fonts/vazirmatn-latin-ext.woff2' );
	$report_ok = ! empty( $report['ok'] );
	foreach ( $report['results'] ?? array() as $row ) {
		if ( in_array( $row['path'], $wanted, true ) ) {
			$report_ok = $report_ok && 200 === (int) $row['status'];
		}
	}
}
es_check( 'SC-01: HTTP 200 برای CSS/woff2', $report_ok );

es_summary();
