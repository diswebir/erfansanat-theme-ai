<?php
/**
 * Test — Task 1.2: Autoloader PSR-4 دستی
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

use function EsTest\php_code;

echo "== Task 1.2: autoloader ==\n";

$code = php_code( 'inc/Autoloader.php' );
check( 'autoloader file exists', fn() => null !== $code );
check( 'uses spl_autoload_register', fn() => str_contains( (string) $code, 'spl_autoload_register' ) );
check( 'prefix is ErfanSanat\\Theme\\', fn() => str_contains( (string) $code, "PREFIX = 'ErfanSanat\\\\Theme\\\\\\\\'" ) || str_contains( (string) $code, 'PREFIX = \'ErfanSanat\\\\Theme\\\\\'' ) );
check( 'uses strncmp prefix guard (namespace boundary)', fn() => str_contains( (string) $code, 'strncmp' ) );
check( 'maps namespace to inc/ directory', fn() => str_contains( (string) $code, "'/inc/'" ) );
check( 'caches resolved paths', fn() => str_contains( (string) $code, '$paths[' ) );
check( 'rejects invalid class-name characters', fn() => str_contains( (string) $code, 'preg_match' ) );
check( 'registers itself on load', fn() => str_contains( (string) $code, '::register()' ) );

$fn = php_code( 'functions.php' );
check( 'functions.php requires only inc/bootstrap.php', function () use ( $fn ) {
	if ( null === $fn ) { return false; }
	preg_match_all( '/require(?:_once)?\s+[^;]+;/', (string) $fn, $m );
	return 1 === count( $m[0] ) && str_contains( $m[0][0], 'inc/bootstrap.php' );
} );

check( 'no manual require of class files in bootstrap/Theme', function () {
	foreach ( array( 'inc/bootstrap.php', 'inc/Theme.php' ) as $f ) {
		$c = (string) php_code( $f );
		if ( preg_match( '/require(?:_once)?\s+[^;]*inc\/(Assets|Setup|Performance|Admin|Content|Helpers|Meta|Sections|Contracts)\//', $c ) ) {
			return false;
		}
	}
	return true;
} );

check( 'bootstrap defines constants before autoloader', function () {
	$c     = (string) php_code( 'inc/bootstrap.php' );
	$c_pos = strpos( $c, 'Constants.php' );
	$a_pos = strpos( $c, 'Autoloader.php' );
	return false !== $c_pos && false !== $a_pos && $c_pos < $a_pos;
} );

check( 'kernel class resolves through the autoloader at runtime', function () {
	if ( ! EsTest\activate_theme() ) { return false; }
	return class_exists( 'ErfanSanat\\Theme\\Theme', false )
		&& class_exists( 'ErfanSanat\\Theme\\Assets\\AssetManager', false )
		&& class_exists( 'ErfanSanat\\Theme\\Performance\\DeBloat', false );
} );

check( 'unknown theme class does not fatal', function () {
	if ( ! function_exists( 'get_stylesheet' ) ) { return null; }
	return ! class_exists( 'ErfanSanat\\Theme\\Does\\Not\\Exist' );
} );

summary();
