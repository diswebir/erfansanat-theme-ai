<?php
/**
 * Shared content meta: hreflang triplets and the view counter.
 *
 * Both groups live on posts, pages, products and projects. The hreflang box is
 * deliberately only a data editor: the actual <link rel="alternate"> output is
 * coordinated with SEOPress in the audit phase, so the theme does not print
 * competing tags.
 *
 * @package ErfanSanat
 */

declare( strict_types=1 );

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Data\LegacyMeta;

defined( 'ABSPATH' ) || exit;

/**
 * "Translations" and "Views" meta boxes.
 */
final class ContentMetaboxes {

	/**
	 * Nonce action.
	 */
	public const NONCE = 'erfansanat_content_meta';

	/**
	 * Post types that receive the boxes.
	 *
	 * @return array<int, string>
	 */
	public static function post_types(): array {
		return array( 'post', 'page', 'product', 'projects' );
	}

	/**
	 * Attach hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'add_meta_boxes', array( self::class, 'add_boxes' ) );
		add_action( 'save_post', array( self::class, 'save' ), 10, 2 );
	}

	/**
	 * Register both boxes on all supported types.
	 *
	 * @return void
	 */
	public static function add_boxes(): void {
		foreach ( self::post_types() as $post_type ) {
			if ( ! post_type_exists( $post_type ) ) {
				continue;
			}
			add_meta_box(
				'erfansanat-hreflang',
				__( 'ترجمه‌ها و Hreflang', 'erfansanat' ),
				array( self::class, 'render_hreflang' ),
				$post_type,
				'side',
				'default'
			);
			add_meta_box(
				'erfansanat-views',
				__( 'میزان بازدید', 'erfansanat' ),
				array( self::class, 'render_views' ),
				$post_type,
				'side',
				'low'
			);
		}
	}

	/**
	 * Render the hreflang box.
	 *
	 * @param \WP_Post $post Post.
	 * @return void
	 */
	public static function render_hreflang( $post ): void {
		if ( ! $post instanceof \WP_Post ) {
			return;
		}
		wp_nonce_field( self::NONCE, 'erfansanat_content_nonce' );
		$links  = LegacyMeta::hreflang( (int) $post->ID );
		$labels = array(
			'fa' => __( 'نسخهٔ فارسی', 'erfansanat' ),
			'en' => __( 'نسخهٔ انگلیسی', 'erfansanat' ),
			'ar' => __( 'نسخهٔ عربی', 'erfansanat' ),
		);
		?>
		<div dir="rtl">
			<?php foreach ( $labels as $language => $label ) : ?>
				<p>
					<label for="erfansanat-hrefmeta-<?php echo esc_attr( $language ); ?>"><?php echo esc_html( $label ); ?></label>
					<input type="url" id="erfansanat-hrefmeta-<?php echo esc_attr( $language ); ?>"
						name="erfansanat_content[hrefmeta_<?php echo esc_attr( $language ); ?>]"
						value="<?php echo esc_attr( $links[ $language ] ); ?>" class="widefat" dir="ltr" placeholder="https://" />
				</p>
			<?php endforeach; ?>
			<p>
				<label for="erfansanat-same-key"><?php esc_html_e( 'کلید مشترک', 'erfansanat' ); ?></label>
				<input type="text" id="erfansanat-same-key" name="erfansanat_content[same_key_hrefl]"
					value="<?php echo esc_attr( LegacyMeta::text( (int) $post->ID, 'same_key_hrefl' ) ); ?>"
					class="widefat" dir="ltr" />
			</p>
			<p class="description">
				<?php esc_html_e( 'این مقادیر فقط دادهٔ ترجمه را نگه می‌دارند؛ تگ‌های alternate با SEOPress هماهنگ می‌شوند.', 'erfansanat' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render the views box.
	 *
	 * @param \WP_Post $post Post.
	 * @return void
	 */
	public static function render_views( $post ): void {
		if ( ! $post instanceof \WP_Post ) {
			return;
		}
		$primary = get_post_meta( (int) $post->ID, 'number2-viewc', true );
		$legacy  = get_post_meta( (int) $post->ID, 'number-viewc', true );
		?>
		<div dir="rtl">
			<p>
				<label for="erfansanat-number2-viewc"><?php esc_html_e( 'تعداد بازدید', 'erfansanat' ); ?></label>
				<input type="number" min="0" id="erfansanat-number2-viewc" name="erfansanat_content[number2-viewc]"
					value="<?php echo esc_attr( (string) LegacyMeta::views( (int) $post->ID ) ); ?>" class="small-text" />
			</p>
			<p class="description">
				<?php
				if ( '' === (string) $primary && is_numeric( $legacy ) ) {
					printf(
						/* translators: %s: legacy counter value. */
						esc_html__( 'مقدار از کلید قدیمی number-viewc خوانده شد (%s). تا زمانی که این فیلد ذخیره نشود، کلید قدیمی دست‌نخورده می‌ماند.', 'erfansanat' ),
						esc_html( (string) $legacy )
					);
				} else {
					esc_html_e( 'شمارنده با هر بازدید یک واحد افزایش می‌یابد.', 'erfansanat' );
				}
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Save both groups.
	 *
	 * @param int      $post_id Post id.
	 * @param \WP_Post $post    Post.
	 * @return void
	 */
	public static function save( int $post_id, $post = null ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		$nonce = isset( $_POST['erfansanat_content_nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['erfansanat_content_nonce'] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$payload = isset( $_POST['erfansanat_content'] ) ? (array) wp_unslash( $_POST['erfansanat_content'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitised in persist().
		self::persist( $post_id, $payload );
		unset( $post );
	}

	/**
	 * Write a sanitised payload.
	 *
	 * @param int                  $post_id Post id.
	 * @param array<string, mixed> $payload Raw payload.
	 * @return void
	 */
	public static function persist( int $post_id, array $payload ): void {
		foreach ( array( 'fa', 'en', 'ar' ) as $language ) {
			$key = 'hrefmeta_' . $language;
			if ( ! array_key_exists( $key, $payload ) ) {
				continue;
			}
			$url = esc_url_raw( is_scalar( $payload[ $key ] ) ? (string) $payload[ $key ] : '' );
			if ( '' === $url ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, wp_slash( $url ) );
			}
		}

		if ( array_key_exists( 'same_key_hrefl', $payload ) ) {
			$key = sanitize_text_field( is_scalar( $payload['same_key_hrefl'] ) ? (string) $payload['same_key_hrefl'] : '' );
			if ( '' === $key ) {
				delete_post_meta( $post_id, 'same_key_hrefl' );
			} else {
				update_post_meta( $post_id, 'same_key_hrefl', wp_slash( $key ) );
			}
		}

		if ( array_key_exists( 'number2-viewc', $payload ) ) {
			$views = is_scalar( $payload['number2-viewc'] ) ? (int) $payload['number2-viewc'] : 0;
			update_post_meta( $post_id, 'number2-viewc', max( 0, $views ) );
		}
	}

	/**
	 * Record a front-end view.
	 *
	 * Never increments for a logged-in editor looking at their own preview, so
	 * the counter stays meaningful.
	 *
	 * @param int $post_id Post id.
	 * @return int
	 */
	public static function record_view( int $post_id ): int {
		if ( is_user_logged_in() && current_user_can( 'edit_post', $post_id ) ) {
			return LegacyMeta::views( $post_id );
		}
		return LegacyMeta::increment_views( $post_id );
	}
}
