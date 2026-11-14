<?php
/**
 * Client feedback form handler (P08-T016).
 *
 * Rating must be 1..5 (Persian digits accepted), text sanitized through the
 * shared schema, entries start in "pending" moderation and can be approved.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Forms;

use ErfanSanat\Theme\Core\Ajax;

defined( 'ABSPATH' ) || exit;

final class FeedbackForm {

	private static bool $booted = false;

	/** Accepted rating range (AC-01). */
	public const MIN_RATING = 1;
	public const MAX_RATING = 5;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		if ( did_action( 'init' ) ) {
			self::register_endpoint();
		} else {
			add_action( 'init', array( __CLASS__, 'register_endpoint' ), 20 );
		}
	}

	public static function register_endpoint(): void {
		Ajax::register(
			'feedback_submit',
			array( __CLASS__, 'ajax_submit' ),
			array(
				'public' => true,
				'inputs' => array(
					'es_fb_name'    => array( 'type' => 'text', 'default' => '' ),
					'es_fb_rating'  => array( 'type' => 'text', 'default' => '' ),
					'es_fb_message' => array( 'type' => 'text', 'default' => '' ),
					'es_fb_hp'      => array( 'type' => 'text', 'default' => '' ),
					'page'          => array( 'type' => 'text', 'default' => '' ),
				),
			)
		);
	}

	/**
	 * Endpoint callback → submit().
	 *
	 * @param array<string,mixed> $input Whitelisted input.
	 * @return array<string,mixed>
	 */
	public static function ajax_submit( array $input ): array {
		return self::submit( $input, array( 'page' => $input['page'] ?? '' ) );
	}

	/** Normalize Persian/Arabic digits to Latin. */
	public static function normalize_digits( string $value ): string {
		$map = array( '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9' );
		return strtr( $value, $map );
	}

	/**
	 * Full pipeline: honeypot → rating range (AC-01) → schema sanitize (AC-02)
	 * → store with pending moderation (AC-03 / SC-01).
	 *
	 * @param array<string,mixed> $payload Raw payload.
	 * @param array<string,mixed> $context Optional context.
	 * @return array{entry:int,message:string}|WP_Error
	 */
	public static function submit( array $payload, array $context = array() ) {
		// P08-T018: shared anti-spam — per-IP rate limit then the honeypot.
		$es_rate = SpamGuard::enforce_rate( 'feedback' );
		if ( is_wp_error( $es_rate ) ) {
			return $es_rate;
		}
		if ( ! SpamGuard::honeypot_ok( $payload['es_fb_hp'] ?? '' ) ) {
			return new \WP_Error( 'es_form_honeypot', 'ارسال درخواست ممکن نیست. لطفاً صفحه را دوباره بارگذاری کنید.' );
		}
		$rating = (int) self::normalize_digits( trim( (string) ( $payload['es_fb_rating'] ?? '' ) ) );
		if ( $rating < self::MIN_RATING || $rating > self::MAX_RATING ) {
			return new \WP_Error( 'es_feedback_bad_rating', 'امتیاز باید بین ۱ تا ۵ باشد.' );
		}
		$data = Schema::validate(
			'feedback',
			array(
				'name'    => $payload['es_fb_name'] ?? '',
				'rating'  => (string) $rating,
				'message' => $payload['es_fb_message'] ?? '',
			)
		);
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$entry_id = Entries::store( 'feedback', $data, $context );
		if ( is_wp_error( $entry_id ) ) {
			return $entry_id;
		}
		SpamGuard::hit( 'feedback' );
		// SC-03: notify after storing — an email failure keeps the entry.
		Mailer::send_entry( 'feedback', (int) $entry_id, $data );
		update_post_meta( (int) $entry_id, '_es_feedback_status', 'pending' ); // AC-03.
		return array(
			'entry'   => (int) $entry_id,
			'message' => 'دیدگاه شما ثبت شد؛ پس از بررسی منتشر می‌شود.',
		);
	}

	/** Moderation status of a feedback entry (AC-03). */
	public static function moderation( int $entry_id ): string {
		$status = get_post_meta( $entry_id, '_es_feedback_status', true );
		return 'approve' === $status ? 'approve' : 'pending';
	}

	/** Approve a feedback entry (admin-side; capability enforced by callers). */
	public static function approve( int $entry_id ): bool {
		return (bool) update_post_meta( $entry_id, '_es_feedback_status', 'approve' );
	}
}
