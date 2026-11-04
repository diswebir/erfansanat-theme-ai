<?php
/** Cached access to legacy post and term metadata. */

namespace ErfanSanat\Theme\Meta;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Reads and writes existing metadata keys without renaming or migrating them. */
final class MetaRepository implements ModuleInterface {

	/** @var array<int,array<string,mixed>> */
	private static array $post_cache = array();

	/** @var array<int,array<string,mixed>> */
	private static array $term_cache = array();

	/** @var array<int,array<string,mixed>> */
	private static array $post_raw_cache = array();

	/** @var array<int,array<string,mixed>> */
	private static array $term_raw_cache = array();

	/** Keys whose scalar storage contract is comma-separated attachment IDs. */
	private const CSV_KEYS = array( 'galerry_produ', '_product_image_gallery', 'gallery-filed' );

	/** Register cache invalidation for writes made by WordPress or other plugins. */
	public function register(): void {
		add_action( 'added_post_meta', array( self::class, 'invalidate_post_meta' ), 10, 4 );
		add_action( 'updated_post_meta', array( self::class, 'invalidate_post_meta' ), 10, 4 );
		add_action( 'deleted_post_meta', array( self::class, 'invalidate_post_meta' ), 10, 4 );
		add_action( 'added_term_meta', array( self::class, 'invalidate_term_meta' ), 10, 4 );
		add_action( 'updated_term_meta', array( self::class, 'invalidate_term_meta' ), 10, 4 );
		add_action( 'deleted_term_meta', array( self::class, 'invalidate_term_meta' ), 10, 4 );
	}

	/** Invalidate one post key after a WordPress metadata mutation. */
	public static function invalidate_post_meta( $meta_id, $object_id, $meta_key = '', $meta_value = null ): void {
		unset( self::$post_cache[ (int) $object_id ][ (string) $meta_key ], self::$post_raw_cache[ (int) $object_id ][ (string) $meta_key ] );
	}

	/** Invalidate one term key after a WordPress metadata mutation. */
	public static function invalidate_term_meta( $meta_id, $object_id, $meta_key = '', $meta_value = null ): void {
		unset( self::$term_cache[ (int) $object_id ][ (string) $meta_key ], self::$term_raw_cache[ (int) $object_id ][ (string) $meta_key ] );
	}

	/** Retrieve one decoded post meta value; ten repeated reads cost one lookup. */
	public static function get( int $post_id, string $key, $fallback = null ) {
		if ( $post_id < 1 || '' === $key ) { return $fallback; }
		$raw = self::read_raw( $post_id, $key, 'post' );
		if ( null === $raw || '' === $raw ) { return $fallback; }
		if ( isset( self::$post_cache[ $post_id ] ) && array_key_exists( $key, self::$post_cache[ $post_id ] ) ) { return self::$post_cache[ $post_id ][ $key ]; }
		$value = self::decode_value( $key, $raw );
		self::$post_cache[ $post_id ][ $key ] = $value;
		return $value;
	}

	/** Retrieve decoded term metadata using the same cache and codec. */
	public static function get_term( int $term_id, string $key, $fallback = null ) {
		if ( $term_id < 1 || '' === $key ) { return $fallback; }
		$raw = self::read_raw( $term_id, $key, 'term' );
		if ( null === $raw || '' === $raw ) { return $fallback; }
		if ( isset( self::$term_cache[ $term_id ] ) && array_key_exists( $key, self::$term_cache[ $term_id ] ) ) { return self::$term_cache[ $term_id ][ $key ]; }
		$value = self::decode_value( $key, $raw );
		self::$term_cache[ $term_id ][ $key ] = $value;
		return $value;
	}

