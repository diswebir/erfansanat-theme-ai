<?php
/**
 * Secure PSR-4-style autoloader for the theme namespace.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Loads ErfanSanat\Theme classes from inc/ without allowing path traversal.
 */
final class Autoloader {

	/**
	 * Namespace prefix owned by this theme.
	 */
	private const PREFIX = 'ErfanSanat\\Theme\\';

	/**
	 * Register the autoloader once.
	 *
	 * @return void
	 */
	public static function register(): void {
		static $registered = false;
		if ( $registered ) {
			return;
		}
		$registered = true;
		spl_autoload_register( array( self::class, 'load' ) );
	}

	/**
	 * Resolve a class name to a file inside inc/.
	 *
	 * Unknown namespaces, malformed names and any path that escapes inc/ are
	 * ignored so a crafted class name can never include a foreign file.
	 *
	 * @param string $class_name Fully qualified class name requested by PHP.
	 * @return void
	 */
	public static function load( string $class_name ): void {
		if ( ! str_starts_with( $class_name, self::PREFIX ) ) {
			return;
		}
		$relative = substr( $class_name, strlen( self::PREFIX ) );
		if ( '' === $relative || ! preg_match( '/^[A-Za-z_][A-Za-z0-9_]*(\\\\[A-Za-z_][A-Za-z0-9_]*)*$/', $relative ) ) {
			return;
		}
		$file = __DIR__ . '/' . str_replace( '\\', '/', $relative ) . '.php';
		$real = realpath( $file );
		$base = realpath( __DIR__ );
		if ( false === $real || false === $base || ! str_starts_with( $real, $base . DIRECTORY_SEPARATOR ) || ! is_file( $real ) ) {
			return;
		}
		require_once $real;
	}
}
