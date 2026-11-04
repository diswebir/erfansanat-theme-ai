<?php
/**
 * Project meta box: gallery slots, completion date and related products.
 *
 * @package ErfanSanat
 */

declare( strict_types=1 );

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Data\LegacyMeta;

defined( 'ABSPATH' ) || exit;

/**
 * The historical "اطلاعات پروژه" box.
 */
final class ProjectMetaboxes {

	/**
	 * Meta box id.
	 */
	public const BOX = 'erfansanat-project-info';

	/**
	 * Nonce action.
	 */
	public const NONCE = 'erfansanat_project_meta';

	/**
	 * Attach hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'add_meta_boxes_projects', array( self::class, 'add_box' ) );
		add_action( 'save_post_projects', array( self::class, 'save' ), 10, 2 );
	}

	/**
	 * Register the box.
	 *
	 * @return void
	 */
	public static function add_box(): void {
		add_meta_box(
			self::BOX,
			__( 'اطلاعات پروژه عرفان صنعت', 'erfansanat' ),
			array( self::class, 'render' ),
			'projects',
			'normal',
			'high'
		);
	}

	/**
	 * Render the box.
	 *
	 * @param \WP_Post $post Project.
	 * @return void
	 */
	public static function render( $post ): void {
		if ( ! $post instanceof \WP_Post ) {
			return;
		}
		$project_id = (int) $post->ID;
		wp_nonce_field( self::NONCE, 'erfansanat_project_nonce' );

		$slots   = LegacyMeta::project_image_slots();
		$gallery = LegacyMeta::project_gallery( $project_id );
		?>
		<div class="erfansanat-project-meta" dir="rtl">
			<h3><?php esc_html_e( 'گالری پروژه', 'erfansanat' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'پنج شکاف تاریخی ثابت‌اند: دوم، سوم، چهارم (foure-image)، پنجم و ششم. تصویر شاخص جدا و از پنل خودش مدیریت می‌شود.', 'erfansanat' ); ?>
			</p>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'جایگاه', 'erfansanat' ); ?></th>
						<th><?php esc_html_e( 'کلید متا', 'erfansanat' ); ?></th>
						<th><?php esc_html_e( 'شناسهٔ تصویر', 'erfansanat' ); ?></th>
						<th><?php esc_html_e( 'پیش‌نمایش', 'erfansanat' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $slots as $index => $key ) : ?>
						<?php $value = (int) get_post_meta( $project_id, $key, true ); ?>
						<tr>
							<td><?php echo esc_html( (string) ( $index + 2 ) ); ?></td>
							<td><code><?php echo esc_html( $key ); ?></code></td>
							<td>
								<span class="erfansanat-media">
									<input type="hidden" name="erfansanat_project[<?php echo esc_attr( $key ); ?>]"
										value="<?php echo esc_attr( (string) $value ); ?>" data-media-input />
									<button type="button" class="button" data-media-select><?php esc_html_e( 'انتخاب تصویر', 'erfansanat' ); ?></button>
									<button type="button" class="button-link" data-media-clear><?php esc_html_e( 'حذف', 'erfansanat' ); ?></button>
								</span>
							</td>
							<td>
								<?php if ( $value > 0 ) : ?>
									<?php echo wp_get_attachment_image( $value, 'thumbnail' ); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p class="description">
				<?php
				printf(
					/* translators: %d: number of resolved images. */
					esc_html__( 'در حال حاضر %d تصویر معتبر از این پروژه شناسایی شد.', 'erfansanat' ),
					count( $gallery )
				);
				?>
			</p>

			<h3><?php esc_html_e( 'جایگاه گالری مدرن', 'erfansanat' ); ?></h3>
			<p class="form-field">
				<label for="erfansanat-project-gallery"><?php esc_html_e( 'شناسهٔ تصاویر با کاما (5 شکاف بالا را بازنویسی می‌کند)', 'erfansanat' ); ?></label>
				<input type="text" id="erfansanat-project-gallery" name="erfansanat_project[gallery]"
					value="<?php echo esc_attr( implode( ',', $gallery ) ); ?>" class="large-text" dir="ltr" />
			</p>

