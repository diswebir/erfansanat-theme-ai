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
	public const COOKIE   = 'es_recent_views';
	public const WINDOW   = 1800; // seconds a repeat view is not counted again

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
	 * Bot / crawler detection (P07-T015 AC-02). Empty UA is treated as a bot.
	 * The signature list is filterable via `es_view_counter_bots`.
	 */
	public static function is_bot( string $ua ): bool {
		$ua = trim( $ua );
		if ( '' === $ua ) {
			return true;
		}
		$signatures = (array) apply_filters(
			'es_view_counter_bots',
			array( 'bot', 'crawl', 'spider', 'slurp', 'curl', 'wget', 'python-requests', 'httpclient', 'headless', 'phantom', 'preview', 'monitor', 'feedfetcher', 'facebookexternalhit', 'embeddedbrowser' )
		);
		$ua = strtolower( $ua );
		foreach ( $signatures as $signature ) {
			if ( '' !== $signature && false !== strpos( $ua, strtolower( (string) $signature ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Count a front-end singular view of a supported type (P07-T015).
	 * Skips admin/AJAX/preview, bots, protected posts, logged-in users who can
	 * edit the post, and repeat views by the same visitor inside the window.
	 */
	public static function maybe_count(): void {
		if ( is_admin() || wp_doing_ajax() || is_preview() ) {
			return;
		}
		if ( ! is_singular( self::TYPES ) ) {
			return;
		}

		$post_id = get_queried_object_id();
		if ( $post_id <= 0 || ! in_array( get_post_type( $post_id ), self::TYPES, true ) ) {
			return;
		}
		if ( post_password_required( $post_id ) ) {
			return;
		}
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		if ( self::is_bot( $ua ) ) {
			return;
		}
		if ( is_user_logged_in() && current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Repeat-view dedupe: bounded cookie list, refreshed on each count.
		$seen = array();
		if ( isset( $_COOKIE[ self::COOKIE ] ) ) {
			$decoded = json_decode( sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE ] ) ), true );
			if ( is_array( $decoded ) ) {
				$seen = array_values( array_filter( array_map( 'intval', $decoded ) ) );
			}
		}
		if ( in_array( $post_id, $seen, true ) ) {
			return;
		}

		self::increment( $post_id );

		$seen[] = $post_id;
		$seen   = array_slice( $seen, -50 );
		if ( ! headers_sent() ) {
			setcookie(
				self::COOKIE,
				(string) wp_json_encode( $seen ),
				array(
					'expires'  => time() + self::WINDOW,
					'path'     => COOKIEPATH ? COOKIEPATH : '/',
					'secure'   => is_ssl(),
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
		}
		$_COOKIE[ self::COOKIE ] = (string) wp_json_encode( $seen );
	}
}
