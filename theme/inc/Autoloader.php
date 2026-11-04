<?php
/**
 * Minimal PSR-4 autoloader (no Composer).
 *
 * `ErfanSanat\Theme\Assets\AssetManager`  ->  inc/Assets/AssetManager.php
 *
 * @package ErfanSanat\Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ErfanSanat\Theme\Autoloader', false ) ) {

	/**
	 * Class autoloader for the theme namespace.
	 */
	final class ErfanSanat_Theme_Autoloader {

		/**
		 * Namespace prefix mapped to the inc/ directory.
		 */
		private const PREFIX = 'ErfanSanat\\Theme\\';

		/**
		 * Resolved class => absolute path cache.
		 *
		 * @var array<string,string|false>
		 */
		private static array $paths = array();

		/**
		 * Whether the autoloader is already registered.
		 */
		private static bool $registered = false;

		/**
		 * Register the autoloader once.
		 */
		public static function register(): void {
			if ( self::$registered ) {
				return;
			}
			self::$registered = true;
			spl_autoload_register( array( __CLASS__, 'load' ), true, true );
		}

		/**
		 * Resolve a theme class name to its file and require it.
		 *
		 * @param string $class Fully qualified class, interface or trait name.
		 */
		public static function load( string $class ): void {
			$path = self::resolve( $class );

			if ( false === $path ) {
				return;
			}

			require_once $path;
		}

		/**
		 * Map a fully qualified name to an absolute file path.
		 *
		 * @param string $class Class name.
		 * @return string|false Absolute path or false when not ours / missing.
		 */
		public static function resolve( string $class ) {
			if ( isset( self::$paths[ $class ] ) ) {
				return self::$paths[ $class ];
			}

			$length = strlen( self::PREFIX );

			// Cheap prefix check: namespace boundary must match exactly.
			if ( 0 !== strncmp( $class, self::PREFIX, $length ) ) {
				self::$paths[ $class ] = false;
				return false;
			}

			$relative = substr( $class, $length );

			// Reject empty and structurally invalid names before touching disk.
			if ( '' === $relative || preg_match( '/[^A-Za-z0-9_\\\\]/', $relative ) ) {
				self::$paths[ $class ] = false;
				return false;
			}

			$relative = str_replace( '\\', '/', $relative );
			$file     = ES_THEME_DIR . '/inc/' . $relative . '.php';

			if ( ! is_file( $file ) ) {
				self::$paths[ $class ] = false;
				return false;
			}

			self::$paths[ $class ] = $file;

			return $file;
		}

		/**
		 * Reset internal state (test helper only).
		 *
		 * @internal
		 */
		public static function reset_cache(): void {
			self::$paths = array();
		}
	}
}

if ( ! class_exists( 'ErfanSanat\\Theme\\Autoloader', false ) ) {
	class_alias( 'ErfanSanat_Theme_Autoloader', 'ErfanSanat\\Theme\\Autoloader' );
}

ErfanSanat_Theme_Autoloader::register();
