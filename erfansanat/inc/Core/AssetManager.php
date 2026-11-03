<?php
/**
 * Conditional asset registration/enqueue with filemtime versioning.
 *
 * The static mirror ships one compiled design-system stylesheet and one
 * interaction script. They are shared across front-end templates, but never
 * loaded in wp-admin. Profiles keep future page-specific assets isolated.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class AssetManager {

	public const STYLE_HANDLE  = 'es-styles';
	public const SCRIPT_HANDLE = 'es-main';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}

		self::$booted = true;
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ), 10 );
	}

	/**
	 * Canonical asset registry.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function assets(): array {
		return array(
			self::STYLE_HANDLE  => array(
				'type' => 'style',
				'src'  => ES_ASSETS_URI . 'css/styles.css',
				'path' => ES_ASSETS_DIR . 'css/styles.css',
				'deps' => array(),
			),
			self::SCRIPT_HANDLE => array(
				'type'     => 'script',
				'src'      => ES_ASSETS_URI . 'js/main.js',
				'path'     => ES_ASSETS_DIR . 'js/main.js',
				'deps'     => array(),
				'footer'   => true,
			),
		);
	}

	/**
	 * Asset profiles per template family. Admin receives nothing.
	 *
	 * @return array<string,array<int,string>>
	 */
	public static function profiles(): array {
		$global = array( self::STYLE_HANDLE, self::SCRIPT_HANDLE );

		return array(
			'admin'    => array(),
			'home'     => $global,
			'single'   => $global,
			'archive'  => $global,
			'page'     => $global,
			'checkout' => $global,
			'account'  => $global,
			'auth'     => $global,
		);
	}

	public static function register_assets(): void {
		foreach ( self::assets() as $handle => $asset ) {
			$version = is_file( $asset['path'] ) ? (string) filemtime( $asset['path'] ) : ES_VERSION;

			if ( 'style' === $asset['type'] ) {
				if ( ! wp_style_is( $handle, 'registered' ) ) {
					wp_register_style( $handle, $asset['src'], $asset['deps'], $version );
				}
			} elseif ( ! wp_script_is( $handle, 'registered' ) ) {
				wp_register_script( $handle, $asset['src'], $asset['deps'], $version, (bool) $asset['footer'] );
			}
		}
	}

	public static function current_profile(): string {
		if ( is_admin() ) {
			return 'admin';
		}

		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return 'checkout';
		}

		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			return 'account';
		}

		if ( is_front_page() ) {
			return 'home';
		}

		if ( is_singular() ) {
			return 'single';
		}

		if ( is_archive() || is_home() ) {
			return 'archive';
		}

		return 'page';
	}

	/**
	 * Enqueue a named profile. Exposed for deterministic tests.
	 */
	public static function enqueue_for_profile( string $profile ): void {
		if ( 'admin' === $profile ) {
			return;
		}

		self::register_assets();

		$profiles = self::profiles();
		$handles  = $profiles[ $profile ] ?? array();

		foreach ( $handles as $handle ) {
			if ( wp_style_is( $handle, 'registered' ) ) {
				wp_enqueue_style( $handle );
			} elseif ( wp_script_is( $handle, 'registered' ) ) {
				wp_enqueue_script( $handle );
			}
		}
	}

	public static function enqueue(): void {
		self::enqueue_for_profile( self::current_profile() );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}
}