			<h3><?php esc_html_e( 'جزئیات تکمیل', 'erfansanat' ); ?></h3>
			<p class="form-field">
				<label for="erfansanat-finish-pro-time"><?php esc_html_e( 'تاریخ تکمیل (مثال: 1404/01/01)', 'erfansanat' ); ?></label>
				<input type="text" id="erfansanat-finish-pro-time" name="erfansanat_project[finish-pro-time]"
					value="<?php echo esc_attr( LegacyMeta::text( $project_id, 'finish-pro-time' ) ); ?>"
					class="regular-text" dir="ltr" placeholder="1404/01/01" />
			</p>
			<p class="form-field">
				<label for="erfansanat-project-products"><?php esc_html_e( 'محصولات مرتبط (شناسهٔ محصول، جداشده با کاما)', 'erfansanat' ); ?></label>
				<input type="text" id="erfansanat-project-products" name="erfansanat_project[mortabet_mahsole_2]"
					value="<?php echo esc_attr( implode( ',', LegacyMeta::read_ids( $project_id, 'mortabet_mahsole_2' ) ) ); ?>"
					class="large-text" dir="ltr" />
			</p>
		</div>
		<?php
	}

	/**
	 * Persist the box.
	 *
	 * @param int      $post_id Project id.
	 * @param \WP_Post $post    Project.
	 * @return void
	 */
	public static function save( int $post_id, $post = null ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		$nonce = isset( $_POST['erfansanat_project_nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['erfansanat_project_nonce'] ) ) : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$payload = isset( $_POST['erfansanat_project'] ) ? (array) wp_unslash( $_POST['erfansanat_project'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitised per key in persist().
		self::persist( $post_id, $payload );
		unset( $post );
	}

	/**
	 * Write a sanitised payload to the historical keys.
	 *
	 * @param int                  $post_id Project id.
	 * @param array<string, mixed> $payload Raw payload.
	 * @return void
	 */
	public static function persist( int $post_id, array $payload ): void {
		if ( array_key_exists( 'gallery', $payload ) ) {
			LegacyMeta::save_project_gallery( $post_id, self::parse_ids( $payload['gallery'] ) );
		}

		foreach ( LegacyMeta::project_image_slots() as $key ) {
			if ( ! array_key_exists( $key, $payload ) ) {
				continue;
			}
			$id = absint( $payload[ $key ] );
			if ( $id > 0 && 'attachment' === get_post_type( $id ) ) {
				update_post_meta( $post_id, $key, wp_slash( (string) $id ) );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}

		if ( array_key_exists( 'finish-pro-time', $payload ) ) {
			$date = self::normalise_jalali( is_scalar( $payload['finish-pro-time'] ) ? (string) $payload['finish-pro-time'] : '' );
			if ( '' === $date ) {
				delete_post_meta( $post_id, 'finish-pro-time' );
			} else {
				update_post_meta( $post_id, 'finish-pro-time', wp_slash( $date ) );
			}
		}

		if ( array_key_exists( 'mortabet_mahsole_2', $payload ) ) {
			$ids = self::parse_ids( $payload['mortabet_mahsole_2'] );
			if ( array() === $ids ) {
				delete_post_meta( $post_id, 'mortabet_mahsole_2' );
			} else {
				update_post_meta( $post_id, 'mortabet_mahsole_2', wp_slash( (string) wp_json_encode( $ids ) ) );
			}
		}
	}

	/**
	 * Accept the Jalali date shapes the live site already stores.
	 *
	 * Stored values are Persian calendar dates such as `1404/01/01`; they are
	 * kept as text and only normalised to `YYYY/MM/DD` when they parse.
	 *
	 * @param string $value Raw date.
	 * @return string
	 */
	public static function normalise_jalali( string $value ): string {
		$value = trim( $value );
		if ( '' === $value ) {
			return '';
		}
		// Persian/Arabic digits to ASCII so the stored format stays consistent.
		$value = strtr(
			$value,
			array(
				'۰' => '0',
				'۱' => '1',
				'۲' => '2',
				'۳' => '3',
				'۴' => '4',
				'۵' => '5',
				'۶' => '6',
				'۷' => '7',
				'۸' => '8',
				'۹' => '9',
				'٠' => '0',
				'١' => '1',
				'٢' => '2',
				'٣' => '3',
				'٤' => '4',
				'٥' => '5',
				'٦' => '6',
				'٧' => '7',
				'٨' => '8',
				'٩' => '9',
			)
		);
		$value = preg_replace( '/[^0-9\/\-]/', '', $value );
		$value = (string) $value;
		if ( ! preg_match( '#^(\d{4})[/-](\d{1,2})[/-](\d{1,2})$#', $value, $matches ) ) {
			return '';
		}
		$year  = (int) $matches[1];
		$month = (int) $matches[2];
		$day   = (int) $matches[3];
		if ( $year < 1300 || $year > 1500 || $month < 1 || $month > 12 || $day < 1 || $day > 31 ) {
			return '';
		}
		return sprintf( '%04d/%02d/%02d', $year, $month, $day );
	}

	/**
	 * Parse a comma/JSON list into unique positive ids.
	 *
	 * @param mixed $raw Raw value.
	 * @return array<int, int>
	 */
	private static function parse_ids( $raw ): array {
		if ( is_string( $raw ) && '' !== trim( $raw ) ) {
			$trimmed = ltrim( $raw );
			$raw     = ( '{' === $trimmed[0] || '[' === $trimmed[0] ) ? LegacyMeta::to_array( $raw ) : explode( ',', $raw );
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
}
