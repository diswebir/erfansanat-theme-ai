<?php
/**
 * Custom admin dashboard: menu, access control, tabbed shell and AJAX save.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;
use ErfanSanat\Theme\Options\Sanitizer;

final class ThemeOptions {

	public const SLUG         = 'erfansanat-dashboard';
	public const CAP          = 'manage_options';
	public const NONCE_ACTION = 'es_save_options';
	public const NONCE_NAME   = 'es_nonce';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}

		self::$booted = true;

		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_ajax_es_save_options', array( __CLASS__, 'ajax_save' ) );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Register the top-level dashboard + settings submenu, capability-gated.
	 */
	public static function register_menu(): void {
		add_menu_page(
			__( 'داشبورد عرفان صنعت', 'erfansanat' ),
			__( 'داشبورد عرفان صنعت', 'erfansanat' ),
			self::CAP,
			self::SLUG,
			array( __CLASS__, 'render_page' ),
			'dashicons-superhero-alt',
			3
		);

		add_submenu_page(
			self::SLUG,
			__( 'تنظیمات قالب', 'erfansanat' ),
			__( 'تنظیمات قالب', 'erfansanat' ),
			self::CAP,
			self::SLUG,
			array( __CLASS__, 'render_page' )
		);
	}

	public static function page_url(): string {
		return admin_url( 'admin.php?page=' . self::SLUG );
	}

	public static function can_manage(): bool {
		return current_user_can( self::CAP );
	}

	/**
	 * Render the dashboard page. Capability is enforced here as well so a
	 * direct request without permission receives a 403.
	 */
	public static function render_page(): void {
		if ( ! self::can_manage() ) {
			wp_die(
				esc_html__( 'شما دسترسی لازم برای مشاهده این صفحه را ندارید.', 'erfansanat' ),
				esc_html__( 'دسترسی غیرمجاز', 'erfansanat' ),
				array( 'response' => 403 )
			);
		}

		self::render_dashboard();
	}

	/**
	 * Enqueue dashboard CSS/JS only on the dashboard screen (no front-end leak).
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public static function enqueue_assets( $hook_suffix ): void {
		if ( false === strpos( (string) $hook_suffix, self::SLUG ) ) {
			return;
		}

		$css = ES_ASSETS_DIR . 'admin/options.css';
		$js  = ES_ASSETS_DIR . 'admin/options.js';

		wp_enqueue_style(
			'es-admin-options',
			ES_ASSETS_URI . 'admin/options.css',
			array( Typography::HANDLE ),
			is_file( $css ) ? (string) filemtime( $css ) : ES_VERSION
		);

		wp_enqueue_script(
			'es-admin-options',
			ES_ASSETS_URI . 'admin/options.js',
			array(),
			is_file( $js ) ? (string) filemtime( $js ) : ES_VERSION,
			true
		);

		wp_localize_script(
			'es-admin-options',
			'ES_OPTIONS',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
				'action'  => 'es_save_options',
			)
		);

		// Media picker (P02-T006) + repeater (P02-T007).
		wp_enqueue_media();

		$media_js = ES_ASSETS_DIR . 'admin/media-field.js';
		wp_enqueue_script(
			'es-admin-media-field',
			ES_ASSETS_URI . 'admin/media-field.js',
			array( 'media-editor' ),
			is_file( $media_js ) ? (string) filemtime( $media_js ) : ES_VERSION,
			true
		);

		$repeater_js = ES_ASSETS_DIR . 'admin/repeater.js';
		if ( is_file( $repeater_js ) ) {
			wp_enqueue_script(
				'es-admin-repeater',
				ES_ASSETS_URI . 'admin/repeater.js',
				array(),
				(string) filemtime( $repeater_js ),
				true
			);
		}
	}

	/**
	 * RTL tabbed dashboard shell with ARIA tabs and an AJAX save bar.
	 */
	public static function render_dashboard(): void {
		$tabs = Schema::tabs();
		$keys = array_keys( $tabs );
		?>
		<div class="wrap es-dashboard" dir="rtl" data-es-dashboard="true">
			<h1 class="es-dashboard__title"><?php echo esc_html__( 'داشبورد اختصاصی عرفان صنعت', 'erfansanat' ); ?></h1>

			<div class="es-dashboard__layout">
				<div class="es-tabs" role="tablist" aria-label="<?php echo esc_attr__( 'تب‌های تنظیمات قالب', 'erfansanat' ); ?>" aria-orientation="vertical">
					<?php foreach ( $tabs as $id => $tab ) : ?>
						<?php $selected = ( $id === $keys[0] ); ?>
						<button
							type="button"
							role="tab"
							id="es-tab-<?php echo esc_attr( $id ); ?>"
							class="es-tab"
							data-es-tab="<?php echo esc_attr( $id ); ?>"
							aria-controls="es-panel-<?php echo esc_attr( $id ); ?>"
							aria-selected="<?php echo $selected ? 'true' : 'false'; ?>"
							tabindex="<?php echo $selected ? '0' : '-1'; ?>"
						>
							<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>" aria-hidden="true"></span>
							<span class="es-tab__label"><?php echo esc_html( $tab['title'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="es-panels">
					<?php foreach ( $tabs as $id => $tab ) : ?>
						<?php $selected = ( $id === $keys[0] ); ?>
						<section
							role="tabpanel"
							id="es-panel-<?php echo esc_attr( $id ); ?>"
							class="es-panel"
							data-es-panel="<?php echo esc_attr( $id ); ?>"
							aria-labelledby="es-tab-<?php echo esc_attr( $id ); ?>"
							<?php echo $selected ? '' : 'hidden'; ?>
						>
							<h2 class="es-panel__title"><?php echo esc_html( $tab['title'] ); ?></h2>
							<div class="es-fields" data-es-fields="<?php echo esc_attr( $id ); ?>">
								<?php self::render_tab_content( $id ); ?>
							</div>
						</section>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="es-save-bar" data-es-save-bar="true">
				<button type="button" class="button button-primary es-save" data-es-save="true">
					<?php echo esc_html__( 'ذخیره تغییرات', 'erfansanat' ); ?>
				</button>
				<span class="es-save-status" role="status" aria-live="polite" data-es-save-status="true"></span>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the fields of a tab via the FieldRenderer (P02-T005+).
	 */
	private static function render_tab_content( string $tab_id ): void {
		if ( class_exists( FieldRenderer::class ) ) {
			FieldRenderer::render_tab( $tab_id );
		}
	}

	/**
	 * AJAX save endpoint (P02-T008 security, P02-T009 sanitization).
	 * Nonce + capability are verified before anything is written.
	 */
	public static function ajax_save(): void {
		if ( ! check_ajax_referer( self::NONCE_ACTION, self::NONCE_NAME, false ) ) {
			wp_send_json_error(
				array( 'message' => __( 'درخواست نامعتبر است (nonce).', 'erfansanat' ) ),
				403
			);
		}

		if ( ! current_user_can( self::CAP ) ) {
			wp_send_json_error(
				array( 'message' => __( 'شما دسترسی ذخیره تنظیمات را ندارید.', 'erfansanat' ) ),
				403
			);
		}

		$payload = isset( $_POST['values'] ) ? wp_unslash( $_POST['values'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$values  = json_decode( (string) $payload, true );

		if ( ! is_array( $values ) ) {
			wp_send_json_error(
				array( 'message' => __( 'داده ارسالی معتبر نیست.', 'erfansanat' ) ),
				400
			);
		}

		$result = self::sanitize_and_save( $values );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
					'errors'  => $result->get_error_data(),
				),
				400
			);
		}

		wp_send_json_success(
			array(
				'message' => __( 'تغییرات ذخیره شد.', 'erfansanat' ),
				'values'  => Store::all(),
				'errors'  => $result['errors'] ?? array(),
			)
		);
	}

	/**
	 * Sanitize + persist. Field-level errors do not abort the whole save.
	 *
	 * @param array<string,mixed> $values Raw candidate values.
	 * @return array<string,mixed>|\WP_Error
	 */
	private static function sanitize_and_save( array $values ) {
		if ( class_exists( Sanitizer::class ) ) {
			$clean = Sanitizer::sanitize_values( $values );
			Store::update( $clean['values'] );

			return $clean;
		}

		// Pre-sanitizer fallback: persist only schema-known keys.
		Store::update( $values );

		return array(
			'values' => $values,
			'errors' => array(),
		);
	}
}
