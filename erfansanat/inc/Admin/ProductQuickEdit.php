<?php
/**
 * Product Quick Edit + Bulk Edit for the industrial identifiers and sale
 * status. Only the allowlisted legacy keys are touched; an empty bulk value
 * never clears an existing value; saves are nonce + capability guarded.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

final class ProductQuickEdit {

	/** Keys editable via Quick/Bulk Edit (P03-T019). */
	public const ALLOWED = array( 'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id', 'true_sale' );

	private static bool $booted = false;
	private static bool $js_printed = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_filter( 'manage_product_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_product_posts_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( __CLASS__, 'quick_edit_fields' ), 10, 2 );
		add_action( 'bulk_edit_custom_box', array( __CLASS__, 'bulk_edit_fields' ), 10, 2 );
		add_action( 'save_post_product', array( __CLASS__, 'save' ), 10, 2 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * @param array<string,string> $columns Columns.
	 * @return array<string,string>
	 */
	public static function columns( $columns ) {
		$columns['es_true_sale'] = __( 'وضعیت فروش', 'erfansanat' );
		return $columns;
	}

	/**
	 * Render the sale-status column with data attributes for Quick Edit JS.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post id.
	 */
	public static function render_column( $column, $post_id ): void {
		if ( 'es_true_sale' !== $column ) {
			return;
		}

		$attrs = '';
		foreach ( self::ALLOWED as $key ) {
			$attrs .= ' data-' . esc_attr( $key ) . '="' . esc_attr( (string) get_post_meta( $post_id, $key, true ) ) . '"';
		}

		$status = (string) get_post_meta( $post_id, 'true_sale', true );
		$label  = ( 'true' === $status ) ? __( 'آنلاین', 'erfansanat' ) : __( 'استعلامی', 'erfansanat' );

		echo '<span class="es-qe-data" data-post-id="' . esc_attr( (string) $post_id ) . '"' . $attrs . '>' . esc_html( $label ) . '</span>';
	}

	/**
	 * Quick Edit fields (rendered once, on our column).
	 *
	 * @param string $column_name Column key.
	 * @param string $post_type   Post type.
	 */
	public static function quick_edit_fields( $column_name, $post_type ): void {
		if ( 'product' !== $post_type || 'es_true_sale' !== $column_name ) {
			return;
		}
		self::render_fields( false );
		self::print_js();
	}

	/**
	 * Bulk Edit fields.
	 *
	 * @param string $column_name Column key.
	 * @param string $post_type   Post type.
	 */
	public static function bulk_edit_fields( $column_name, $post_type ): void {
		if ( 'product' !== $post_type || 'es_true_sale' !== $column_name ) {
			return;
		}
		self::render_fields( true );
	}

	/**
	 * @param bool $bulk Whether this is the bulk editor (adds "no change").
	 */
	private static function render_fields( bool $bulk ): void {
		$labels = array(
			'gs1_id'        => __( 'GS1', 'erfansanat' ),
			'national_code' => __( 'کد ملی', 'erfansanat' ),
			'hs_id'         => __( 'HS', 'erfansanat' ),
			'isic_id'       => __( 'ISIC', 'erfansanat' ),
			'cpc_id'        => __( 'CPC', 'erfansanat' ),
			'product_id'    => __( 'شناسه محصول', 'erfansanat' ),
		);

		echo '<fieldset class="inline-edit-col-right es-qe"><div class="inline-edit-col">';
		echo '<legend class="inline-edit-legend">' . esc_html__( 'شناسه‌های صنعتی و فروش', 'erfansanat' ) . '</legend>';

		foreach ( $labels as $key => $label ) {
			printf(
				'<label class="inline-edit-group"><span class="title">%1$s</span><input type="text" name="es_qe[%2$s]" value="" /></label>',
				esc_html( $label ),
				esc_attr( $key )
			);
		}

		echo '<label class="inline-edit-group"><span class="title">' . esc_html__( 'وضعیت فروش', 'erfansanat' ) . '</span><select name="es_qe[true_sale]">';
		if ( $bulk ) {
			echo '<option value="">' . esc_html__( '— بدون تغییر —', 'erfansanat' ) . '</option>';
		}
		echo '<option value="true">' . esc_html__( 'خرید آنلاین', 'erfansanat' ) . '</option>';
		echo '<option value="false">' . esc_html__( 'استعلامی', 'erfansanat' ) . '</option>';
		echo '</select></label>';
		echo '</div></fieldset>';
	}

	/**
	 * Populate Quick Edit fields from the row's data attributes.
	 */
	private static function print_js(): void {
		if ( self::$js_printed ) {
			return;
		}
		self::$js_printed = true;
		?>
		<script>
		jQuery( function ( $ ) {
			if ( typeof inlineEditPost === 'undefined' ) { return; }
			var original = inlineEditPost.edit;
			inlineEditPost.edit = function ( id ) {
				original.apply( this, arguments );
				var postId = typeof id === 'object' ? parseInt( this.getId( id ), 10 ) : id;
				var $data = $( '#post-' + postId + ' .es-qe-data' );
				var $edit = $( '#edit-' + postId );
				if ( ! $data.length ) { return; }
				$.each( $data[0].dataset, function ( key, value ) {
					if ( key === 'postId' ) { return; }
					$edit.find( '[name="es_qe[' + key + ']"]' ).val( value );
				} );
			};
		} );
		</script>
		<?php
	}

	/**
	 * Save Quick/Bulk Edit values (allowlist, nonce + capability guarded).
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

		$is_bulk  = isset( $_POST['bulk_edit'] );
		$is_quick = isset( $_POST['action'] ) && 'inline-save' === $_POST['action'];
		if ( ! $is_bulk && ! $is_quick ) {
			return;
		}

		if ( $is_bulk ) {
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'bulk-posts' ) ) {
				return;
			}
		} elseif ( ! isset( $_POST['_inline_edit'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_inline_edit'] ) ), 'inlineeditnonce' ) ) {
			return;
		}

		if ( ! isset( $_POST['es_qe'] ) || ! is_array( $_POST['es_qe'] ) ) {
			return;
		}

		$raw = wp_unslash( $_POST['es_qe'] );
		foreach ( self::ALLOWED as $key ) {
			if ( ! array_key_exists( $key, $raw ) ) {
				continue; // AC-01: only allowlisted keys.
			}
			$value = (string) $raw[ $key ];

			// AC-02: an empty value never clears existing data (Quick or Bulk).
			if ( '' === $value ) {
				continue;
			}

			if ( 'true_sale' === $key ) {
				if ( 'true' === $value || 'false' === $value ) {
					update_post_meta( $post_id, 'true_sale', $value );
				}
				continue;
			}

			update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
		}
	}
}
