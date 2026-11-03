<?php
/**
 * Test — Task 1.8: انتقال تصاویر + ویدیو + WebP
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

use function EsTest\theme_md5;

echo "== Task 1.8: images + video + webp ==\n";

$ref_imgs = array();
foreach ( array( 'images', 'images/pexels' ) as $dir ) {
	$abs_dir = ES_REPO_ROOT . '/reference/static-mirror/' . $dir;
	if ( ! is_dir( $abs_dir ) ) { continue; }
	foreach ( (array) scandir( $abs_dir ) as $entry ) {
		if ( '.' === $entry || '..' === $entry ) { continue; }
		$abs = $abs_dir . '/' . $entry;
		if ( is_file( $abs ) && preg_match( '/\.(jpe?g|png|svg|webp)$/i', $entry ) ) {
			$ref_imgs[] = $abs;
		}
	}
}

check( 'the static mirror ships raster images', fn() => count( $ref_imgs ) > 0 );

$missing = array();
$differ  = array();
foreach ( $ref_imgs as $abs ) {
	$rel   = substr( $abs, strlen( ES_REPO_ROOT . '/reference/static-mirror/' ) );
	$theme = 'assets/' . str_replace( 'images/', 'img/', $rel );
	if ( ! is_file( EsTest\tpath( $theme ) ) ) {
		$missing[] = $theme;
		continue;
	}
	if ( theme_md5( $theme ) !== md5_file( $abs ) ) {
		$differ[] = $theme;
	}
}

check( 'every mirrored image is present in the theme', fn() => array() === $missing );
check( 'every mirrored image is byte-identical', fn() => array() === $differ );
if ( $missing ) { echo '    missing: ' . implode( ', ', array_slice( $missing, 0, 8 ) ) . "\n"; }
if ( $differ )  { echo '    differing: ' . implode( ', ', array_slice( $differ, 0, 8 ) ) . "\n"; }

check( 'video is byte-identical to the mirror', function () {
	$ref = ES_REPO_ROOT . '/reference/static-mirror/images/hero-video.mp4';
	return is_file( $ref ) && theme_md5( 'assets/video/hero-video.mp4' ) === md5_file( $ref );
} );

check( 'logo svg is present', fn() => is_file( EsTest\tpath( 'assets/img/ese-logo.svg' ) ) );
check( 'hero image exists', fn() => is_file( EsTest\tpath( 'assets/img/hero.jpg' ) ) );

$jpgs    = (array) glob( EsTest\tpath( 'assets/img/*.jpg' ) );
$no_webp = array();
$savings = array();
foreach ( $jpgs as $jpg ) {
	$webp = preg_replace( '/\.jpg$/', '.webp', $jpg );
	if ( ! is_file( $webp ) ) { $no_webp[] = basename( $jpg ); continue; }
	$savings[] = 1 - ( filesize( $webp ) / max( 1, filesize( $jpg ) ) );
}
check( 'all jpgs have a webp sibling', fn() => array() === $no_webp );
check( 'webp files are meaningfully smaller', fn() => $savings && array_sum( $savings ) / count( $savings ) >= 0.10 );
check( 'the hero LCP image has a webp version', fn() => is_file( EsTest\tpath( 'assets/img/hero.webp' ) ) );
check( 'webp files are valid RIFF containers', function () {
	foreach ( (array) glob( EsTest\tpath( 'assets/img/*.webp' ) ) as $f ) {
		$fh   = fopen( $f, 'rb' );
		$head = (string) fread( $fh, 12 );
		fclose( $fh );
		if ( 'RIFF' !== substr( $head, 0, 4 ) || 'WEBP' !== substr( $head, 8, 4 ) ) { return false; }
	}
	return true;
} );

summary();
