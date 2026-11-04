<?php
/** Product metadata box registration and nonce-guarded persistence. */

namespace ErfanSanat\Theme\Admin\Metaboxes;

use ErfanSanat\Theme\Admin\Metaboxes\Tabs\ProductIdentityTab;
use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Owns the product meta box save boundary shared by its tabs. */
final class ProductMeta implements ModuleInterface {

	private const NONCE_NAME = 'es_product_meta_nonce';
	private const INPUT_NAME = 'es_product_meta';

	/** Register the product edit box and its post-type-specific save callback. */
	public function register(): void {
		add_action( 'add_meta_boxes_product', array( self::class, 'register_box' ) );
		add_action( 'save_post_product', array( self::class, 'save' ), 10, 2 );
	}

	/** Register one native meta box on WooCommerce products. */
	public static function register_box(): void {
		add_meta_box(
			'es-product-information',
			__( 'اطلاعات محصولات', 'erfansanat-theme' ),
			array( self::class, 'render' ),
			'product',
			'normal',
			'high'
		);
	}

	/** Render only for users who may edit the current product. */
	public static function render( $post ): void {
		if ( ! is_object( $post ) || empty( $post->ID ) || ! current_user_can( 'edit_post', (int) $post->ID ) ) { return; }
		echo self::render_content( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- renderer escapes all field values.
	}

	/** Return nonce-protected identity markup for the meta-box callback and tests. */
	public static function render_content( $post ): string {
		if ( ! is_object( $post ) || empty( $post->ID ) ) { return ''; }
		$nonce = wp_nonce_field( 'es_product_meta_' . (int) $post->ID, self::NONCE_NAME, false, false );
		return (string) $nonce . ProductIdentityTab::render( (int) $post->ID );
	}

	/** Validate post state, capability and nonce before saving exact meta keys. */
	public static function save( int $post_id, $post = null ): bool {
		if ( $post_id < 1 || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( function_exists( 'wp_is_post_revision' ) && wp_is_post_revision( $post_id ) ) || ( function_exists( 'wp_is_post_autosave' ) && wp_is_post_autosave( $post_id ) ) ) {
			return false;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) || ! isset( $_POST[ self::NONCE_NAME ] ) || ! is_string( $_POST[ self::NONCE_NAME ] ) ) {
			return false;
		}
		$nonce = wp_unslash( $_POST[ self::NONCE_NAME ] );
		if ( ! is_string( $nonce ) || ! wp_verify_nonce( $nonce, 'es_product_meta_' . $post_id ) ) {
			return false;
		}
		if ( ! isset( $_POST[ self::INPUT_NAME ] ) || ! is_array( $_POST[ self::INPUT_NAME ] ) ) {
			return false;
		}
		$input = wp_unslash( $_POST[ self::INPUT_NAME ] );
		$keys  = array_keys( ProductIdentityTab::text_fields() );
		foreach ( $keys as $key ) {
			if ( ! array_key_exists( $key, $input ) || ! is_scalar( $input[ $key ] ) ) { continue; }
			update_post_meta( $post_id, $key, sanitize_text_field( (string) $input[ $key ] ) );
		}
		if ( isset( $input['true_sale'] ) && in_array( $input['true_sale'], array( 'true', 'false' ), true ) ) {
			update_post_meta( $post_id, 'true_sale', $input['true_sale'] );
		}
		return true;
	}
}
