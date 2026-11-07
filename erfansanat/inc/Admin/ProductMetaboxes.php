<?php
/**
 * Product data meta box: the historical "اطلاعات محصولات" fields.
 *
 * Every key here exists on the live site. Writing a key with a different
 * spelling, or rewriting an existing value into another format, would lose
 * data during migration, so the meta box reads and writes the exact keys and
 * leaves untouched formats alone.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Data\LegacyMeta;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and persists the product information meta box.
 */
final class ProductMetaboxes {

	/**
	 * Meta box id.
	 */
	public const BOX = 'erfansanat-product-info';

	/**
	 * Nonce action.
	 */
	public const NONCE = 'erfansanat_product_meta';

	/**
	 * Text keys shown in the "identifiers" group.
	 *
	 * @return array<string, string>
	 */
	public static function identifier_fields(): array {
		return array(
			'gs1_id'        => __( 'شناسه GS1 (GTIN)', 'erfansanat' ),
			'national_code' => __( 'کد ملی کالا', 'erfansanat' ),
			'hs_id'         => __( 'شناسه HS', 'erfansanat' ),
			'isic_id'       => __( 'شناسه ISIC', 'erfansanat' ),
			'cpc_id'        => __( 'شناسه CPC', 'erfansanat' ),
			'product_id'    => __( 'شناسه محصول', 'erfansanat' ),
		);
	}

	/**
	 * Attach hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'add_meta_boxes_product', array( self::class, 'add_box' ) );
		add_action( 'save_post_product', array( self::class, 'save' ), 10, 2 );
		add_action( 'woocommerce_process_product_meta', array( self::class, 'save_from_woocommerce' ) );
		add_filter( 'woocommerce_product_data_tabs', array( self::class, 'add_product_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( self::class, 'render_before_sale_panel' ) );
		add_action( 'quick_edit_custom_box', array( self::class, 'quick_edit_fields' ), 10, 2 );
		add_action( 'save_post_product', array( self::class, 'save_quick_edit' ) );
		add_filter( 'manage_edit-product_columns', array( self::class, 'add_sale_column' ), 20 );
		add_action( 'manage_product_posts_custom_column', array( self::class, 'render_sale_column' ), 10, 2 );
	}

	/**
	 * Register the meta box under the WooCommerce product editor.
	 *
	 * @return void
	 */
	public static function add_box(): void {
		add_meta_box(
			self::BOX,
			__( 'اطلاعات محصول عرفان صنعت', 'erfansanat' ),
			array( self::class, 'render' ),
			'product',
			'normal',
			'high'
		);
	}

