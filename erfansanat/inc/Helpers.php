<?php
/**
 * Global helpers: es_option() with a static, self-invalidating cache.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme {

	defined( 'ABSPATH' ) || exit;

	use ErfanSanat\Theme\Options\Schema;
	use ErfanSanat\Theme\Options\Store;

	final class Helpers {

		/** @var array<string,mixed>|null */
		private static ?array $cache = null;

		private static bool $hooks_registered = false;

		/**
		 * Read a theme option with a static per-request cache.
		 *
		 * @param mixed $default Returned when the key is unknown.
		 * @return mixed
		 */
		public static function option( string $key, $default = null ) {
			self::register_hooks();

			if ( ! Schema::is_known( $key ) ) {
				return $default;
			}

			if ( null === self::$cache ) {
				self::$cache = Store::raw();
			}

			if ( array_key_exists( $key, self::$cache ) ) {
				return self::$cache[ $key ];
			}

			$schema_default = Schema::default( $key );

			return null === $schema_default ? $default : $schema_default;
		}

		/**
		 * Invalidate the static cache (called after any option write).
		 */
		public static function flush(): void {
			self::$cache = null;
		}

		/**
		 * Whether the cache is currently warm (used by tests).
		 */
		public static function is_cached(): bool {
			return null !== self::$cache;
		}

		private static function register_hooks(): void {
			if ( self::$hooks_registered ) {
				return;
			}

			self::$hooks_registered = true;

			add_action( 'erfansanat_options_updated', array( self::class, 'flush' ), 10, 0 );
			add_action( 'updated_option', array( self::class, 'maybe_flush' ), 10, 1 );
			add_action( 'deleted_option', array( self::class, 'maybe_flush' ), 10, 1 );
		}

		/**
		 * Flush only when our single option is touched directly.
		 *
		 * @param string $option Option name.
		 */
		public static function maybe_flush( $option ): void {
			if ( Store::OPTION === $option ) {
				self::flush();
			}
		}
	}
}

namespace {

	if ( ! function_exists( 'es_option' ) ) {
		/**
		 * Read a theme option (static-cached, schema-aware).
		 *
		 * @param mixed $default Fallback for unknown keys.
		 * @return mixed
		 */
		function es_option( string $key, $default = null ) {
			return \ErfanSanat\Theme\Helpers::option( $key, $default );
		}
	}
}
