<?php
/**
 * Header / announcement / logo tab.
 *
 * Defaults are extracted verbatim from static-mirror/index.html (see Schema).
 * Provides the data source the Phase-4 header.php will consume.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options\Tabs;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Schema;

final class HeaderTab {

	public const ID = 'header';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		// The Phase-4 header template reads these accessors; no front-end hook
		// is needed here yet. Boot is kept for lifecycle consistency.
	}

	public static function id(): string {
		return self::ID;
	}

	/**
	 * Field keys belonging to this tab.
	 *
	 * @return array<int,string>
	 */
	public static function fields(): array {
		return array_keys(
			array_filter(
				Schema::fields(),
				static function ( $field ) {
					return ( $field['tab'] ?? '' ) === self::ID;
				}
			)
		);
	}

	/**
	 * Mirror-derived defaults for this tab.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults(): array {
		$defaults = array();
		foreach ( self::fields() as $key ) {
			$defaults[ $key ] = Schema::default( $key );
		}
		return $defaults;
	}

	/**
	 * Announcement bar data (enabled flag + texts + quick links).
	 *
	 * @return array{enabled:bool,title:string,subtitle:string,links:array}
	 */
	public static function announcement(): array {
		return array(
			'enabled'  => (bool) es_option( 'announcement_enabled' ),
			'title'    => (string) es_option( 'announcement_title' ),
			'subtitle' => (string) es_option( 'announcement_subtitle' ),
			'links'    => (array) es_option( 'announcement_links' ),
		);
	}

	public static function announcement_enabled(): bool {
		return (bool) es_option( 'announcement_enabled' );
	}

	/**
	 * Logo URL with a safe fallback to the bundled SVG (AC-03).
	 */
	public static function logo_url(): string {
		$logo_id = (int) es_option( 'logo_id' );

		if ( $logo_id > 0 ) {
			$url = wp_get_attachment_image_url( $logo_id, 'full' );
			if ( $url ) {
				return $url;
			}
		}

		return ES_ASSETS_URI . 'images/ese-logo.svg';
	}

	public static function brand_name(): string {
		return (string) es_option( 'brand_name' );
	}

	public static function brand_tagline(): string {
		return (string) es_option( 'brand_tagline' );
	}
}
