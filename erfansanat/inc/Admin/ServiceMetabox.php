<?php
/**
 * Service details metabox (P07-T027, registered dependency of the services
 * overview P07-T026 which reads the same meta keys).
 *
 * Keys: _es_service_badge (text), _es_service_bullets (newline lines),
 * _es_service_note (text), _es_service_steps ("title|description" per line).
 * Nonce + capability guarded; textarea content is sanitized as plain text.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

final class ServiceMetabox {

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_metabox' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 1 );
	}

	public static function register_metabox(): void {
		add_meta_box( 'es-service-details', __( 'جزئیات خدمت (اقلام/مراحل/نشان)', 'erfansanat' ), array( __CLASS__, 'render' ), 'services', 'normal', 'default' );
	}

	/**
	 * @param \WP_Post $post Current post.
	 */
	public static function render( $post ): void {
		wp_nonce_field( 'es_service_details', 'es_service_details_nonce' );
		$badge   = (string) get_post_meta( $post->ID, '_es_service_badge', true );
		$bullets = (string) get_post_meta( $post->ID, '_es_service_bullets', true );
		$note    = (string) get_post_meta( $post->ID, '_es_service_note', true );
		$steps   = (string) get_post_meta( $post->ID, '_es_service_steps', true );
		?>
		<p><label style="display:block;font-weight:700;margin-bottom:4px" for="es-service-badge"><?php esc_html_e( 'نشان بالای عنوان', 'erfansanat' ); ?></label>
		<input type="text" id="es-service-badge" name="es_service_details[badge]" value="<?php echo esc_attr( $badge ); ?>" class="widefat"/></p>
		<p><label style="display:block;font-weight:700;margin-bottom:4px" for="es-service-bullets"><?php esc_html_e( 'اقلام اجرایی / مزیت‌ها (هر خط یک مورد)', 'erfansanat' ); ?></label>
		<textarea id="es-service-bullets" name="es_service_details[bullets]" rows="5" class="widefat"><?php echo esc_textarea( $bullets ); ?></textarea></p>
		<p><label style="display:block;font-weight:700;margin-bottom:4px" for="es-service-note"><?php esc_html_e( 'نکته مهم پایین اقلام (خالی = متن پیش‌فرض)', 'erfansanat' ); ?></label>
		<textarea id="es-service-note" name="es_service_details[note]" rows="2" class="widefat"><?php echo esc_textarea( $note ); ?></textarea></p>
		<p><label style="display:block;font-weight:700;margin-bottom:4px" for="es-service-steps"><?php esc_html_e( 'مراحل اجرا — هر خط: عنوان|توضیح (خالی = مسیر پیش‌فرض)', 'erfansanat' ); ?></label>
		<textarea id="es-service-steps" name="es_service_details[steps]" rows="5" class="widefat"><?php echo esc_textarea( $steps ); ?></textarea></p>
		<?php
	}

	/**
	 * Save handler (nonce + capability guarded).
	 *
	 * @param int $post_id Post id.
	 */
	public static function save( $post_id ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( 'services' !== get_post_type( $post_id ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( ! isset( $_POST['es_service_details_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_service_details_nonce'] ) ), 'es_service_details' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_service_details'] ) || ! is_array( $_POST['es_service_details'] ) ) {
			return;
		}
		$raw  = wp_unslash( $_POST['es_service_details'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized per field below.
		$keys = array(
			'badge'   => '_es_service_badge',
			'bullets' => '_es_service_bullets',
			'note'    => '_es_service_note',
			'steps'   => '_es_service_steps',
		);
		foreach ( $keys as $field => $meta_key ) {
			$value = (string) ( $raw[ $field ] ?? '' );
			$value = 'badge' === $field ? sanitize_text_field( $value ) : sanitize_textarea_field( $value );
			update_post_meta( $post_id, $meta_key, $value );
		}
	}

	/**
	 * Parse the steps meta into title/description pairs; empty → reference path.
	 *
	 * @param int $post_id Service id.
	 * @return array<int,array{title:string,description:string}>
	 */
	public static function steps( int $post_id ): array {
		$raw   = (string) get_post_meta( $post_id, '_es_service_steps', true );
		$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $raw ) ), static function ( $l ) { return '' !== $l; } );
		$out   = array();
		foreach ( $lines as $line ) {
			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			$out[] = array( 'title' => $parts[0], 'description' => $parts[1] ?? '' );
		}
		if ( $out ) {
			return $out;
		}
		return array(
			array( 'title' => 'بازدید و نیازسنجی', 'description' => 'بررسی میدانی محل پروژه' ),
			array( 'title' => 'طراحی اختصاصی', 'description' => 'طرح سه‌بعدی و سناریوی نوری' ),
			array( 'title' => 'تولید در کارخانه', 'description' => 'استاندارد IP67/IP68' ),
			array( 'title' => 'نصب و اجرا', 'description' => 'تیم مجرب در سراسر کشور' ),
			array( 'title' => 'تحویل و پشتیبانی', 'description' => '۲۴ ماه گارانتی' ),
		);
	}
}