	/** Save a post value, preserving JSON/serialization/CSV format when possible. */
	public static function set( int $post_id, string $key, $value ): bool {
		if ( $post_id < 1 || '' === $key || ! function_exists( 'update_post_meta' ) ) { return false; }
		$existing = self::read_raw( $post_id, $key, 'post' );
		$stored   = is_array( $value ) && null !== $existing ? RepeaterCodec::preserve_format( $existing, $value ) : $value;
		$result   = update_post_meta( $post_id, $key, $stored );
		self::$post_raw_cache[ $post_id ][ $key ] = $stored;
		unset( self::$post_cache[ $post_id ][ $key ] );
		return false !== $result;
	}

	/** Save term metadata while preserving the existing representation. */
	public static function set_term( int $term_id, string $key, $value ): bool {
		if ( $term_id < 1 || '' === $key || ! function_exists( 'update_term_meta' ) ) { return false; }
		$existing = self::read_raw( $term_id, $key, 'term' );
		$stored   = is_array( $value ) && null !== $existing ? RepeaterCodec::preserve_format( $existing, $value ) : $value;
		$result   = update_term_meta( $term_id, $key, $stored );
		self::$term_raw_cache[ $term_id ][ $key ] = $stored;
		unset( self::$term_cache[ $term_id ][ $key ] );
		return false !== $result;
	}

	/** Remove a post meta key and invalidate only its request cache entry. */
	public static function delete( int $post_id, string $key ): bool {
		if ( ! function_exists( 'delete_post_meta' ) ) { return false; }
		$result = delete_post_meta( $post_id, $key );
		unset( self::$post_cache[ $post_id ][ $key ], self::$post_raw_cache[ $post_id ][ $key ] );
		return (bool) $result;
	}

	/** Remove a term meta key and invalidate only its request cache entry. */
	public static function delete_term( int $term_id, string $key ): bool {
		if ( ! function_exists( 'delete_term_meta' ) ) { return false; }
		$result = delete_term_meta( $term_id, $key );
		unset( self::$term_cache[ $term_id ][ $key ], self::$term_raw_cache[ $term_id ][ $key ] );
		return (bool) $result;
	}

	/** Clear all metadata caches, or only one object's caches. */
	public static function clear_cache( ?int $object_id = null ): void {
		if ( null === $object_id ) {
			self::$post_cache = self::$term_cache = self::$post_raw_cache = self::$term_raw_cache = array();
			return;
		}
		unset( self::$post_cache[ $object_id ], self::$term_cache[ $object_id ], self::$post_raw_cache[ $object_id ], self::$term_raw_cache[ $object_id ] );
	}

	/** Read and cache the raw scalar for one object/key pair. */
	private static function read_raw( int $object_id, string $key, string $type ) {
		$raw_cache = 'term' === $type ? self::$term_raw_cache : self::$post_raw_cache;
		if ( isset( $raw_cache[ $object_id ] ) && array_key_exists( $key, $raw_cache[ $object_id ] ) ) { return $raw_cache[ $object_id ][ $key ]; }
		if ( 'term' === $type ) {
			if ( ! function_exists( 'get_term_meta' ) ) { return null; }
			$value = get_term_meta( $object_id, $key, true );
		} else {
			if ( ! function_exists( 'get_post_meta' ) ) { return null; }
			$value = get_post_meta( $object_id, $key, true );
		}
		if ( '' === $value || null === $value ) { return null; }
		if ( 'term' === $type ) { self::$term_raw_cache[ $object_id ][ $key ] = $value; }
		else { self::$post_raw_cache[ $object_id ][ $key ] = $value; }
		return $value;
	}

	/** Decode only known structured formats; leave ordinary text untouched. */
	private static function decode_value( string $key, $raw ) {
		if ( in_array( $key, self::CSV_KEYS, true ) && is_string( $raw ) ) { return RepeaterCodec::decode_csv( $raw ); }
		$format = RepeaterCodec::format( $raw );
		return in_array( $format, array( 'json', 'serialized' ), true ) ? RepeaterCodec::decode( $raw ) : $raw;
	}
}
