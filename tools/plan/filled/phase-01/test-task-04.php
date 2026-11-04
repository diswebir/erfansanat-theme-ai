<?php
/**
 * Test — Task 1.4: functions.php سبک + index.php + screenshot + پوسته سند
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

use function EsTest\php_code;

echo "== Task 1.4: functions.php + index.php ==\n";

$fn    = (string) php_code( 'functions.php' );
$lines = count( preg_split( '/\R/', (string) \EsTest\tfile( 'functions.php' ) ) );

check( 'functions.php is <= 40 lines', fn() => $lines <= 40 );
check( 'functions.php has the ABSPATH guard', fn() => str_contains( $fn, "defined( 'ABSPATH' )" ) );
check( 'functions.php has no business logic', fn() => ! preg_match( '/\b(add_action|add_filter|register_post_type|wp_enqueue)/', $fn ) );
check( 'functions.php has no output statements', fn() => ! preg_match( '/\b(echo|print_r|var_dump|printf)\b/', $fn ) );

$idx = (string) php_code( 'index.php' );
check( 'index.php calls get_header()', fn() => str_contains( $idx, 'get_header()' ) );
check( 'index.php calls get_footer()', fn() => str_contains( $idx, 'get_footer()' ) );
check( 'index.php handles the empty state', fn() => str_contains( $idx, 'have_posts()' ) && str_contains( $idx, 'محتوایی یافت نشد' ) );
check( 'index.php escapes its output', fn() => str_contains( $idx, 'esc_html_e' ) && str_contains( $idx, 'the_permalink()' ) );
check( 'index.php has the ABSPATH guard', fn() => str_contains( $idx, "defined( 'ABSPATH' )" ) );

check( 'screenshot.png exists with 1200x900', function () {
	$p = \EsTest\tpath( 'screenshot.png' );
	if ( ! is_file( $p ) ) { return false; }
	$info = @getimagesize( $p );
	return $info && 1200 === (int) $info[0] && 900 === (int) $info[1];
} );

check( 'header.php ships a valid document head', function () {
	$c = (string) \EsTest\php_code( 'header.php' );
	return str_contains( $c, 'language_attributes()' )
		&& str_contains( $c, 'wp_head()' )
		&& str_contains( $c, 'wp_body_open()' )
		&& str_contains( $c, 'body_class()' );
} );

check( 'footer.php closes the document and calls wp_footer()', function () {
	$c = (string) \EsTest\php_code( 'footer.php' );
	return str_contains( $c, 'wp_footer()' ) && str_contains( $c, '</html>' );
} );

check( 'header/footer stay free of static page copy (Phase 4 owns it)', function () {
	foreach ( array( 'header.php', 'footer.php' ) as $f ) {
		$c = (string) \EsTest\tfile( $f );
		if ( str_contains( $c, '.html"' ) || str_contains( $c, 'erfansanat.com' ) ) {
			return false;
		}
	}
	return true;
} );

check( 'every theme php file lints cleanly', function () {
	foreach ( \EsTest\theme_php_files() as $f ) {
		if ( null !== \EsTest\php_lint( \EsTest\tpath( $f ) ) ) { return false; }
	}
	return true;
} );

check( 'every theme php file has the ABSPATH guard', function () {
	$missing = array();
	foreach ( \EsTest\theme_php_files() as $f ) {
		if ( 'index.php' === $f ) { continue; }
		$c = (string) \EsTest\tfile( $f );
		if ( ! str_contains( $c, "defined( 'ABSPATH' )" ) ) {
			$missing[] = $f;
		}
	}
	return array() === $missing;
} );

summary();
