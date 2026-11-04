<?php
/**
 * Bespoke RTL settings panel and its AJAX save endpoint.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the "Erfan Sanat" admin page and saves the option over AJAX.
 *
 * The screen is a single RTL page with a tab rail. Every request is guarded by
 * `manage_options` and a nonce; the save endpoint re-checks both, then hands the
 * payload to Settings::sanitize() before writing the single option row.
 */
final class ThemeOptions {

	/**
	 * Admin page slug.
	 */
	public const SLUG = 'erfansanat-options';

	/**
	 * AJAX action name.
	 */
	public const AJAX_ACTION = 'erfansanat_save_options';

	/**
	 * Nonce action name.
	 */
	public const NONCE_ACTION = 'erfansanat_options_nonce';

	/**
	 * Capability required for the screen and the endpoint.
	 */
	public const CAPABILITY = 'manage_options';

	/**
	 * Optional JSON sender used by the acceptance suite.
	 *
	 * When null, the standard wp_send_json_* helpers end the request as usual.
	 *
	 * @var callable|null
	 */
	private static $sender = null;

	/**
	 * Attach hooks.
	 *
	 * @return void
	 */
	/**
	 * Replace the JSON sender (tests only).
	 *
	 * @param callable|null $sender Receives (array $payload, int $status, bool $success).
	 * @return void
	 */
	public static function set_sender( ?callable $sender ): void {
		self::$sender = $sender;
	}

	/**
	 * Send a JSON success or error envelope.
	 *
	 * @param array<string, mixed> $payload Payload for the `data` member.
	 * @param int                  $status  HTTP status for the error case.
	 * @param bool                 $success Whether the request succeeded.
	 * @return void
	 */
	private static function respond( array $payload, int $status = 200, bool $success = true ): void {
		if ( null !== self::$sender ) {
			call_user_func( self::$sender, $payload, $status, $success );
			return;
		}
		if ( $success ) {
			wp_send_json_success( $payload, $status );
		}
		wp_send_json_error( $payload, $status );
	}

