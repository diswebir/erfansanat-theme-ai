<?php
/**
 * Home / hero tab (P05-T005).
 *
 * The main hero video is a global theme option, independent from the
 * per-page project slides (AC-01). Poster and preload are configurable
 * (AC-03); when no video is selected the hero falls back to an image
 * (AC-02) so removing the video never breaks the hero (SC-02).
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options\Tabs;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Schema;

final class HomeTab {

	public const ID = 'home';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		// The hero template part reads these accessors; no front-end hook is
		// needed here. Boot is kept for lifecycle consistency.
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
	 * Selected main hero video attachment ID (0 = none).
	 */
	public static function hero_video_id(): int {
		return (int) es_option( 'hero_video_id' );
	}

	/**
	 * Main hero video file URL, or '' when no valid video is selected.
	 */
	public static function hero_video_url(): string {
		$id = self::hero_video_id();
		if ( $id <= 0 ) {
			return '';
		}
		$url = wp_get_attachment_url( $id );
		return $url ? (string) $url : '';
	}

	/**
	 * Poster image URL (falls back to the bundled hero image).
	 */
	public static function hero_video_poster_url(): string {
		$id = (int) es_option( 'hero_video_poster' );
		if ( $id > 0 ) {
			$url = wp_get_attachment_image_url( $id, 'large' );
			if ( $url ) {
				return (string) $url;
			}
		}
		return ES_ASSETS_URI . 'images/hero.jpg';
	}

	/**
	 * Preload policy, constrained to the three valid HTML values (AC-03).
	 */
	public static function hero_video_preload(): string {
		$value = (string) es_option( 'hero_video_preload' );
		return in_array( $value, array( 'auto', 'metadata', 'none' ), true ) ? $value : 'metadata';
	}

	/**
	 * Fallback image URL used when no video is selected (AC-02). Prefers the
	 * dedicated fallback image, then the poster, then the bundled hero image.
	 */
	public static function hero_video_fallback_url(): string {
		$id = (int) es_option( 'hero_video_fallback' );
		if ( $id > 0 ) {
			$url = wp_get_attachment_image_url( $id, 'large' );
			if ( $url ) {
				return (string) $url;
			}
		}
		return self::hero_video_poster_url();
	}
}
