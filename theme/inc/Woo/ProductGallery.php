<?php
/** Synchronizes legacy and WooCommerce product galleries. */

namespace ErfanSanat\Theme\Woo;

use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Meta\RepeaterCodec;

defined( 'ABSPATH' ) || exit;

/** Keeps the original CSV metadata and WooCommerce's gallery key synchronized. */
final class ProductGallery implements ModuleInterface {

	/** @var bool Prevent recursive metadata hooks during synchronization. */
	private static bool $syncing = false;

	public function register(): void {
		add_action( 'added_post_meta', array( self::class, 'sync_meta' ), 20, 4 );
		add_action( 'updated_post_meta', array( self::class, 'sync_meta' ), 20, 4 );
		add_action( 'deleted_post_meta', array( self::class, 'sync_meta' ), 20, 4 );
	}

	/** Mirror either canonical gallery key to its partner for product posts. */
	public static function sync_meta( $meta_id, $post_id, $meta_key, $meta_value = null ): void {
		if ( self::$syncing || ! in_array( (string) $meta_key, array( 'galerry_produ', '_product_image_gallery' ), true ) || 'product' !== get_post_type( (int) $post_id ) ) { return; }
		self::$syncing = true;
		$ids = RepeaterCodec::decode_csv( $meta_value );
		$value = RepeaterCodec::encode_csv( $ids );
		$target = 'galerry_produ' === $meta_key ? '_product_image_gallery' : 'galerry_produ';
		if ( '' === $value ) { delete_post_meta( (int) $post_id, $target ); }
		else { update_post_meta( (int) $post_id, $target, $value ); }
		self::$syncing = false;
	}

	/** Read the gallery in legacy-aware priority order and return attachment IDs. */
	public static function get( int $post_id ): array {
		if ( $post_id < 1 ) { return array(); }
		foreach ( array( '_product_image_gallery', 'gallery-filed', 'galerry_produ' ) as $key ) {
			$value = get_post_meta( $post_id, $key, true );
			$ids = self::valid_gallery_ids( RepeaterCodec::decode_csv( $value ) );
			if ( $ids ) { return $ids; }
		}
		$legacy = array();
		foreach ( array( 'image-2', 'image-3', 'image-4', 'image-5', 'image-6' ) as $key ) {
			$value = get_post_meta( $post_id, $key, true );
		$id = absint( is_scalar( $value ) ? $value : 0 );
			if ( $id > 0 && ! in_array( $id, $legacy, true ) ) { $legacy[] = $id; }
		}
		return self::valid_gallery_ids( $legacy );
	}

	/** Keep only existing image attachments and remove duplicates. */
	private static function valid_gallery_ids( array $ids ): array {
		$valid = array();
		foreach ( $ids as $id ) {
			$id = absint( $id );
			if ( $id < 1 || in_array( $id, $valid, true ) ) { continue; }
			if ( function_exists( 'get_post_type' ) && 'attachment' !== get_post_type( $id ) ) { continue; }
			if ( function_exists( 'wp_attachment_is_image' ) && ! wp_attachment_is_image( $id ) ) { continue; }
			$valid[] = $id;
		}
		return $valid;
	}
}

if ( ! function_exists( 'es_product_gallery' ) ) {
	/** Public helper for product templates and integrations. */
	function es_product_gallery( $post_id = 0 ): array {
		$id = $post_id ? absint( $post_id ) : absint( get_the_ID() );
		return ProductGallery::get( $id );
	}
}