	/**
	 * Register the screen, assets and AJAX endpoint.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'admin_menu', array( self::class, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_assets' ) );
	}

	/**
	 * Register the AJAX endpoint.
	 *
	 * `wp_ajax_*` handlers are only dispatched for authenticated requests, so the
	 * hook is added on admin_init and can also be called directly by the tests.
	 *
	 * @return void
	 */
	public static function register_ajax(): void {
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( self::class, 'handle_save' ) );
	}

	/**
	 * Register the top-level menu entry.
	 *
	 * @return void
	 */
	public static function add_menu(): void {
		add_menu_page(
			__( 'داشبورد عرفان صنعت', 'erfansanat' ),
			__( 'عرفان صنعت', 'erfansanat' ),
			self::CAPABILITY,
			self::SLUG,
			array( self::class, 'render' ),
			'dashicons-art',
			59
		);
	}

	/**
	 * Whether the current request is the theme panel.
	 *
	 * @param string $hook Current admin page hook.
	 * @return bool
	 */
	public static function is_panel( string $hook ): bool {
		return false !== strpos( $hook, self::SLUG );
	}

	/**
	 * Load the panel styles and script only on the panel screen.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public static function enqueue_assets( string $hook ): void {
		if ( ! self::is_panel( $hook ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style(
			'erfansanat-admin',
			\ErfanSanat\Theme\AssetManager::uri( 'admin/options.css' ),
			array( 'wp-components' ),
			\ErfanSanat\Theme\AssetManager::version( 'admin/options.css' )
		);
		wp_enqueue_script(
			'erfansanat-admin',
			\ErfanSanat\Theme\AssetManager::uri( 'admin/options.js' ),
			array( 'jquery', 'wp-util' ),
			\ErfanSanat\Theme\AssetManager::version( 'admin/options.js' ),
			true
		);
		wp_localize_script(
			'erfansanat-admin',
			'erfansanatAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
				'action'  => self::AJAX_ACTION,
				'strings' => array(
					'saving'        => __( 'در حال ذخیره…', 'erfansanat' ),
					'saved'         => __( 'ذخیره شد.', 'erfansanat' ),
					'error'         => __( 'ذخیره نشد. دوباره تلاش کنید.', 'erfansanat' ),
					'confirmRemove' => __( 'این ردیف حذف شود؟', 'erfansanat' ),
					'chooseImage'   => __( 'انتخاب تصویر', 'erfansanat' ),
					'useImage'      => __( 'استفاده از این تصویر', 'erfansanat' ),
				),
			)
		);
	}

	/**
	 * Render the settings screen.
	 *
	 * @return void
	 */
	public static function render(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'دسترسی به این صفحه مجاز نیست.', 'erfansanat' ), 403 );
		}

		$tabs   = Settings::tabs();
		$schema = Settings::schema();
		$values = Settings::all();
		$active = 'brand';
		?>
		<div class="wrap erfansanat-admin" dir="rtl">
			<h1><?php esc_html_e( 'داشبورد عرفان صنعت', 'erfansanat' ); ?></h1>
			<p class="description">
				<?php esc_html_e( 'تنظیمات سراسری قالب در یک ردیف ذخیره می‌شوند. محتوای صفحات با سازندهٔ بخش‌ها در هر برگه ویرایش می‌شود.', 'erfansanat' ); ?>
			</p>

			<div class="erfansanat-admin__status" role="status" aria-live="polite" hidden></div>

			<div class="erfansanat-admin__layout">
				<nav class="erfansanat-admin__tabs" aria-label="<?php esc_attr_e( 'بخش‌های تنظیمات', 'erfansanat' ); ?>">
					<?php foreach ( $tabs as $slug => $label ) : ?>
						<button type="button"
							class="erfansanat-admin__tab<?php echo $slug === $active ? ' is-active' : ''; ?>"
							data-tab="<?php echo esc_attr( $slug ); ?>"
							aria-controls="erfansanat-panel-<?php echo esc_attr( $slug ); ?>"
							aria-selected="<?php echo $slug === $active ? 'true' : 'false'; ?>"
							role="tab">
							<?php echo esc_html( $label ); ?>
						</button>
					<?php endforeach; ?>
				</nav>

				<form id="erfansanat-options-form" class="erfansanat-admin__form" novalidate>
					<?php foreach ( $schema as $tab => $fields ) : ?>
						<?php
						if ( 'sections' === $tab ) {
							// The layout field is rendered by the section manager so it
							// can offer an ordered list instead of a bare text input.
							continue;
						}
						?>
						<section id="erfansanat-panel-<?php echo esc_attr( $tab ); ?>"
							class="erfansanat-admin__panel<?php echo $tab === $active ? ' is-active' : ''; ?>"
							data-panel="<?php echo esc_attr( $tab ); ?>"
							role="tabpanel"
							<?php echo $tab === $active ? '' : 'hidden'; ?>>
							<?php foreach ( $fields as $name => $field ) : ?>
								<?php self::render_field( $name, $field, $values[ $name ] ?? $field['default'] ); ?>
							<?php endforeach; ?>
						</section>
					<?php endforeach; ?>

					<?php SectionManager::render_panel(); ?>

					<p class="erfansanat-admin__actions">
						<button type="submit" class="button button-primary button-large">
							<?php esc_html_e( 'ذخیره تغییرات', 'erfansanat' ); ?>
						</button>
						<span class="erfansanat-admin__hint">
							<?php esc_html_e( 'تغییرات فقط پس از ذخیره اعمال می‌شوند.', 'erfansanat' ); ?>
						</span>
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Render one field.
	 *
	 * @param string               $name  Field name.
	 * @param array<string, mixed> $field Field definition.
	 * @param mixed                $value Current value.
	 * @return void
	 */
	private static function render_field( string $name, array $field, $value ): void {
		$id          = 'erfansanat-' . str_replace( '_', '-', $name );
		$type        = (string) $field['type'];
		$label       = (string) ( $field['label'] ?? $name );
		$description = (string) ( $field['description'] ?? '' );
		$locked      = isset( $field['capability'] ) && ! current_user_can( (string) $field['capability'] );
		?>
		<div class="erfansanat-field erfansanat-field--<?php echo esc_attr( $type ); ?>" data-name="<?php echo esc_attr( $name ); ?>">
			<label class="erfansanat-field__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>

			<?php if ( 'bool' === $type ) : ?>
				<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( (bool) $value ); ?> <?php disabled( $locked ); ?> />
			<?php elseif ( 'textarea' === $type || 'code' === $type ) : ?>
				<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="<?php echo 'code' === $type ? 6 : 3; ?>" class="<?php echo 'code' === $type ? 'large-text code' : 'large-text'; ?>" dir="<?php echo 'code' === $type ? 'ltr' : 'rtl'; ?>" <?php disabled( $locked ); ?>><?php echo esc_textarea( (string) $value ); ?></textarea>
			<?php elseif ( 'media' === $type ) : ?>
				<?php $preview = (int) $value > 0 ? wp_get_attachment_image_url( (int) $value, 'thumbnail' ) : ''; ?>
				<span class="erfansanat-media">
					<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) (int) $value ); ?>" data-media-input />
					<img src="<?php echo esc_url( (string) $preview ); ?>" alt="" class="erfansanat-media__preview" data-media-preview <?php echo $preview ? '' : 'hidden'; ?> />
					<button type="button" class="button" data-media-select><?php esc_html_e( 'انتخاب تصویر', 'erfansanat' ); ?></button>
					<button type="button" class="button-link" data-media-clear><?php esc_html_e( 'حذف', 'erfansanat' ); ?></button>
				</span>
			<?php elseif ( 'links' === $type ) : ?>
				<div class="erfansanat-repeater" data-repeater="links">
					<div class="erfansanat-repeater__rows" data-repeater-rows>
						<?php foreach ( (array) $value as $index => $row ) : ?>
							<div class="erfansanat-repeater__row">
								<input type="text" name="<?php echo esc_attr( $name ); ?>[<?php echo (int) $index; ?>][title]" value="<?php echo esc_attr( (string) ( $row['title'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'عنوان', 'erfansanat' ); ?>" />
								<input type="url" name="<?php echo esc_attr( $name ); ?>[<?php echo (int) $index; ?>][url]" value="<?php echo esc_attr( (string) ( $row['url'] ?? '' ) ); ?>" placeholder="https://" dir="ltr" />
								<button type="button" class="button-link erfansanat-repeater__remove" data-repeater-remove aria-label="<?php esc_attr_e( 'حذف ردیف', 'erfansanat' ); ?>">×</button>
							</div>
						<?php endforeach; ?>
					</div>
					<template data-repeater-template>
						<div class="erfansanat-repeater__row">
							<input type="text" name="<?php echo esc_attr( $name ); ?>[__INDEX__][title]" value="" placeholder="<?php esc_attr_e( 'عنوان', 'erfansanat' ); ?>" />
							<input type="url" name="<?php echo esc_attr( $name ); ?>[__INDEX__][url]" value="" placeholder="https://" dir="ltr" />
							<button type="button" class="button-link erfansanat-repeater__remove" data-repeater-remove aria-label="<?php esc_attr_e( 'حذف ردیف', 'erfansanat' ); ?>">×</button>
						</div>
					</template>
					<button type="button" class="button" data-repeater-add><?php esc_html_e( 'افزودن ردیف', 'erfansanat' ); ?></button>
				</div>
			<?php elseif ( 'list' === $type ) : ?>
				<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="4" class="large-text" aria-describedby="<?php echo esc_attr( $id ); ?>-help"><?php echo esc_textarea( implode( "\n", array_map( 'strval', (array) $value ) ) ); ?></textarea>
				<p class="description" id="<?php echo esc_attr( $id ); ?>-help"><?php esc_html_e( 'هر مورد در یک خط.', 'erfansanat' ); ?></p>
			<?php elseif ( 'terms' === $type ) : ?>
				<?php
				$taxonomy = (string) ( $field['taxonomy'] ?? 'product_cat' );
				$terms    = get_terms(
					array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => false,
					)
				);
				$selected = array_map( 'intval', (array) $value );
				?>
				<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>[]" multiple size="6" class="erfansanat-select--multi">
					<?php if ( is_array( $terms ) ) : ?>
						<?php foreach ( $terms as $term ) : ?>
							<option value="<?php echo esc_attr( (string) $term->term_id ); ?>" <?php echo in_array( (int) $term->term_id, $selected, true ) ? 'selected' : ''; ?>>
								<?php echo esc_html( $term->name ); ?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			<?php elseif ( 'int' === $type ) : ?>
				<input type="number" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) (int) $value ); ?>" min="<?php echo esc_attr( (string) ( $field['min'] ?? 0 ) ); ?>" max="<?php echo esc_attr( (string) ( $field['max'] ?? 100 ) ); ?>" class="small-text" />
			<?php elseif ( 'email' === $type ) : ?>
				<input type="email" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" dir="ltr" />
			<?php elseif ( 'url' === $type ) : ?>
				<input type="url" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" dir="ltr" />
			<?php elseif ( 'phone' === $type ) : ?>
				<input type="tel" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" dir="ltr" />
			<?php else : ?>
				<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" class="regular-text" <?php echo isset( $field['max'] ) ? 'maxlength="' . esc_attr( (string) $field['max'] ) . '"' : ''; ?> />
			<?php endif; ?>

			<?php if ( '' !== $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
			<?php if ( $locked ) : ?>
				<p class="description erfansanat-field__locked"><?php esc_html_e( 'برای ویرایش این فیلد مجوز unfiltered_html لازم است.', 'erfansanat' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * AJAX endpoint: verify capability and nonce, sanitise, save.
	 *
	 * @return void
	 */
	public static function handle_save(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			self::respond( array( 'message' => __( 'دسترسی مجاز نیست.', 'erfansanat' ) ), 403, false );
			return;
		}

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['nonce'] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			self::respond( array( 'message' => __( 'نشست منقضی شده است. صفحه را دوباره بارگذاری کنید.', 'erfansanat' ) ), 403, false );
			return;
		}

		$payload = isset( $_POST['options'] ) ? wp_unslash( $_POST['options'] ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitised per field in Settings::sanitize().
		if ( ! is_array( $payload ) ) {
			self::respond( array( 'message' => __( 'داده‌ای برای ذخیره ارسال نشد.', 'erfansanat' ) ), 400, false );
			return;
		}

		$result = Settings::sanitize( $payload, true );
		update_option( Settings::OPTION, $result['values'], false );
		Settings::flush();

		self::respond(
			array(
				'values'  => $result['values'],
				'errors'  => $result['errors'],
				'message' => __( 'تنظیمات ذخیره شد.', 'erfansanat' ),
			)
		);
	}
}
