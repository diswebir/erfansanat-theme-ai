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
		add_meta_box( 'es-product-titles', __( 'عناوین و وضعیت فروش', 'erfansanat' ), array( __CLASS__, 'render_titles' ), 'product', 'normal', 'default' );
		add_meta_box( 'es-product-files', __( 'کاتالوگ و گالری', 'erfansanat' ), array( __CLASS__, 'render_files' ), 'product', 'normal', 'default' );
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
		self::save_titles( $post_id );
		self::save_files( $post_id );
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

	/**
	 * Render the titles / sale-status metabox (P03-T014).
	 *
	 * @param \WP_Post $post Post.
	 */
	public static function render_titles( $post ): void {
		wp_nonce_field( 'es_product_titles', 'es_product_titles_nonce' );

		$zireonvan    = (string) get_post_meta( $post->ID, 'zireonvan', true );
		$english_titl = (string) get_post_meta( $post->ID, 'english_titl', true );
		$before_sale  = (string) get_post_meta( $post->ID, 'before-sale-c', true );
		$true_sale    = (string) get_post_meta( $post->ID, 'true_sale', true );

		printf( '<p class="es-field"><label for="es-zireonvan">%s</label><input type="text" id="es-zireonvan" name="es_titles[zireonvan]" value="%s" class="widefat" /></p>', esc_html__( 'زیرعنوان', 'erfansanat' ), esc_attr( $zireonvan ) );
		printf( '<p class="es-field"><label for="es-english-titl">%s</label><input type="text" id="es-english-titl" name="es_titles[english_titl]" value="%s" class="widefat" /></p>', esc_html__( 'عنوان انگلیسی', 'erfansanat' ), esc_attr( $english_titl ) );
		printf( '<p class="es-field"><label for="es-before-sale">%s</label><textarea id="es-before-sale" name="es_titles[before-sale-c]" class="widefat" rows="3">%s</textarea></p>', esc_html__( 'اطلاعیه قبل از خرید', 'erfansanat' ), esc_textarea( $before_sale ) );
		?>
		<p class="es-field">
			<label for="es-true-sale"><?php esc_html_e( 'وضعیت فروش', 'erfansanat' ); ?></label>
			<select id="es-true-sale" name="es_titles[true_sale]">
				<option value="true" <?php selected( $true_sale, 'true' ); ?>><?php esc_html_e( 'خرید آنلاین', 'erfansanat' ); ?></option>
				<option value="false" <?php selected( $true_sale, 'false' ); ?>><?php esc_html_e( 'استعلامی', 'erfansanat' ); ?></option>
			</select>
		</p>
		<?php
	}

	/**
	 * Save titles / sale status (nonce-guarded). true_sale accepts only the
	 * literal strings "true"/"false"; anything else keeps the existing value.
	 *
	 * @param int $post_id Post id.
	 */
	public static function save_titles( $post_id ): void {
		if ( ! isset( $_POST['es_product_titles_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_product_titles_nonce'] ) ), 'es_product_titles' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_titles'] ) || ! is_array( $_POST['es_titles'] ) ) {
			return;
		}

		$raw = wp_unslash( $_POST['es_titles'] );

		if ( array_key_exists( 'zireonvan', $raw ) ) {
			update_post_meta( $post_id, 'zireonvan', sanitize_text_field( (string) $raw['zireonvan'] ) );
		}
		if ( array_key_exists( 'english_titl', $raw ) ) {
			update_post_meta( $post_id, 'english_titl', sanitize_text_field( (string) $raw['english_titl'] ) );
		}
		if ( array_key_exists( 'before-sale-c', $raw ) ) {
			update_post_meta( $post_id, 'before-sale-c', sanitize_textarea_field( (string) $raw['before-sale-c'] ) );
		}
		if ( array_key_exists( 'true_sale', $raw ) ) {
			$value = (string) $raw['true_sale'];
			if ( 'true' === $value || 'false' === $value ) {
				update_post_meta( $post_id, 'true_sale', $value );
			}
			// AC-02: invalid values are rejected (existing value kept).
		}
	}

	/**
	 * Read the product gallery with the legacy fallback order (P03-T015):
	 * galerry_produ → _product_image_gallery → gallery-filed → image-2..6.
	 *
	 * @return array<int,int> Attachment ids in order.
	 */
	public static function get_gallery( $post_id ): array {
		foreach ( array( 'galerry_produ', '_product_image_gallery', 'gallery-filed' ) as $key ) {
			$value = (string) get_post_meta( $post_id, $key, true );
			if ( '' !== trim( $value ) ) {
				return self::parse_ids( $value );
			}
		}

		$ids = array();
		foreach ( array( 'image-2', 'image-3', 'image-4', 'image-5', 'image-6' ) as $key ) {
			$id = absint( get_post_meta( $post_id, $key, true ) );
			if ( $id > 0 ) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	/**
	 * Parse a comma list (or array) into positive integer ids, preserving order.
	 *
	 * @param mixed $value Raw value.
	 * @return array<int,int>
	 */
	public static function parse_ids( $value ): array {
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
	 * Render the catalog / gallery metabox.
	 *
	 * @param \WP_Post $post Post.
	 */
	public static function render_files( $post ): void {
		wp_nonce_field( 'es_product_files', 'es_product_files_nonce' );

		$catalog = (string) get_post_meta( $post->ID, 'product-catalog-file', true );
		$gallery = implode( ',', self::get_gallery( $post->ID ) );

		printf( '<p class="es-field"><label for="es-catalog">%s</label><input type="text" id="es-catalog" name="es_files[product-catalog-file]" value="%s" class="widefat" /></p>', esc_html__( 'فایل کاتالوگ (URL یا شناسه رسانه)', 'erfansanat' ), esc_attr( $catalog ) );
		printf( '<p class="es-field"><label for="es-gallery">%s</label><input type="text" id="es-gallery" name="es_files[galerry_produ]" value="%s" class="widefat" /></p>', esc_html__( 'گالری (شناسه تصاویر با کاما)', 'erfansanat' ), esc_attr( $gallery ) );
	}

	/**
	 * Save catalog (as URL) + gallery (comma ids), syncing the standard Woo
	 * gallery and any pre-existing legacy keys. Never creates new legacy keys.
	 *
	 * @param int $post_id Post id.
	 */
	public static function save_files( $post_id ): void {
		if ( ! isset( $_POST['es_product_files_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_product_files_nonce'] ) ), 'es_product_files' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_files'] ) || ! is_array( $_POST['es_files'] ) ) {
			return;
		}

		$raw = wp_unslash( $_POST['es_files'] );

		if ( array_key_exists( 'product-catalog-file', $raw ) ) {
			$value = trim( (string) $raw['product-catalog-file'] );
			if ( is_numeric( $value ) ) {
				$url = wp_get_attachment_url( absint( $value ) );
				$url = $url ? $url : '';
			} else {
				$url = esc_url_raw( $value );
			}
			update_post_meta( $post_id, 'product-catalog-file', $url ); // AC-01: stored as URL.
		}

		if ( array_key_exists( 'galerry_produ', $raw ) ) {
			$ids = self::parse_ids( $raw['galerry_produ'] );
			$csv = implode( ',', $ids );
			update_post_meta( $post_id, 'galerry_produ', $csv );          // AC-02: comma id list.
			update_post_meta( $post_id, '_product_image_gallery', $csv ); // AC-03: standard sync.
			self::sync_legacy_gallery( $post_id, $ids );
		}
	}

	/**
	 * Update pre-existing legacy gallery keys to match the new order. Keys that
	 * do not already exist are never created (preserves historical data shape).
	 *
	 * @param array<int,int> $ids Ordered attachment ids.
	 */
	private static function sync_legacy_gallery( $post_id, array $ids ): void {
		if ( metadata_exists( 'post', $post_id, 'gallery-filed' ) ) {
			update_post_meta( $post_id, 'gallery-filed', implode( ',', $ids ) );
		}

		$keys = array( 'image-2', 'image-3', 'image-4', 'image-5', 'image-6' );
		foreach ( $keys as $i => $key ) {
			if ( metadata_exists( 'post', $post_id, $key ) ) {
				update_post_meta( $post_id, $key, isset( $ids[ $i ] ) ? $ids[ $i ] : '' );
			}
		}
	}
}
