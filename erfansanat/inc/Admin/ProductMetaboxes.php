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

use ErfanSanat\Theme\Core\LegacyMeta;
use WP_Term;

final class ProductMetaboxes {

	/** Industrial/legal identifiers (P03-T013) — text, leading zeros preserved. */
	public const IDS_KEYS = array( 'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id' );

	/** Feature repeaters (P03-T017): meta key => historical subkeys. */
	public const FEATURE_KEYS = array(
		'product-feature'       => array( 'add-new-feature' ),
		'product-feature2_copy' => array( 'add-new-feature', 'vaule_of_variable' ),
	);

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
		add_meta_box( 'es-product-relations', __( 'پروژه‌ها و کاربردهای مرتبط', 'erfansanat' ), array( __CLASS__, 'render_relations' ), 'product', 'normal', 'default' );
		add_meta_box( 'es-product-features', __( 'مشخصات فنی (ویژگی‌ها)', 'erfansanat' ), array( __CLASS__, 'render_features' ), 'product', 'normal', 'default' );
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
		self::save_relations( $post_id );
		self::save_features( $post_id );
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

	/**
	 * Read related project ids (validated against existing projects).
	 *
	 * @return array<int,int>
	 */
	public static function get_related_projects( $post_id ): array {
		$ids = array();
		foreach ( LegacyMeta::get( $post_id, 'project-ids' ) as $id ) {
			$id = absint( $id );
			if ( $id > 0 && 'projects' === get_post_type( $id ) ) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	/**
	 * Read related project-type term ids (validated against existing terms).
	 *
	 * @return array<int,int>
	 */
	public static function get_usage_terms( $post_id ): array {
		$ids = array();
		foreach ( LegacyMeta::get( $post_id, 'usage_countr' ) as $term_id ) {
			$term_id = absint( $term_id );
			$term    = $term_id > 0 ? get_term( $term_id, 'project_type' ) : null;
			if ( $term instanceof WP_Term ) {
				$ids[] = $term_id;
			}
		}
		return $ids;
	}

	/**
	 * Render the relations metabox (P03-T016).
	 *
	 * @param \WP_Post $post Post.
	 */
	public static function render_relations( $post ): void {
		wp_nonce_field( 'es_product_relations', 'es_product_relations_nonce' );

		$projects = implode( ',', self::get_related_projects( $post->ID ) );
		$terms    = implode( ',', self::get_usage_terms( $post->ID ) );

		printf( '<p class="es-field"><label for="es-project-ids">%s</label><input type="text" id="es-project-ids" name="es_relations[project-ids]" value="%s" class="widefat" /></p>', esc_html__( 'پروژه‌های مرتبط (شناسه‌ها با کاما)', 'erfansanat' ), esc_attr( $projects ) );
		printf( '<p class="es-field"><label for="es-usage-countr">%s</label><input type="text" id="es-usage-countr" name="es_relations[usage_countr]" value="%s" class="widefat" /></p>', esc_html__( 'کاربردها (شناسه انواع پروژه با کاما)', 'erfansanat' ), esc_attr( $terms ) );
	}

	/**
	 * Save relations: only valid project ids / project_type term ids are kept,
	 * and the previous storage format (PHP array vs JSON) is preserved.
	 *
	 * @param int $post_id Post id.
	 */
	public static function save_relations( $post_id ): void {
		if ( ! isset( $_POST['es_product_relations_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_product_relations_nonce'] ) ), 'es_product_relations' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_relations'] ) || ! is_array( $_POST['es_relations'] ) ) {
			return;
		}

		$raw = wp_unslash( $_POST['es_relations'] );

		if ( array_key_exists( 'project-ids', $raw ) ) {
			$valid = array();
			foreach ( self::parse_ids( $raw['project-ids'] ) as $id ) {
				if ( 'projects' === get_post_type( $id ) ) {
					$valid[] = $id; // AC-01: only real projects; deleted ids dropped.
				}
			}
			LegacyMeta::set( $post_id, 'project-ids', $valid );
		}

		if ( array_key_exists( 'usage_countr', $raw ) ) {
			$valid = array();
			foreach ( self::parse_ids( $raw['usage_countr'] ) as $term_id ) {
				$term = get_term( $term_id, 'project_type' );
				if ( $term instanceof WP_Term ) {
					$valid[] = $term_id; // AC-02: only real project_type terms.
				}
			}
			LegacyMeta::set( $post_id, 'usage_countr', $valid );
		}
	}

	/**
	 * Render the feature repeaters (P03-T017). Rows use the exact historical
	 * subkeys; a trailing empty row allows adding an item without JS.
	 *
	 * @param \WP_Post $post Post.
	 */
	public static function render_features( $post ): void {
		wp_nonce_field( 'es_product_features', 'es_product_features_nonce' );

		$labels = array(
			'product-feature'       => __( 'ویژگی‌های محصول', 'erfansanat' ),
			'product-feature2_copy' => __( 'جدول مشخصات (نام ویژگی / مقدار)', 'erfansanat' ),
		);

		foreach ( self::FEATURE_KEYS as $meta_key => $subkeys ) {
			echo '<div class="es-repeater"><h4>' . esc_html( $labels[ $meta_key ] ) . '</h4>';
			$items   = LegacyMeta::get( $post->ID, $meta_key );
			$items[] = array_fill_keys( $subkeys, '' ); // New empty row.

			foreach ( $items as $i => $item ) {
				echo '<p class="es-repeater-row">';
				foreach ( $subkeys as $subkey ) {
					printf(
						'<input type="text" name="es_features[%1$s][%2$d][%3$s]" value="%4$s" class="regular-text" placeholder="%3$s" /> ',
						esc_attr( $meta_key ),
						(int) $i,
						esc_attr( $subkey ),
						esc_attr( (string) ( is_array( $item ) ? ( $item[ $subkey ] ?? '' ) : '' ) )
					);
				}
				echo '</p>';
			}
			echo '</div>';
		}
	}

	/**
	 * Save the feature repeaters, preserving the item-0 storage format.
	 *
	 * @param int $post_id Post id.
	 */
	public static function save_features( $post_id ): void {
		if ( ! isset( $_POST['es_product_features_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_product_features_nonce'] ) ), 'es_product_features' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_features'] ) || ! is_array( $_POST['es_features'] ) ) {
			return;
		}

		$raw = wp_unslash( $_POST['es_features'] );
		foreach ( self::FEATURE_KEYS as $meta_key => $subkeys ) {
			if ( ! array_key_exists( $meta_key, $raw ) ) {
				continue;
			}
			LegacyMeta::set( $post_id, $meta_key, self::sanitize_repeater_rows( $raw[ $meta_key ], $subkeys ) );
		}
	}

	/**
	 * Sanitize repeater rows against the declared subkeys; fully empty rows
	 * are dropped, order is preserved.
	 *
	 * @param mixed           $value   Rows (array or JSON string).
	 * @param array<int,string> $subkeys Allowed subkeys.
	 * @return array<int,array<string,string>>
	 */
	private static function sanitize_repeater_rows( $value, array $subkeys ): array {
		$items = LegacyMeta::decode( $value );
		$clean = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$row = array();
			foreach ( $subkeys as $subkey ) {
				$row[ $subkey ] = sanitize_text_field( (string) ( $item[ $subkey ] ?? '' ) );
			}
			if ( '' !== trim( implode( '', $row ) ) ) {
				$clean[] = $row;
			}
		}

		return $clean;
	}
}
