<?php
/** Deferred, bot-aware legacy view counter. */

namespace ErfanSanat\Theme\Meta;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Counts human visits once per browser session on the exact new key. */
final class Views implements ModuleInterface {

	private const POST_TYPES = array( 'post', 'projects', 'product' );
	private static array $pending = array();
	private static array $seen = array();

	public function register(): void { add_action( 'wp', array( self::class, 'track_current_view' ), 10 ); add_action( 'shutdown', array( self::class, 'flush_queue' ), 20 ); }

	/** Identify the current singular object and defer its write until shutdown. */
	public static function track_current_view(): void {
		if ( ! function_exists( 'is_singular' ) || ! is_singular( self::POST_TYPES ) || self::is_bot( (string) ( $_SERVER['HTTP_USER_AGENT'] ?? '' ) ) ) { return; }
		$id = function_exists( 'get_queried_object_id' ) ? absint( get_queried_object_id() ) : 0;
		if ( $id && in_array( get_post_type( $id ), self::POST_TYPES, true ) ) { self::queue_view( $id ); }
	}

	/** Queue one view, setting a first-party session cookie without visitor IDs. */
	public static function queue_view( int $post_id ): bool {
		if ( $post_id < 1 || ! in_array( get_post_type( $post_id ), self::POST_TYPES, true ) || isset( self::$seen[$post_id] ) ) { return false; }
		$cookie = 'es_viewed_' . $post_id;
		if ( isset( $_COOKIE[$cookie] ) ) { return false; }
		self::$seen[$post_id] = true;
		self::$pending[$post_id] = true;
		$_COOKIE[$cookie] = '1';
		if ( ! headers_sent() ) { setcookie( $cookie, '1', array( 'expires' => 0, 'path' => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/', 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax' ) ); }
		if ( ! defined( 'DONOTCACHEPAGE' ) ) { define( 'DONOTCACHEPAGE', true ); }
		if ( function_exists( 'do_action' ) ) { do_action( 'litespeed_control_set_nocache', 'ErfanSanat view counter' ); }
		return true;
	}

	/** Add queued counts via WordPress metadata APIs outside the response path. */
	public static function flush_queue(): void {
		foreach ( array_keys( self::$pending ) as $post_id ) {
			$current = get_post_meta( $post_id, 'number2-viewc', true );
			if ( ! is_scalar( $current ) || '' === (string) $current ) { $current = get_post_meta( $post_id, 'number-viewc', true ); }
			$count = max( 0, (int) $current ) + 1;
			update_post_meta( $post_id, 'number2-viewc', $count );
		}
		self::$pending = array();
	}

	/** Expose the display count with a fallback to the historical key. */
	public static function get_views( int $post_id ): int {
		if ( $post_id < 1 ) { return 0; }
		$value = get_post_meta( $post_id, 'number2-viewc', true );
		if ( ! is_scalar( $value ) || '' === (string) $value ) { $value = get_post_meta( $post_id, 'number-viewc', true ); }
		return max( 0, (int) $value );
	}

	/** Conservative common crawler and preview-bot detection. */
	public static function is_bot( string $agent ): bool {
		if ( '' === trim( $agent ) ) { return true; }
		return (bool) preg_match( '/bot|crawler|spider|slurp|curl|wget|python-requests|facebookexternalhit|twitterbot|linkedinbot|pingdom|uptime|headless|lighthouse|pagespeed/i', $agent );
	}
}

if ( ! function_exists( 'es_get_views' ) ) {
	/** Return the current view count, preferring the new metadata key. */
	function es_get_views( $post_id ): int { return Views::get_views( absint( $post_id ) ); }
}
