<?php
/**
 * Section Manager: ordered, enable/disable and edit the home page sections.
 *
 * The manager is a tab inside the existing theme panel. It renders the saved
 * layout with native drag handles plus explicit move up/down buttons, so the
 * order can be changed with a mouse and with the keyboard only.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Sections;
use ErfanSanat\Theme\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Renders the section manager and sanitises its posted layout.
 */
final class SectionManager {

	/**
	 * Form field that carries the serialised layout.
	 */
	public const FIELD = 'home_sections';

	/**
	 * Nonce action used by the standalone page-layout meta box.
	 */
	public const META_NONCE = 'erfansanat_sections_meta';

	/**
	 * Post meta key holding a page override.
	 */
	public const META_KEY = '_erfansanat_sections';

	/**
	 * Render the manager panel.
	 *
	 * @return void
	 */
	public static function render_panel(): void {
		$layout   = Sections::home_layout();
		$registry = Sections::registry();
		?>
		<section id="erfansanat-panel-sections" class="erfansanat-admin__panel" data-panel="sections" role="tabpanel" hidden>
			<p class="description">
				<?php esc_html_e( 'ترتیب بخش‌های صفحه اصلی را با کشیدن هر ردیف یا با کلیدهای بالا/پایین تغییر دهید. بخش‌های خاموش در سایت رندر نمی‌شوند.', 'erfansanat' ); ?>
			</p>

			<div class="erfansanat-sections" data-sections>
				<input type="hidden" name="<?php echo esc_attr( self::FIELD ); ?>" value="<?php echo esc_attr( (string) wp_json_encode( $layout ) ); ?>" data-sections-input />
				<ol class="erfansanat-sections__list" data-sections-list>
					<?php foreach ( $layout as $position => $row ) : ?>
						<?php
						$key = (string) $row['key'];
						if ( ! isset( $registry[ $key ] ) ) {
							continue;
						}
						?>
						<li class="erfansanat-sections__item" draggable="true" data-key="<?php echo esc_attr( $key ); ?>" data-position="<?php echo (int) $position; ?>">
							<span class="erfansanat-sections__handle" aria-hidden="true" title="<?php esc_attr_e( 'جابه‌جایی با کشیدن', 'erfansanat' ); ?>">⋮⋮</span>
							<span class="erfansanat-sections__title"><?php echo esc_html( (string) $registry[ $key ]['label'] ); ?></span>
							<label class="erfansanat-sections__toggle">
								<input type="checkbox" data-section-enabled <?php checked( ! empty( $row['enabled'] ) ); ?> />
								<?php esc_html_e( 'نمایش', 'erfansanat' ); ?>
							</label>
							<span class="erfansanat-sections__anchor">
								<?php echo '' !== (string) $registry[ $key ]['anchor'] ? '#' . esc_html( (string) $registry[ $key ]['anchor'] ) : '—'; ?>
							</span>
							<span class="erfansanat-sections__move">
								<button type="button" class="button-link" data-move="up" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: section name. */ __( 'انتقال «%s» به بالا', 'erfansanat' ), (string) $registry[ $key ]['label'] ) ); ?>">▲</button>
								<button type="button" class="button-link" data-move="down" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: section name. */ __( 'انتقال «%s» به پایین', 'erfansanat' ), (string) $registry[ $key ]['label'] ) ); ?>">▼</button>
							</span>
							<button type="button" class="button-link erfansanat-sections__edit" data-section-edit aria-expanded="false">
								<?php esc_html_e( 'ویرایش محتوا', 'erfansanat' ); ?>
							</button>
							<div class="erfansanat-sections__fields" data-section-fields hidden>
								<?php self::render_fields( $key, $row['fields'] ?? array() ); ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>
		<?php
	}

	/**
	 * Render the editable fields of one section.
	 *
	 * @param string               $section_key Section key.
	 * @param array<string, mixed> $values      Saved field values.
	 * @return void
	 */
	private static function render_fields( string $section_key, array $values ): void {
		$registry = Sections::registry();
		if ( ! isset( $registry[ $section_key ] ) ) {
			return;
		}
		foreach ( $registry[ $section_key ]['fields'] as $name => $field ) {
			$value = $values[ $name ] ?? ( $field['default'] ?? '' );
			self::render_field( $section_key, $name, $field, $value );
		}
	}

	/**
	 * Render a single section field, reusing the option field vocabulary.
	 *
	 * @param string               $section_key Section key.
	 * @param string               $name        Field name.
	 * @param array<string, mixed> $field       Field definition.
	 * @param mixed                $value       Current value.
	 * @return void
	 */
	private static function render_field( string $section_key, string $name, array $field, $value ): void {
		$path = $section_key . '.' . $name;
		$id   = 'erfansanat-section-' . $section_key . '-' . $name;
		?>
		<div class="erfansanat-field erfansanat-field--<?php echo esc_attr( (string) $field['type'] ); ?>" data-section-field="<?php echo esc_attr( $name ); ?>">
			<label class="erfansanat-field__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( (string) $field['label'] ); ?></label>
			<?php
			switch ( (string) $field['type'] ) {
				case 'media':
					$preview = (int) $value > 0 ? wp_get_attachment_image_url( (int) $value, 'thumbnail' ) : '';
					?>
					<span class="erfansanat-media">
						<input type="hidden" id="<?php echo esc_attr( $id ); ?>" data-field="<?php echo esc_attr( $path ); ?>" value="<?php echo esc_attr( (string) (int) $value ); ?>" data-media-input />
						<img src="<?php echo esc_url( (string) $preview ); ?>" alt="" class="erfansanat-media__preview" data-media-preview <?php echo $preview ? '' : 'hidden'; ?> />
						<button type="button" class="button" data-media-select><?php esc_html_e( 'انتخاب تصویر', 'erfansanat' ); ?></button>
						<button type="button" class="button-link" data-media-clear><?php esc_html_e( 'حذف', 'erfansanat' ); ?></button>
					</span>
					<?php
					break;

				case 'icon':
					$current = (string) $value;
					?>
					<select id="<?php echo esc_attr( $id ); ?>" data-field="<?php echo esc_attr( $path ); ?>" data-type="icon">
						<?php foreach ( Sections::ICONS as $icon ) : ?>
							<option value="<?php echo esc_attr( $icon ); ?>" <?php selected( $current, $icon ); ?>><?php echo esc_html( $icon ); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="erfansanat-icon-preview">
						<?php echo Sections::icon_svg( in_array( $current, Sections::ICONS, true ) ? $current : 'lamp', 'h-6 w-6' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Whitelisted inline SVG. ?>
					</span>
					<?php
					break;

				case 'link':
					$row = is_array( $value ) ? $value : array();
					?>
					<span class="erfansanat-link-field">
						<input type="text" id="<?php echo esc_attr( $id ); ?>" data-field="<?php echo esc_attr( $path . '.title' ); ?>" value="<?php echo esc_attr( (string) ( $row['title'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'عنوان دکمه', 'erfansanat' ); ?>" />
						<input type="url" data-field="<?php echo esc_attr( $path . '.url' ); ?>" value="<?php echo esc_attr( (string) ( $row['url'] ?? '' ) ); ?>" placeholder="https://" dir="ltr" />
					</span>
					<?php
					break;

				case 'repeater':
					$rows = is_array( $value ) ? $value : array();
					?>
					<div class="erfansanat-repeater" data-section-repeater="<?php echo esc_attr( $path ); ?>">
						<div class="erfansanat-repeater__rows" data-repeater-rows>
							<?php foreach ( $rows as $index => $row ) : ?>
								<div class="erfansanat-repeater__row" data-row-index="<?php echo (int) $index; ?>">
									<?php foreach ( (array) ( $field['fields'] ?? array() ) as $sub_name => $sub_field ) : ?>
										<?php self::render_sub_field( $path, (int) $index, $sub_name, $sub_field, is_array( $row ) ? ( $row[ $sub_name ] ?? '' ) : '' ); ?>
									<?php endforeach; ?>
									<button type="button" class="button-link erfansanat-repeater__remove" data-repeater-remove aria-label="<?php esc_attr_e( 'حذف ردیف', 'erfansanat' ); ?>">×</button>
								</div>
							<?php endforeach; ?>
						</div>
						<template data-repeater-template>
							<div class="erfansanat-repeater__row" data-row-index="__INDEX__">
								<?php foreach ( (array) ( $field['fields'] ?? array() ) as $sub_name => $sub_field ) : ?>
									<?php self::render_sub_field( $path, '__INDEX__', $sub_name, $sub_field, $sub_field['default'] ?? '' ); ?>
								<?php endforeach; ?>
								<button type="button" class="button-link erfansanat-repeater__remove" data-repeater-remove aria-label="<?php esc_attr_e( 'حذف ردیف', 'erfansanat' ); ?>">×</button>
							</div>
						</template>
						<button type="button" class="button" data-repeater-add><?php esc_html_e( 'افزودن ردیف', 'erfansanat' ); ?></button>
					</div>
					<?php
					break;

				case 'url':
					?>
					<input type="url" id="<?php echo esc_attr( $id ); ?>" data-field="<?php echo esc_attr( $path ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" dir="ltr" />
					<?php
					break;

				default:
					?>
					<input type="text" id="<?php echo esc_attr( $id ); ?>" data-field="<?php echo esc_attr( $path ); ?>" value="<?php echo esc_attr( is_scalar( $value ) ? (string) $value : '' ); ?>" class="regular-text" />
					<?php
					break;
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render one repeater sub-field.
	 *
	 * @param string               $path       Parent field path.
	 * @param int|string           $index      Row index, or the `__INDEX__` placeholder.
	 * @param string               $sub_name   Sub-field name.
	 * @param array<string, mixed> $sub_field  Sub-field definition.
	 * @param mixed                $value      Current value.
	 * @return void
	 */
	private static function render_sub_field( string $path, $index, string $sub_name, array $sub_field, $value ): void {
		$field_path = $path . '.' . $index . '.' . $sub_name;
		$type       = (string) $sub_field['type'];
		$label      = (string) ( $sub_field['label'] ?? $sub_name );
		?>
		<span class="erfansanat-repeater__field">
			<?php if ( 'icon' === $type ) : ?>
				<select aria-label="<?php echo esc_attr( $label ); ?>" data-field="<?php echo esc_attr( $field_path ); ?>" data-type="icon">
					<?php foreach ( Sections::ICONS as $icon ) : ?>
						<option value="<?php echo esc_attr( $icon ); ?>" <?php selected( (string) $value, $icon ); ?>><?php echo esc_html( $icon ); ?></option>
					<?php endforeach; ?>
				</select>
			<?php elseif ( 'url' === $type ) : ?>
				<input type="url" aria-label="<?php echo esc_attr( $label ); ?>" data-field="<?php echo esc_attr( $field_path ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" placeholder="https://" dir="ltr" />
			<?php else : ?>
				<input type="text" aria-label="<?php echo esc_attr( $label ); ?>" data-field="<?php echo esc_attr( $field_path ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" placeholder="<?php echo esc_attr( $label ); ?>" />
			<?php endif; ?>
		</span>
		<?php
	}

	/**
	 * Register the page override meta box.
	 *
	 * @return void
	 */
	public static function register_meta_box(): void {
		add_meta_box(
			'erfansanat-sections-meta',
			__( 'ترتیب بخش‌های صفحه', 'erfansanat' ),
			array( self::class, 'render_meta_box' ),
			array( 'page' ),
			'side',
			'default'
		);
	}

	/**
	 * Render the page override meta box: which sections this page shows and in what order.
	 *
	 * @param \WP_Post $post Post being edited.
	 * @return void
	 */
	public static function render_meta_box( $post ): void {
		if ( ! $post instanceof \WP_Post ) {
			return;
		}
		wp_nonce_field( self::META_NONCE, 'erfansanat_sections_nonce' );
		$layout  = Sections::home_layout();
		$saved   = get_post_meta( $post->ID, self::META_KEY, true );
		$enabled = is_array( $saved ) && array() !== $saved ? array_map( 'strval', $saved ) : array();
		$inherit = array() === $enabled;
		?>
		<p class="description">
			<?php esc_html_e( 'اگر چیزی انتخاب نشود، ترتیب صفحه اصلی اعمال می‌شود.', 'erfansanat' ); ?>
		</p>
		<ol class="erfansanat-sections__list" data-section-meta>
			<?php
			$ordered = array();
			foreach ( $layout as $row ) {
				$ordered[] = (string) $row['key'];
			}
			foreach ( $enabled as $key ) {
				if ( in_array( $key, $ordered, true ) ) {
					$ordered = array_values( array_diff( $ordered, array( $key ) ) );
					array_unshift( $ordered, $key );
				}
			}
			$registry = Sections::registry();
			foreach ( $ordered as $key ) {
				if ( ! isset( $registry[ $key ] ) ) {
					continue;
				}
				?>
				<li>
					<label>
						<input type="checkbox" name="erfansanat_page_sections[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $inherit || in_array( $key, $enabled, true ) ); ?> />
						<?php echo esc_html( (string) $registry[ $key ]['label'] ); ?>
					</label>
				</li>
				<?php
			}
			?>
		</ol>
		<?php
	}

	/**
	 * Persist the page override.
	 *
	 * @param int $post_id Post id.
	 * @return void
	 */
	public static function save_meta_box( int $post_id ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$nonce = isset( $_POST['erfansanat_sections_nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['erfansanat_sections_nonce'] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::META_NONCE ) ) {
			return;
		}

		$registry = Sections::registry();
		$posted   = isset( $_POST['erfansanat_page_sections'] ) ? (array) wp_unslash( $_POST['erfansanat_page_sections'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Keys are sanitised in the loop.
		$keys     = array();
		foreach ( $posted as $key ) {
			$key = is_string( $key ) ? sanitize_key( $key ) : '';
			if ( '' !== $key && isset( $registry[ $key ] ) && ! in_array( $key, $keys, true ) ) {
				$keys[] = $key;
			}
		}

		if ( count( $keys ) === count( $registry ) ) {
			delete_post_meta( $post_id, self::META_KEY );
			return;
		}
		update_post_meta( $post_id, self::META_KEY, $keys );
	}

	/**
	 * Sanitise a serialised layout coming from the panel.
	 *
	 * Kept here so Settings stays free of request handling.
	 *
	 * @param mixed $raw Raw JSON or array.
	 * @return array<int, array<string, mixed>>
	 */
	public static function sanitize_layout( $raw ): array {
		return Sections::sanitize_layout( $raw );
	}

	/**
	 * Register the meta box hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'add_meta_boxes', array( self::class, 'register_meta_box' ) );
		add_action( 'save_post_page', array( self::class, 'save_meta_box' ) );
	}
}
