<?php
/**
 * Test — Task 1.1: هدر قالب در style.css و متادیتای نسخه
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

use function EsTest\theme_code;
use function EsTest\php_code;

echo "== Task 1.1: theme header metadata ==\n";

$style = theme_code( 'style.css' );
check( 'style.css exists', fn() => null !== $style );

$headers = $style ? EsTest\parse_headers( 'style.css', array(
	'Name' => 'Theme Name', 'Version' => 'Version', 'TextDomain' => 'Text Domain',
	'DomainPath' => 'Domain Path', 'RequiresPHP' => 'Requires PHP', 'License' => 'License',
) ) : array();

check( 'Theme Name is set', fn() => ! empty( $headers['Name'] ) );
check( 'Text Domain is erfansanat', fn() => ( $headers['TextDomain'] ?? '' ) === 'erfansanat' );
check( 'Domain Path is /languages', fn() => ( $headers['DomainPath'] ?? '' ) === '/languages' );
check( 'Version is 1.0.0', fn() => ( $headers['Version'] ?? '' ) === '1.0.0' );
check( 'Requires PHP >= 8.0', fn() => version_compare( $headers['RequiresPHP'] ?? '0', '8.0', '>=' ) );

$code = php_code( 'inc/Constants.php' );
check( 'ES_THEME_VERSION matches the header', function () use ( $code, $headers ) {
	if ( ! $code || ! preg_match( "/ES_THEME_VERSION',\\s*'([0-9.]+)'/", $code, $m ) ) {
		return false;
	}
	return $m[1] === ( $headers['Version'] ?? '' );
} );

check( 'constants are defined once (defined guard)', function () use ( $code ) {
	return $code && 1 === preg_match( "/defined\\(\\s*'ES_THEME_VERSION'\\s*\\)/", $code );
} );

check( 'no CSS rules outside comments', function () use ( $style ) {
	if ( null === $style ) { return false; }
	$css = preg_replace( '#/\\*.*?\\*/#s', '', $style );
	$css = preg_replace( '#^\\s*\\*.*$#m', '', $css );
	return ! preg_match( '/[a-zA-Z][^{}]*\\{[^}]*:[^}]*\\}/', (string) $css );
} );

check( 'ABSPATH guard present', fn() => str_contains( (string) $code, "defined( 'ABSPATH' )" ) );

summary();