	/**
	 * Render the box.
	 *
	 * @param \WP_Post $post Product post.
	 * @return void
	 */
	public static function render( $post ): void {
		if ( ! $post instanceof \WP_Post ) {
			return;
		}
		$product_id = (int) $post->ID;
		wp_nonce_field( self::NONCE, 'erfansanat_product_nonce' );
		?>
		<div class="erfansanat-product-meta" dir="rtl">
			<p class="description">
				<?php esc_html_e( 'کلیدها عیناً با نام تاریخی ذخیره می‌شوند. مقدار خالی یعنی فیلد در دیتابیس وجود ندارد و نوشته نمی‌شود.', 'erfansanat' ); ?>
			</p>

			<h3><?php esc_html_e( 'شناسه‌های صنعتی و قانونی', 'erfansanat' ); ?></h3>
			<div class="erfansanat-product-meta__grid">
				<?php foreach ( self::identifier_fields() as $key => $label ) : ?>
					<p class="form-field">
						<label for="erfansanat-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
						<input type="text" id="erfansanat-<?php echo esc_attr( $key ); ?>"
							name="erfansanat_product[<?php echo esc_attr( $key ); ?>]"
							value="<?php echo esc_attr( LegacyMeta::text( $product_id, $key ) ); ?>"
							class="short" data-erfansanat-product-field="<?php echo esc_attr( $key ); ?>" />
					</p>
				<?php endforeach; ?>
			</div>

			<h3><?php esc_html_e( 'عنوان و وضعیت فروش', 'erfansanat' ); ?></h3>
			<p class="form-field">
				<label for="erfansanat-zireonvan"><?php esc_html_e( 'زیرعنوان محصول', 'erfansanat' ); ?></label>
				<input type="text" id="erfansanat-zireonvan" name="erfansanat_product[zireonvan]"
					value="<?php echo esc_attr( LegacyMeta::text( $product_id, 'zireonvan' ) ); ?>" class="large-text" />
			</p>
			<p class="form-field">
				<label for="erfansanat-english-titl"><?php esc_html_e( 'عنوان انگلیسی محصول', 'erfansanat' ); ?></label>
				<input type="text" id="erfansanat-english-titl" name="erfansanat_product[english_titl]"
					value="<?php echo esc_attr( LegacyMeta::text( $product_id, 'english_titl' ) ); ?>" class="large-text" dir="ltr" />
			</p>
			<p class="form-field">
				<label for="erfansanat-true-sale"><?php esc_html_e( 'وضعیت خرید آنلاین', 'erfansanat' ); ?></label>
				<select id="erfansanat-true-sale" name="erfansanat_product[true_sale]">
					<option value="true" <?php selected( LegacyMeta::is_direct_sale( $product_id ), true ); ?>><?php esc_html_e( 'قابل خرید آنلاین', 'erfansanat' ); ?></option>
					<option value="false" <?php selected( LegacyMeta::is_direct_sale( $product_id ), false ); ?>><?php esc_html_e( 'استعلامی / تماس با فروش', 'erfansanat' ); ?></option>
				</select>
			</p>

			<h3><?php esc_html_e( 'فایل و گالری', 'erfansanat' ); ?></h3>
			<?php
			$catalog = LegacyMeta::text( $product_id, 'product-catalog-file' );
			$gallery = LegacyMeta::get_gallery_string( $product_id );
			?>
			<p class="form-field">
				<label for="erfansanat-product-catalog-file"><?php esc_html_e( 'فایل کاتالوگ (نشانی)', 'erfansanat' ); ?></label>
				<span class="erfansanat-media">
					<input type="text" id="erfansanat-product-catalog-file" name="erfansanat_product[product-catalog-file]"
						value="<?php echo esc_attr( $catalog ); ?>" class="large-text" dir="ltr" data-media-input />
					<button type="button" class="button" data-media-select><?php esc_html_e( 'انتخاب فایل', 'erfansanat' ); ?></button>
				</span>
			</p>
			<p class="form-field">
				<label for="erfansanat-galerry-produ"><?php esc_html_e( 'گالری محصول (شناسه‌های تصویر با کاما)', 'erfansanat' ); ?></label>
				<input type="text" id="erfansanat-galerry-produ" name="erfansanat_product[galerry_produ]"
					value="<?php echo esc_attr( $gallery ); ?>" class="large-text" dir="ltr" />
				<span class="description">
					<?php
					printf(
						/* translators: %s: resolved image count. */
						esc_html__( 'در حال حاضر %s تصویر از همهٔ کلیدهای پشتیبانی‌شده شناسایی شد.', 'erfansanat' ),
						esc_html( (string) count( LegacyMeta::product_gallery( $product_id ) ) )
					);
					?>
				</span>
			</p>

			<h3><?php esc_html_e( 'روابط', 'erfansanat' ); ?></h3>
			<p class="form-field">
				<label for="erfansanat-project-ids"><?php esc_html_e( 'پروژه‌های مرتبط (شناسه، جداشده با کاما)', 'erfansanat' ); ?></label>
				<input type="text" id="erfansanat-project-ids" name="erfansanat_product[project-ids]"
					value="<?php echo esc_attr( implode( ',', LegacyMeta::read_ids( $product_id, 'project-ids' ) ) ); ?>"
					class="large-text" dir="ltr" />
			</p>
			<p class="form-field">
				<label for="erfansanat-usage-countr"><?php esc_html_e( 'کاربردها (شناسهٔ ترم نوع پروژه، جداشده با کاما)', 'erfansanat' ); ?></label>
				<input type="text" id="erfansanat-usage-countr" name="erfansanat_product[usage_countr]"
					value="<?php echo esc_attr( implode( ',', LegacyMeta::read_ids( $product_id, 'usage_countr' ) ) ); ?>"
					class="large-text" dir="ltr" />
			</p>

			<h3><?php esc_html_e( 'فیلدهای تکرارشونده', 'erfansanat' ); ?></h3>
			<?php
			foreach ( LegacyMeta::repeater_fields() as $key => $fields ) {
				self::render_repeater( $product_id, $key, $fields );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render one repeater table.
	 *
	 * @param int                $post_id Post id.
	 * @param string             $key     Repeater key.
	 * @param array<int, string> $fields  Sub-field names.
	 * @return void
	 */
	private static function render_repeater( int $post_id, string $key, array $fields ): void {
		$rows = LegacyMeta::read_repeater( $post_id, $key );
		?>
		<fieldset class="erfansanat-repeater-meta" data-erfansanat-repeater="<?php echo esc_attr( $key ); ?>">
			<legend><?php echo esc_html( self::repeater_label( $key ) ); ?></legend>
			<table class="widefat striped">
				<thead>
					<tr>
						<?php foreach ( $fields as $field ) : ?>
							<th><?php echo esc_html( self::field_label( $field ) ); ?></th>
						<?php endforeach; ?>
						<th><?php esc_html_e( 'حذف', 'erfansanat' ); ?></th>
					</tr>
				</thead>
				<tbody data-erfansanat-rows>
					<?php foreach ( $rows as $index => $row ) : ?>
						<tr>
							<?php foreach ( $fields as $field ) : ?>
								<td>
									<input type="text"
										name="erfansanat_product[<?php echo esc_attr( $key ); ?>][<?php echo (int) $index; ?>][<?php echo esc_attr( $field ); ?>]"
										value="<?php echo esc_attr( (string) ( $row[ $field ] ?? '' ) ); ?>" class="widefat" />
								</td>
							<?php endforeach; ?>
							<td><button type="button" class="button-link" data-erfansanat-remove aria-label="<?php esc_attr_e( 'حذف ردیف', 'erfansanat' ); ?>">×</button></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p>
				<button type="button" class="button" data-erfansanat-add
					data-next-index="<?php echo (int) count( $rows ); ?>"
					data-fields="<?php echo esc_attr( implode( ',', $fields ) ); ?>"
					data-key="<?php echo esc_attr( $key ); ?>">
					<?php esc_html_e( 'افزودن ردیف', 'erfansanat' ); ?>
				</button>
			</p>
		</fieldset>
		<?php
	}

	/**
	 * Human label for a repeater key.
	 *
	 * @param string $key Repeater key.
	 * @return string
	 */
	private static function repeater_label( string $key ): string {
		$labels = array(
			'product-feature'       => __( 'مشخصات محصول', 'erfansanat' ),
			'product-feature2_copy' => __( 'ویژگی‌های کلیدی', 'erfansanat' ),
			'usage_product'         => __( 'کاربردها و موارد مصرف', 'erfansanat' ),
			'faq'                   => __( 'سوالات متداول', 'erfansanat' ),
		);
		return $labels[ $key ] ?? $key;
	}

	/**
	 * Human label for a sub-field.
	 *
	 * @param string $field Sub-field name.
	 * @return string
	 */
	private static function field_label( string $field ): string {
		$labels = array(
			'add-new-feature'   => __( 'ویژگی / عنوان', 'erfansanat' ),
			'vaule_of_variable' => __( 'مقدار', 'erfansanat' ),
			'add-new-usage'     => __( 'کاربرد', 'erfansanat' ),
			'name_question'     => __( 'سوال', 'erfansanat' ),
			'answer_question'   => __( 'پاسخ', 'erfansanat' ),
		);
		return $labels[ $field ] ?? $field;
	}

	/**
	 * Persist the meta box.
	 *
	 * @param int      $post_id Post id.
	 * @param \WP_Post $post    Post object.
	 * @return void
	 */
	public static function save( int $post_id, $post = null ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		$nonce = isset( $_POST['erfansanat_product_nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['erfansanat_product_nonce'] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$payload = isset( $_POST['erfansanat_product'] ) ? (array) wp_unslash( $_POST['erfansanat_product'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitised per key below.
		self::persist( $post_id, $payload );
		unset( $post );
	}

	/**
	 * WooCommerce's own save hook, used when the classic product form posts.
	 *
	 * @param int $post_id Product id.
	 * @return void
	 */
	public static function save_from_woocommerce( $post_id ): void {
		self::save( (int) $post_id );
	}

	/**
	 * Write a sanitised payload to the exact historical keys.
	 *
	 * Public and pure so the acceptance suite can exercise it without a request.
	 *
	 * @param int                  $post_id Product id.
	 * @param array<string, mixed> $payload Raw payload.
	 * @return void
	 */
	public static function persist( int $post_id, array $payload ): void {
		foreach ( array_keys( self::identifier_fields() ) as $key ) {
			if ( ! array_key_exists( $key, $payload ) ) {
				continue;
			}
			self::write_text( $post_id, $key, $payload[ $key ] );
		}

		foreach ( array( 'zireonvan', 'english_titl', 'before-sale-c' ) as $key ) {
			if ( array_key_exists( $key, $payload ) ) {
				self::write_text( $post_id, $key, $payload[ $key ] );
			}
		}

		if ( array_key_exists( 'true_sale', $payload ) ) {
			$flag = is_scalar( $payload['true_sale'] ) ? strtolower( trim( (string) $payload['true_sale'] ) ) : '';
			update_post_meta( $post_id, 'true_sale', wp_slash( in_array( $flag, array( 'true', '1', 'yes', 'on' ), true ) ? 'true' : 'false' ) );
		}

		if ( array_key_exists( 'product-catalog-file', $payload ) ) {
			$url = esc_url_raw( is_scalar( $payload['product-catalog-file'] ) ? (string) $payload['product-catalog-file'] : '' );
			self::write_or_delete( $post_id, 'product-catalog-file', $url );
		}

		if ( array_key_exists( 'galerry_produ', $payload ) ) {
			$ids = self::parse_ids( $payload['galerry_produ'] );
			LegacyMeta::save_product_gallery( $post_id, $ids );
		}

		foreach ( LegacyMeta::relation_keys() as $key ) {
			if ( array_key_exists( $key, $payload ) ) {
				$ids = self::parse_ids( $payload[ $key ] );
				if ( array() === $ids ) {
					delete_post_meta( $post_id, $key );
				} else {
					update_post_meta( $post_id, $key, wp_slash( (string) wp_json_encode( $ids ) ) );
				}
			}
		}

		foreach ( array_keys( LegacyMeta::repeater_fields() ) as $key ) {
			if ( array_key_exists( $key, $payload ) ) {
				$rows = self::parse_rows( $payload[ $key ], LegacyMeta::repeater_fields()[ $key ] );
				$json = LegacyMeta::encode_repeater( $rows );
				self::write_or_delete( $post_id, $key, '[]' === $json || '{}' === $json ? '' : $json );
			}
		}
	}

	/**
	 * Write a text key, deleting it when the submitted value is empty.
	 *
	 * Deleting instead of storing an empty string keeps "absent" and "empty"
	 * distinguishable, which the migration audit relies on.
	 *
	 * @param int    $post_id Post id.
	 * @param string $key    Meta key.
	 * @param mixed  $value  Raw value.
	 * @return void
	 */
	private static function write_text( int $post_id, string $key, $value ): void {
		$clean = sanitize_text_field( is_scalar( $value ) ? (string) $value : '' );
		self::write_or_delete( $post_id, $key, $clean );
	}

	/**
	 * Write when non-empty, delete otherwise.
	 *
	 * @param int    $post_id Post id.
	 * @param string $key     Meta key.
	 * @param string $value   Clean value.
	 * @return void
	 */
	private static function write_or_delete( int $post_id, string $key, string $value ): void {
		if ( '' === trim( $value ) ) {
			delete_post_meta( $post_id, $key );
			return;
		}
		// update_metadata() unslashes; slashing keeps backslashes intact.
		update_post_meta( $post_id, $key, wp_slash( $value ) );
	}

	/**
	 * Parse a comma/JSON value into unique, positive ids.
	 *
	 * @param mixed $raw Raw value.
	 * @return array<int, int>
	 */
	private static function parse_ids( $raw ): array {
		if ( is_string( $raw ) && '' !== trim( $raw ) ) {
			if ( '{' === ltrim( $raw )[0] || '[' === ltrim( $raw )[0] ) {
				$raw = LegacyMeta::to_array( $raw );
			} else {
				$raw = explode( ',', $raw );
			}
		}
		$ids = array();
		foreach ( (array) $raw as $value ) {
			if ( is_array( $value ) ) {
				$value = reset( $value );
			}
			if ( ! is_scalar( $value ) || ! is_numeric( trim( (string) $value ) ) || (int) $value <= 0 ) {
				continue;
			}
			$id = (int) $value;
			if ( ! in_array( $id, $ids, true ) ) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	/**
	 * Parse repeater rows, keeping only declared sub-fields.
	 *
	 * @param mixed              $raw    Raw rows.
	 * @param array<int, string> $fields Declared sub-fields.
	 * @return array<int, array<string, string>>
	 */
	private static function parse_rows( $raw, array $fields ): array {
		$rows = array();
		foreach ( (array) $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$clean = array();
			$empty = true;
			foreach ( $fields as $field ) {
				$value           = sanitize_textarea_field( is_scalar( $row[ $field ] ?? '' ) ? (string) $row[ $field ] : '' );
				$clean[ $field ] = $value;
				if ( '' !== trim( $value ) ) {
					$empty = false;
				}
			}
			if ( ! $empty ) {
				$rows[] = $clean;
			}
		}
		return $rows;
	}

	/**
	 * Add a "before purchase" field to the WooCommerce product data panel.
	 *
	 * @param array<string, array<string, mixed>> $tabs Existing tabs.
	 * @return array<string, array<string, mixed>>
	 */
	public static function add_product_tab( array $tabs ): array {
		$tabs['erfansanat'] = array(
			'label'    => __( 'اطلاعات عرفان صنعت', 'erfansanat' ),
			'target'   => 'erfansanat_product_data',
			'class'    => array(),
			'priority' => 65,
		);
		return $tabs;
	}

	/**
	 * Render the panel that holds `before-sale-c`.
	 *
	 * @return void
	 */
	public static function render_before_sale_panel(): void {
		global $post;
		$post_id = $post instanceof \WP_Post ? (int) $post->ID : 0;
		?>
		<div id="erfansanat_product_data" class="panel woocommerce_options_panel" dir="rtl">
			<?php wp_nonce_field( self::NONCE, 'erfansanat_product_nonce' ); ?>
			<div class="options_group">
				<?php
				woocommerce_wp_textarea_input(
					array(
						'id'          => 'erfansanat-before-sale-c',
						'name'        => 'erfansanat_product[before-sale-c]',
						'label'       => __( 'اطلاعیه قبل از خرید', 'erfansanat' ),
						'value'       => $post_id > 0 ? LegacyMeta::text( $post_id, 'before-sale-c' ) : '',
						'description' => __( 'در صفحهٔ محصول، بالای دکمهٔ خرید نمایش داده می‌شود.', 'erfansanat' ),
						'desc_tip'    => true,
					)
				);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Quick Edit fields for the identifier keys.
	 *
	 * @param string $column  Column name.
	 * @param string $post_type Post type.
	 * @return void
	 */
	public static function quick_edit_fields( string $column, string $post_type ): void {
		if ( 'product' !== $post_type || 'name' !== $column ) {
			return;
		}
		wp_nonce_field( self::NONCE, 'erfansanat_quick_nonce' );
		?>
		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<span class="title"><?php esc_html_e( 'اطلاعات عرفان صنعت', 'erfansanat' ); ?></span>
				<?php foreach ( self::identifier_fields() as $key => $label ) : ?>
					<label>
						<span class="title"><?php echo esc_html( $label ); ?></span>
						<span class="input-text-wrap">
							<input type="text" name="erfansanat_quick[<?php echo esc_attr( $key ); ?>]" value="" class="text" data-erfansanat-quick="<?php echo esc_attr( $key ); ?>" />
						</span>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Save the Quick Edit payload with its own nonce and capability check.
	 *
	 * @param int $post_id Product id.
	 * @return void
	 */
	public static function save_quick_edit( int $post_id ): void {
		$nonce = isset( $_POST['erfansanat_quick_nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['erfansanat_quick_nonce'] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$payload = isset( $_POST['erfansanat_quick'] ) ? (array) wp_unslash( $_POST['erfansanat_quick'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitised per key below.
		foreach ( array_keys( self::identifier_fields() ) as $key ) {
			if ( ! array_key_exists( $key, $payload ) ) {
				continue;
			}
			$value = sanitize_text_field( is_scalar( $payload[ $key ] ) ? (string) $payload[ $key ] : '' );
			if ( '' === $value ) {
				continue;
			}
			update_post_meta( $post_id, $key, wp_slash( $value ) );
		}
	}

	/**
	 * Add a sale-mode column to the products list.
	 *
	 * @param array<string, string> $columns Columns.
	 * @return array<string, string>
	 */
	public static function add_sale_column( array $columns ): array {
		$columns['erfansanat_sale'] = __( 'نوع فروش', 'erfansanat' );
		return $columns;
	}

	/**
	 * Render the sale-mode column.
	 *
	 * @param string $column  Column.
	 * @param int    $post_id Product id.
	 * @return void
	 */
	public static function render_sale_column( string $column, int $post_id ): void {
		if ( 'erfansanat_sale' !== $column ) {
			return;
		}
		echo esc_html(
			LegacyMeta::is_direct_sale( $post_id )
				? __( 'خرید آنلاین', 'erfansanat' )
				: __( 'استعلامی', 'erfansanat' )
		);
	}
}
