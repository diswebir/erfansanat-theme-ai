<?php
/**
 * PSR-4-like autoloader for ErfanSanat\Theme classes.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

final class Autoloader {

	private static bool $registered = false;

	public static function register(): void {
		if ( self::$registered ) {
			return;
		}

		spl_autoload_register( array( self::class, 'load' ) );
		self::$registered = true;
	}

	public static function is_registered(): bool {
		return self::$registered;
	}

	public static function load( string $class ): void {
		$prefix = 'ErfanSanat\\Theme\\';

		if ( ! str_starts_with( $class, $prefix ) ) {
			return;
		}

		$relative = substr( $class, strlen( $prefix ) );
		$path     = __DIR__ . '/' . str_replace( '\\', '/', $relative ) . '.php';

		if ( is_file( $path ) ) {
			require_once $path;
		}
	}
}
