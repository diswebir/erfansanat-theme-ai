<?php
/**
 * Test — Task 1.9: AssetManager + Manifest
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

use function EsTest\php_code;

echo "== Task 1.9: asset manager ==\n";

$man = (string) php_code( 'inc/Assets/Manifest.php' );
$am  = (string) php_code( 'inc/Assets/AssetManager.php' );

check( 'manifest declares the single stylesheet handle', fn() => str_contains( $man, "'es-main'" ) );
check( 'manifest points at assets/css/styles.css', fn() => str_contains( $man, 'css/styles.css' ) );
check( 'manifest points at assets/js/main.js', fn() => str_contains( $man, 'js/main.js' ) );
check( 'script is deferred in the manifest', fn() => str_contains( $man, "'defer'" ) );
check( 'script loads in the head with defer (matching the static page)', fn() => str_contains( $man, "'in_footer'" ) );
check( 'manager enqueues on wp_enqueue_scripts', fn() => str_contains( $am, "'wp_enqueue_scripts'" ) );
check( 'manager injects defer via script_loader_tag', fn() => str_contains( $am, 'script_loader_tag' ) );
check( 'manager versions assets by filemtime', fn() => str_contains( $am, 'filemtime' ) );
check( 'manager refuses to enqueue a missing file', fn() => str_contains( $am, 'is_readable' ) );
check( 'manager never hardcodes a CDN url', fn() => ! str_contains( $am, 'http://' ) && ! str_contains( $am, 'https://' ) );

check( 'runtime: es-main style is queued once', function () {
	if ( ! EsTest\activate_theme() ) { return false; }
	do_action( 'wp_enqueue_scripts' );
	return 1 === count( array_keys( wp_styles()->queue, 'es-main', true ) );
} );

check( 'runtime: es-main script is queued once', function () {
	if ( ! function_exists( 'wp_scripts' ) ) { return null; }
	return 1 === count( array_keys( wp_scripts()->queue, 'es-main', true ) );
} );

check( 'runtime: style resolves to the theme asset', function () {
	if ( ! function_exists( 'wp_styles' ) ) { return null; }
	$src = wp_styles()->registered['es-main']->src ?? '';
	return str_contains( (string) $src, '/themes/erfansanat/assets/css/styles.css' );
} );

check( 'runtime: script resolves to the theme asset', function () {
	if ( ! function_exists( 'wp_scripts' ) ) { return null; }
	$src = wp_scripts()->registered['es-main']->src ?? '';
	return str_contains( (string) $src, '/themes/erfansanat/assets/js/main.js' );
} );

check( 'runtime: version is a cache-busting stamp', function () {
	if ( ! function_exists( 'wp_styles' ) ) { return null; }
	$ver = (string) ( wp_styles()->registered['es-main']->ver ?? '' );
	return $ver !== '' && $ver !== ( defined( 'ES_THEME_VERSION' ) ? ES_THEME_VERSION : '' );
} );

summary();
