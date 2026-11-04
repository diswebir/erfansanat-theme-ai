<?php
/**
 * View counter with the live meta contract: `number2-viewc` is the primary
 * store, `number-viewc` is a read-only legacy fallback. Increments are atomic
 * (a single SQL UPDATE) so concurrent views never lose a count.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class ViewCounter {

	public const PRIMARY  = 'number2-viewc';
	public const FALLBACK = 'number-viewc';
	public const TYPES    = array( 'post', 'projects', 'product' );

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'wp', array( __CLASS__, 'maybe_count' ) );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Read the view count: primary first, then the legacy fallback. Non-numeric
	 * values are treated as 0 (AC-03).
	 */
	public static function get( int $post_id ): int {
		if ( metadata_exists( 'post', $post_id, self::PRIMARY ) ) {
			$value = get_post_meta( $post_id, self::PRIMARY, true );
			return is_numeric( $value ) ? (int) $value : 0;
		}

		$value = get_post_meta( $post_id, self::FALLBACK, true );
		return is_numeric( $value ) ? (int) $value : 0;
	}

	/**
	 * Atomically increment the primary counter. Seeds from the fallback on the
	 * first increment; never writes the fallback key (AC-02).
	 */
	public static function increment( int $post_id ): int {
		global $wpdb;

		if ( ! metadata_exists( 'post', $post_id, self::PRIMARY ) ) {
			$seed = self::get( $post_id );
			// unique=true: if a concurrent request already created it, this fails
			// harmlessly and the atomic UPDATE below still applies.
			add_post_meta( $post_id, self::PRIMARY, $seed, true );
		}

		// Single atomic statement — no read-modify-write race (AC-04).
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->postmeta} SET meta_value = meta_value + 1 WHERE post_id = %d AND meta_key = %s",
				$post_id,
				self::PRIMARY
			)
		);

		wp_cache_delete( $post_id, 'post_meta' );

		return self::get( $post_id );
	}

	/**
	 * Count a front-end singular view of a supported type.
	 */
	public static function maybe_count(): void {
		if ( is_admin() || wp_doing_ajax() || is_preview() ) {
			return;
		}
		if ( ! is_singular( self::TYPES ) ) {
			return;
		}

		$post_id = get_queried_object_id();
		if ( $post_id > 0 ) {
			self::increment( $post_id );
		}
	}
}
