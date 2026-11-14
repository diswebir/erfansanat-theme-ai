<?php
/**
 * Project legacy metaboxes (replaces JetEngine project fields).
 *
 * The gallery keeps the five historical image keys (including the `foure-image`
 * typo) in sync with a modern ordered gallery field. All keys keep their exact
 * historical spelling.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\LegacyMeta;

final class ProjectMetaboxes {

	/** Legacy gallery keys in order (P03-T020). `foure-image` spelling is sacred. */
	public const GALLERY_KEYS = array( 'second-image', 'three-image', 'foure-image', 'five-image', 'six-image' );

	/** Modern ordered gallery meta key (our own, synced with the legacy keys). */
	public const MODERN_KEY = '_es_project_gallery';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'add_meta_boxes', array( __CLASS__, 'register_metaboxes' ) );
		add_action( 'save_post_projects', array( __CLASS__, 'save' ), 10, 2 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	public static function register_metaboxes(): void {
		add_meta_box( 'es-project-gallery', __( 'گالری پروژه', 'erfansanat' ), array( __CLASS__, 'render_gallery' ), 'projects', 'normal', 'default' );
		add_meta_box( 'es-project-details', __( 'تاریخ تکمیل و محصولات استفاده‌شده', 'erfansanat' ), array( __CLASS__, 'render_details' ), 'projects', 'normal', 'default' );
	}

	/**
	 * Save dispatcher with the usual guards.
	 *
	 * @param int      $post_id Post id.
	 * @param \WP_Post $post    Post.
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

		self::save_gallery( $post_id );
		self::save_details( $post_id );
	}

	/**
	 * Read the project gallery: modern key first, else the legacy keys in order.
	 *
	 * @return array<int,int>
	 */
	public static function get_gallery( $post_id ): array {
		$modern = (string) get_post_meta( $post_id, self::MODERN_KEY, true );
		if ( '' !== trim( $modern ) ) {
			return self::parse_ids( $modern );
		}

		$ids = array();
		foreach ( self::GALLERY_KEYS as $key ) {
			$id = absint( get_post_meta( $post_id, $key, true ) );
			if ( $id > 0 ) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	/**
	 * @param mixed $value Comma list or array.
	 * @return array<int,int>
	 */
	private static function parse_ids( $value ): array {
		$parts = is_array( $value ) ? $value : explode( ',', (string) $value );
		$ids   = array();
		foreach ( $parts as $part ) {
			$id = absint( trim( (string) $part ) );
			if ( $id > 0 ) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	/**
	 * Render the gallery metabox.
	 *
	 * @param \WP_Post $post Post.
	 */
	public static function render_gallery( $post ): void {
		wp_nonce_field( 'es_project_gallery', 'es_project_gallery_nonce' );
		$gallery = implode( ',', self::get_gallery( $post->ID ) );
		printf(
			'<p class="es-field"><label for="es-project-gallery">%s</label><input type="text" id="es-project-gallery" name="es_project_gallery" value="%s" class="widefat" /></p>',
			esc_html__( 'گالری (شناسه تصاویر با کاما، به‌ترتیب)', 'erfansanat' ),
			esc_attr( $gallery )
		);
		echo '<p class="description">' . esc_html__( 'تصویر شاخص = تصویر اول؛ این گالری تصاویر دوم تا ششم را نگه می‌دارد.', 'erfansanat' ) . '</p>';
	}

	/**
	 * Save the gallery: store the modern key and map order onto the five legacy
	 * keys; unused slots are cleared so removal/reorder stays in sync.
	 *
	 * @param int $post_id Post id.
	 */
	public static function save_gallery( $post_id ): void {
		if ( ! isset( $_POST['es_project_gallery_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_project_gallery_nonce'] ) ), 'es_project_gallery' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_project_gallery'] ) ) {
			return;
		}

		$ids = array_slice( self::parse_ids( wp_unslash( $_POST['es_project_gallery'] ) ), 0, count( self::GALLERY_KEYS ) );

		update_post_meta( $post_id, self::MODERN_KEY, implode( ',', $ids ) );

		foreach ( self::GALLERY_KEYS as $i => $key ) {
			update_post_meta( $post_id, $key, isset( $ids[ $i ] ) ? $ids[ $i ] : '' );
		}
	}

	/**
	 * Read related product ids, validated against existing products (P03-T021).
	 *
	 * @return array<int,int>
	 */
	public static function get_related_products( $post_id ): array {
		$ids = array();
		foreach ( LegacyMeta::get( $post_id, 'mortabet_mahsole_2' ) as $id ) {
			$id = absint( $id );
			if ( $id > 0 && 'product' === get_post_type( $id ) ) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	/**
	 * Render the completion-date / related-products metabox.
	 *
	 * @param \WP_Post $post Post.
	 */
	public static function render_details( $post ): void {
		wp_nonce_field( 'es_project_details', 'es_project_details_nonce' );

		$date     = (string) get_post_meta( $post->ID, 'finish-pro-time', true );
		$products = implode( ',', self::get_related_products( $post->ID ) );

		printf( '<p class="es-field"><label for="es-finish-time">%s</label><input type="text" id="es-finish-time" name="es_project_details[finish-pro-time]" value="%s" class="regular-text" placeholder="1404/01/01" /></p>', esc_html__( 'تاریخ تکمیل پروژه (شمسی)', 'erfansanat' ), esc_attr( $date ) );
		printf( '<p class="es-field"><label for="es-related-products">%s</label><input type="text" id="es-related-products" name="es_project_details[mortabet_mahsole_2]" value="%s" class="widefat" /></p>', esc_html__( 'محصولات استفاده‌شده (شناسه‌ها با کاما)', 'erfansanat' ), esc_attr( $products ) );
	}

	/**
	 * Save the completion date (text) + related products (validated ids,
	 * format-preserving). Deleted products are dropped (AC-03).
	 *
	 * @param int $post_id Post id.
	 */
	public static function save_details( $post_id ): void {
		if ( ! isset( $_POST['es_project_details_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_project_details_nonce'] ) ), 'es_project_details' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_project_details'] ) || ! is_array( $_POST['es_project_details'] ) ) {
			return;
		}

		$raw = wp_unslash( $_POST['es_project_details'] );

		if ( array_key_exists( 'finish-pro-time', $raw ) ) {
			update_post_meta( $post_id, 'finish-pro-time', sanitize_text_field( (string) $raw['finish-pro-time'] ) );
		}

		if ( array_key_exists( 'mortabet_mahsole_2', $raw ) ) {
			$valid = array();
			foreach ( self::parse_ids( $raw['mortabet_mahsole_2'] ) as $id ) {
				if ( 'product' === get_post_type( $id ) ) {
					$valid[] = $id;
				}
			}
			LegacyMeta::set( $post_id, 'mortabet_mahsole_2', $valid );
		}
	}
}
