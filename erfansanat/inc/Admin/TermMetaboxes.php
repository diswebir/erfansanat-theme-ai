<?php
/**
 * Term meta: product category thumbnail and its FAQ repeater.
 *
 * @package ErfanSanat
 */

declare( strict_types=1 );

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Data\LegacyMeta;

defined( 'ABSPATH' ) || exit;

/**
 * Adds `thumbnail` and `faq` to `product_cat` (and the same fields to the
 * project taxonomies, which share the layout of the static category pages).
 */
final class TermMetaboxes {

	/**
	 * Nonce action.
	 */
	public const NONCE = 'erfansanat_term_meta';

	/**
	 * Taxonomies that receive the box.
	 *
	 * @return array<int, string>
	 */
	public static function taxonomies(): array {
		return array( 'product_cat', 'project_type', 'city' );
	}

	/**
	 * Attach hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		foreach ( self::taxonomies() as $taxonomy ) {
			add_action( $taxonomy . '_edit_form_fields', array( self::class, 'render_fields' ), 10, 2 );
			add_action( $taxonomy . '_add_form_fields', array( self::class, 'render_add_fields' ) );
			add_action( 'edited_' . $taxonomy, array( self::class, 'save' ) );
			add_action( 'created_' . $taxonomy, array( self::class, 'save' ) );
		}
		add_filter( 'manage_edit-product_cat_columns', array( self::class, 'add_thumbnail_column' ) );
		add_filter( 'manage_product_cat_custom_column', array( self::class, 'render_thumbnail_column' ), 10, 3 );
	}

	/**
	 * Fields shown when editing a term.
	 *
	 * @param \WP_Term $term Term.
	 * @param string   $taxonomy Taxonomy.
	 * @return void
	 */
	public static function render_fields( $term, string $taxonomy ): void {
		if ( ! $term instanceof \WP_Term ) {
			return;
		}
		$term_id = (int) $term->term_id;
		wp_nonce_field( self::NONCE, 'erfansanat_term_nonce' );
		$thumbnail = absint( get_term_meta( $term_id, LegacyMeta::term_key( 'thumbnail' ), true ) );
		$faq       = self::term_faq( $term_id );
		?>
		<tr class="form-field">
			<th scope="row"><label for="erfansanat-term-thumbnail"><?php esc_html_e( 'تصویر بندانگشتی', 'erfansanat' ); ?></label></th>
			<td>
				<span class="erfansanat-media">
					<input type="hidden" id="erfansanat-term-thumbnail" name="erfansanat_term[thumbnail]"
						value="<?php echo esc_attr( (string) $thumbnail ); ?>" data-media-input />
					<button type="button" class="button" data-media-select><?php esc_html_e( 'انتخاب تصویر', 'erfansanat' ); ?></button>
					<button type="button" class="button-link" data-media-clear><?php esc_html_e( 'حذف', 'erfansanat' ); ?></button>
				</span>
				<?php if ( $thumbnail > 0 ) : ?>
					<p><?php echo wp_get_attachment_image( $thumbnail, 'thumbnail' ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row"><?php esc_html_e( 'سوالات متداول', 'erfansanat' ); ?></th>
			<td>
				<fieldset data-erfansanat-term-faq>
					<table class="widefat striped" data-erfansanat-rows>
						<tbody>
							<?php foreach ( $faq as $index => $row ) : ?>
								<tr>
									<td><input type="text" class="widefat" name="erfansanat_term[faq][<?php echo (int) $index; ?>][name_question]"
										value="<?php echo esc_attr( $row['name_question'] ); ?>" placeholder="<?php esc_attr_e( 'سوال', 'erfansanat' ); ?>" /></td>
									<td><input type="text" class="widefat" name="erfansanat_term[faq][<?php echo (int) $index; ?>][answer_question]"
										value="<?php echo esc_attr( $row['answer_question'] ); ?>" placeholder="<?php esc_attr_e( 'پاسخ', 'erfansanat' ); ?>" /></td>
									<td><button type="button" class="button-link" data-erfansanat-remove aria-label="<?php esc_attr_e( 'حذف ردیف', 'erfansanat' ); ?>">×</button></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<p>
						<button type="button" class="button" data-erfansanat-add data-next-index="<?php echo (int) count( $faq ); ?>">
							<?php esc_html_e( 'افزودن سوال', 'erfansanat' ); ?>
						</button>
					</p>
				</fieldset>
				<?php unset( $taxonomy ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Fields shown when adding a term.
	 *
	 * @return void
	 */
	public static function render_add_fields(): void {
		wp_nonce_field( self::NONCE, 'erfansanat_term_nonce' );
		?>
		<div class="form-field">
			<label for="erfansanat-term-thumbnail"><?php esc_html_e( 'تصویر بندانگشتی (شناسه)', 'erfansanat' ); ?></label>
			<input type="number" min="0" id="erfansanat-term-thumbnail" name="erfansanat_term[thumbnail]" value="0" />
		</div>
		<?php
	}

	/**
	 * Save term meta.
	 *
	 * @param int $term_id Term id.
	 * @return void
	 */
	public static function save( $term_id ): void {
		$term_id = (int) $term_id;
		$nonce   = isset( $_POST['erfansanat_term_nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['erfansanat_term_nonce'] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			return;
		}
		// `manage_product_terms` is a WooCommerce capability, not a core one.
		if ( ! current_user_can( 'manage_product_terms' ) && ! current_user_can( 'manage_categories' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Registered by WooCommerce.
			return;
		}
		$payload = isset( $_POST['erfansanat_term'] ) ? (array) wp_unslash( $_POST['erfansanat_term'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitised below.
		self::persist( $term_id, $payload );
	}

	/**
	 * Write the sanitised term payload.
	 *
	 * @param int                  $term_id Term id.
	 * @param array<string, mixed> $payload Raw payload.
	 * @return void
	 */
	public static function persist( int $term_id, array $payload ): void {
		if ( array_key_exists( 'thumbnail', $payload ) ) {
			$id = absint( is_scalar( $payload['thumbnail'] ) ? $payload['thumbnail'] : 0 );
			if ( $id > 0 && 'attachment' === get_post_type( $id ) ) {
				update_term_meta( $term_id, LegacyMeta::term_key( 'thumbnail' ), $id );
			} else {
				delete_term_meta( $term_id, LegacyMeta::term_key( 'thumbnail' ) );
			}
		}

		if ( array_key_exists( 'faq', $payload ) ) {
			$rows = array();
			foreach ( (array) $payload['faq'] as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$raw_question = $row['name_question'] ?? '';
				$raw_answer   = $row['answer_question'] ?? '';
				$question     = sanitize_text_field( is_scalar( $raw_question ) ? (string) $raw_question : '' );
				$answer       = sanitize_textarea_field( is_scalar( $raw_answer ) ? (string) $raw_answer : '' );
				if ( '' === $question && '' === $answer ) {
					continue;
				}
				$rows[] = array(
					'name_question'   => $question,
					'answer_question' => $answer,
				);
			}
			if ( array() === $rows ) {
				delete_term_meta( $term_id, LegacyMeta::term_key( 'faq' ) );
			} else {
				update_term_meta( $term_id, LegacyMeta::term_key( 'faq' ), wp_slash( LegacyMeta::encode_repeater( $rows ) ) );
			}
		}
	}

	/**
	 * Read a term FAQ as clean rows.
	 *
	 * @param int $term_id Term id.
	 * @return array<int, array<string, string>>
	 */
	public static function term_faq( int $term_id ): array {
		$rows = array();
		foreach ( LegacyMeta::to_array( get_term_meta( $term_id, LegacyMeta::term_key( 'faq' ), true ) ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$raw_question = $row['name_question'] ?? '';
			$raw_answer   = $row['answer_question'] ?? '';
			$question     = is_scalar( $raw_question ) ? (string) $raw_question : '';
			$answer       = is_scalar( $raw_answer ) ? (string) $raw_answer : '';
			if ( '' === $question && '' === $answer ) {
				continue;
			}
			$rows[] = array(
				'name_question'   => $question,
				'answer_question' => $answer,
			);
		}
		return $rows;
	}

	/**
	 * Add the thumbnail column to the product category list.
	 *
	 * @param array<string, string> $columns Columns.
	 * @return array<string, string>
	 */
	public static function add_thumbnail_column( array $columns ): array {
		$columns['erfansanat_thumb'] = __( 'تصویر', 'erfansanat' );
		return $columns;
	}

	/**
	 * Render the thumbnail column.
	 *
	 * @param string $content Existing content.
	 * @param string $column  Column.
	 * @param int    $term_id Term id.
	 * @return string
	 */
	public static function render_thumbnail_column( string $content, string $column, int $term_id ): string {
		if ( 'erfansanat_thumb' !== $column ) {
			return $content;
		}
		$id = absint( get_term_meta( $term_id, LegacyMeta::term_key( 'thumbnail' ), true ) );
		return $id > 0 ? (string) wp_get_attachment_image( $id, array( 40, 40 ) ) : $content;
	}
}
