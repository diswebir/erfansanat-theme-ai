<?php
/**
 * Section manager metabox for pages: drag & drop ordering, enable/disable and
 * add/remove, saved over AJAX with nonce + edit_post capability checks.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Sections\Schema as SectionSchema;
use ErfanSanat\Theme\Sections\SectionManager;

final class SectionMetabox {

	public const NONCE_ACTION = 'es_save_sections';
	public const NONCE_NAME   = 'es_sections_nonce';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'add_meta_boxes', array( __CLASS__, 'register_metabox' ) );
		add_action( 'wp_ajax_es_save_sections', array( __CLASS__, 'ajax_save' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Register the metabox on pages (capability-gated).
	 */
	public static function register_metabox(): void {
		if ( ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		add_meta_box(
			'es-sections',
			__( 'مدیریت سکشن‌های صفحه', 'erfansanat' ),
			array( __CLASS__, 'render_metabox' ),
			array( 'page' ),
			'normal',
			'high'
		);
	}

	/**
	 * Render the section ordering UI.
	 *
	 * @param \WP_Post $post Current post.
	 */
	public static function render_metabox( $post ): void {
		$context = SectionManager::context_for( $post->ID );
		if ( '' === $context ) {
			$context = 'home';
		}

		$sections  = SectionManager::get_sections( $post->ID, $context );
		$available = SectionSchema::sections( $context );

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
		?>
		<div class="es-sections" data-es-sections data-post-id="<?php echo esc_attr( (string) $post->ID ); ?>" data-context="<?php echo esc_attr( $context ); ?>">
			<div class="es-sections__list" data-es-sections-list>
				<?php foreach ( $sections as $section ) : ?>
					<?php $def = $available[ $section['type'] ] ?? null; ?>
					<div class="es-section-row" draggable="true"
						data-section-id="<?php echo esc_attr( $section['id'] ); ?>"
						data-section-type="<?php echo esc_attr( $section['type'] ); ?>">
						<span class="es-section-row__handle" aria-hidden="true">⠿</span>
						<label class="es-section-row__toggle">
							<input type="checkbox" data-es-section-enabled <?php checked( (bool) $section['enabled'] ); ?> />
						</label>
						<span class="es-section-row__title"><?php echo esc_html( $def['title'] ?? $section['type'] ); ?></span>
						<span class="es-section-row__controls">
							<button type="button" class="button" data-es-section-up title="<?php esc_attr_e( 'بالا', 'erfansanat' ); ?>">↑</button>
							<button type="button" class="button" data-es-section-down title="<?php esc_attr_e( 'پایین', 'erfansanat' ); ?>">↓</button>
							<button type="button" class="button" data-es-section-remove title="<?php esc_attr_e( 'حذف', 'erfansanat' ); ?>">×</button>
						</span>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="es-sections__actions">
				<select data-es-section-add-select>
					<?php foreach ( $available as $type => $def ) : ?>
						<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $def['title'] ); ?></option>
					<?php endforeach; ?>
				</select>
				<button type="button" class="button" data-es-section-add><?php esc_html_e( 'افزودن سکشن', 'erfansanat' ); ?></button>
				<button type="button" class="button button-primary" data-es-sections-save><?php esc_html_e( 'ذخیره ترتیب', 'erfansanat' ); ?></button>
				<span class="es-sections__status" role="status" aria-live="polite" data-es-sections-status></span>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX endpoint: persist section order / enabled / membership.
	 */
	public static function ajax_save(): void {
		if ( ! check_ajax_referer( self::NONCE_ACTION, self::NONCE_NAME, false ) ) {
			wp_send_json_error( array( 'message' => __( 'درخواست نامعتبر است (nonce).', 'erfansanat' ) ), 403 );
		}

		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'شما دسترسی ویرایش این صفحه را ندارید.', 'erfansanat' ) ), 403 );
		}

		$context = isset( $_POST['context'] ) ? sanitize_key( wp_unslash( $_POST['context'] ) ) : '';
		if ( ! in_array( $context, SectionSchema::contexts(), true ) ) {
			$context = SectionManager::context_for( $post_id );
		}

		$sections = json_decode( (string) wp_unslash( $_POST['sections'] ?? '' ), true );
		if ( ! is_array( $sections ) ) {
			wp_send_json_error( array( 'message' => __( 'داده ارسالی معتبر نیست.', 'erfansanat' ) ), 400 );
		}

		SectionManager::save_sections( $post_id, $sections, $context );

		wp_send_json_success(
			array(
				'message'  => __( 'ترتیب سکشن‌ها ذخیره شد.', 'erfansanat' ),
				'sections' => SectionManager::get_sections( $post_id, $context ),
			)
		);
	}

	/**
	 * Enqueue sections.js only on the page editor.
	 *
	 * @param string $hook_suffix Current admin hook.
	 */
	public static function enqueue_assets( $hook_suffix ): void {
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'page' !== $screen->post_type ) {
			return;
		}

		$js = ES_ASSETS_DIR . 'admin/sections.js';
		wp_enqueue_script(
			'es-admin-sections',
			ES_ASSETS_URI . 'admin/sections.js',
			array(),
			is_file( $js ) ? (string) filemtime( $js ) : ES_VERSION,
			true
		);

		wp_localize_script(
			'es-admin-sections',
			'ES_SECTIONS',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
				'action'  => 'es_save_sections',
			)
		);
	}
}
