<?php
/**
 * Test — Task 1.3: Kernel و Theme Supports
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

use function EsTest\php_code;

echo "== Task 1.3: kernel + theme supports ==\n";

$theme = (string) php_code( 'inc/Theme.php' );
$supp  = (string) php_code( 'inc/Setup/ThemeSupport.php' );

check( 'kernel uses a controlled singleton', fn() => str_contains( $theme, 'self::$instance' ) && str_contains( $theme, 'private function __construct' ) );
check( 'kernel boots only once', fn() => str_contains( $theme, '$this->booted' ) );
check( 'modules implement ModuleInterface', fn() => str_contains( $supp, 'implements ModuleInterface' ) );
check( 'module contract declares register(): void', fn() => str_contains( (string) php_code( 'inc/Contracts/ModuleInterface.php' ), 'public function register(): void' ) );
check( 'kernel exposes an es_theme_booted action', fn() => str_contains( $theme, "'es_theme_booted'" ) );
check( 'kernel never echoes', fn() => ! preg_match( '/\b(echo|print|var_dump|printf)\b/', $theme ) );

check( 'setup runs on after_setup_theme', fn() => str_contains( $supp, "add_action( 'after_setup_theme'" ) );
foreach ( array( 'title-tag', 'post-thumbnails', 'custom-logo', 'automatic-feed-links', 'responsive-embeds' ) as $support ) {
	check( "registers theme support: $support", fn() => str_contains( $supp, "'" . $support . "'" ) );
}
check( 'registers html5 support list', fn() => str_contains( $supp, "'html5'" ) && substr_count( $supp, "'search-form'" ) === 1 );
check( 'align-wide is NOT registered', fn() => ! str_contains( $supp, "add_theme_support( 'align-wide'" ) );
check( 'woocommerce support is guarded by class_exists', fn() => str_contains( $supp, "class_exists( 'WooCommerce' )" ) );
check( 'loads the erfansanat text domain', fn() => str_contains( $supp, "load_theme_textdomain( 'erfansanat'" ) );

check( 'runtime: theme is active and supports are registered', function () {
	if ( ! EsTest\activate_theme() ) { return false; }
	return current_theme_supports( 'title-tag' )
		&& current_theme_supports( 'post-thumbnails' )
		&& current_theme_supports( 'html5' )
		&& current_theme_supports( 'custom-logo' );
} );

check( 'runtime: align-wide is absent', function () {
	if ( ! function_exists( 'current_theme_supports' ) ) { return null; }
	return ! current_theme_supports( 'align-wide' );
} );

check( 'runtime: kernel modules are registered', function () {
	if ( ! function_exists( 'did_action' ) ) { return null; }
	return did_action( 'es_theme_booted' ) >= 1
		&& count( \ErfanSanat\Theme\Theme::instance()->module_keys() ) >= 5;
} );

check( 'runtime: woocommerce support absent when WooCommerce is inactive', function () {
	if ( ! function_exists( 'current_theme_supports' ) || class_exists( 'WooCommerce' ) ) { return null; }
	return ! current_theme_supports( 'woocommerce' );
} );

summary();
