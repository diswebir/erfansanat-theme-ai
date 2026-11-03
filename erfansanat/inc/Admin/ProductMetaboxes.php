<?php
/**
 * Product legacy metaboxes (replaces JetEngine/Elementor product fields).
 *
 * Every meta key keeps its exact historical spelling. Values are sanitized by
 * type and saved with nonce + capability checks. Industrial identifiers are
 * stored as text so leading zeros survive.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

final class ProductMetaboxes {

	/** Industrial/legal identifiers (P03-T013) — text, leading zeros preserved. */
	public const IDS_KEYS = array( 'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id' );

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'add_meta_boxes', array( __CLASS__, 'register_metaboxes' ) );
		add_action( 'save_post_product', array( __CLASS__, 'save' ), 10, 2 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Register the product metaboxes.
	 */
	public static function register_metaboxes(): void {
		add_meta_box( 'es-product-ids', __( 'شناسه‌های صنعتی و قانونی', 'erfansanat' ), array( __CLASS__, 'render_ids' ), 'product', 'normal', 'default' );
	}

	/**
	 * Save dispatcher: autosave/revision/capability guards, then each group.
	 *
	 * @param int      $post_id Post id.
	 * @param \WP_Post $post    Post object.
	 */
	public static function save( $post_id, $post ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		self::save_ids( $post_id );
	}

	/**
	 * Industrial identifiers labels.
	 *
	 * @return array<string,string>
	 */
	private static function ids_labels(): array {
		return array(
			'gs1_id'        => __( 'شناسه GS1 (GTIN)', 'erfansanat' ),
			'national_code' => __( 'کد ملی', 'erfansanat' ),
			'hs_id'         => __( 'شناسه HS', 'erfansanat' ),
			'isic_id'       => __( 'شناسه ISIC', 'erfansanat' ),
			'cpc_id'        => __( 'شناسه CPC', 'erfansanat' ),
			'product_id'    => __( 'شناسه محصول', 'erfansanat' ),
		);
	}

	/**
	 * Render the industrial identifiers metabox.
	 *
	 * @param \WP_Post $post Post.
	 */
	public static function render_ids( $post ): void {
		wp_nonce_field( 'es_product_ids', 'es_product_ids_nonce' );
		$labels = self::ids_labels();

		foreach ( self::IDS_KEYS as $key ) {
			$value = (string) get_post_meta( $post->ID, $key, true );
			printf(
				'<p class="es-field"><label for="es-ids-%1$s">%2$s</label><input type="text" id="es-ids-%1$s" name="es_ids[%1$s]" value="%3$s" class="widefat" /></p>',
				esc_attr( $key ),
				esc_html( $labels[ $key ] ),
				esc_attr( $value )
			);
		}
	}

	/**
	 * Save the industrial identifiers (nonce-guarded, text-preserving).
	 *
	 * @param int $post_id Post id.
	 */
	public static function save_ids( $post_id ): void {
		if ( ! isset( $_POST['es_product_ids_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_product_ids_nonce'] ) ), 'es_product_ids' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_ids'] ) || ! is_array( $_POST['es_ids'] ) ) {
			return;
		}

		$raw = wp_unslash( $_POST['es_ids'] );
		foreach ( self::IDS_KEYS as $key ) {
			if ( array_key_exists( $key, $raw ) ) {
				// AC-02: keep as text (never cast to int) so leading zeros survive.
				update_post_meta( $post_id, $key, sanitize_text_field( (string) $raw[ $key ] ) );
			}
		}
	}
}
