<?php
/**
 * Public form submission endpoint.
 *
 * One `admin-post.php` action serves every public form. The rules are the same
 * for all of them and are enforced here, not in the browser:
 *
 * - a nonce, so a third-party page cannot post on a visitor's behalf;
 * - a honeypot field, which a human never fills and most bots do;
 * - a per-IP rate limit, so one script cannot flood the inbox;
 * - a minimum time-on-form, because a submission three milliseconds after load
 *   is not a person;
 * - strict length and type limits on every field, with no HTML accepted at all.
 *
 * The visitor is redirected back with a status code in the query string rather
 * than being shown a JSON blob, so the form degrades to a normal page POST when
 * JavaScript is unavailable.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Forms;

defined( 'ABSPATH' ) || exit;

/**
 * Validate, store and acknowledge a public form submission.
 */
final class FormHandler {

	/**
	 * The `admin-post.php` action name.
	 */
	public const ACTION = 'erfansanat_submit';

	/**
	 * Nonce action.
	 */
	public const NONCE = 'erfansanat_form_nonce';

	/**
	 * The redirect argument carrying the outcome.
	 */
	public const STATUS_ARG = 'es_sent';

	/**
	 * Seconds a form must be on screen before a submission is believed.
	 */
	private const MIN_SECONDS = 3;

	/**
	 * Maximum submissions per hashed address per window.
	 *
	 * Not `1`: a shared office NAT, a household and a returning visitor all send
	 * more than one enquiry, and locking the second one out costs a real lead. The
	 * limit exists to stop a script, not a person.
	 */
	private const RATE_LIMIT = 5;

	/**
	 * Rate-limit window, in seconds.
	 */
	private const RATE_WINDOW = HOUR_IN_SECONDS;

	/**
	 * Forms that may post, and the fields each one requires.
	 *
	 * @var array<string, array{required: array<int, string>, optional: array<int, string>}>
	 */
	private const SCHEMAS = array(
		'contact'  => array(
			'required' => array( 'name', 'phone', 'message' ),
			'optional' => array( 'email', 'org', 'subject' ),
		),
		'quote'    => array(
			'required' => array( 'name', 'phone', 'message' ),
			'optional' => array( 'email', 'org', 'subject' ),
		),
		'callback' => array(
			'required' => array( 'name', 'phone' ),
			'optional' => array( 'message' ),
		),
	);

	/**
	 * Field length caps, so a hostile request cannot write a megabyte per field.
	 *
	 * @var array<string, int>
	 */
	private const MAX_LENGTH = array(
		'name'    => 120,
		'phone'   => 32,
		'email'   => 190,
		'org'     => 160,
		'subject' => 160,
		'message' => 4000,
	);

	/**
	 * Attach the hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'admin_post_nopriv_' . self::ACTION, array( self::class, 'handle' ) );
		add_action( 'admin_post_' . self::ACTION, array( self::class, 'handle' ) );
	}

	/**
	 * The forms a template may render.
	 *
	 * @return array<int, string>
	 */
	public static function forms(): array {
		return array_keys( self::SCHEMAS );
	}

	/**
	 * Read the outcome of a previous submission for the current request.
	 *
	 * @return array{state: string, message: string}
	 */
	public static function status(): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status display.
		$raw = isset( $_GET[ self::STATUS_ARG ] ) ? sanitize_key( wp_unslash( (string) $_GET[ self::STATUS_ARG ] ) ) : '';

		switch ( $raw ) {
			case 'ok':
				return array(
					'state'   => 'ok',
					'message' => __( 'پیام شما ثبت شد. کارشناسان ما در اولین فرصت با شما تماس می‌گیرند.', 'erfansanat' ),
				);
			case 'invalid':
				return array(
					'state'   => 'error',
					'message' => __( 'لطفاً فیلدهای ضروری را کامل و درست پر کنید.', 'erfansanat' ),
				);
			case 'slow':
				return array(
					'state'   => 'error',
					'message' => __( 'ارسال بیش از حد سریع بود. لطفاً چند لحظه صبر کنید و دوباره تلاش کنید.', 'erfansanat' ),
				);
			case 'throttled':
				return array(
					'state'   => 'error',
					'message' => __( 'تعداد پیام‌های ارسالی از این دستگاه زیاد بوده است. کمی بعد دوباره تلاش کنید.', 'erfansanat' ),
				);
			case 'failed':
				return array(
					'state'   => 'error',
					'message' => __( 'ثبت پیام ممکن نشد. لطفاً با شمارهٔ تماس با ما در ارتباط باشید.', 'erfansanat' ),
				);
			default:
				return array(
					'state'   => '',
					'message' => '',
				);
		}
	}

	/**
	 * Handle one submission.
	 *
	 * @return void
	 */
	public static function handle(): void {
		$redirect = wp_get_referer();
		if ( ! is_string( $redirect ) || '' === $redirect ) {
			$redirect = home_url( '/' );
		}
		$redirect = remove_query_arg( self::STATUS_ARG, $redirect );

		$fail = static function ( string $reason ) use ( $redirect ): void {
			wp_safe_redirect( add_query_arg( self::STATUS_ARG, $reason, $redirect ) . '#contact' );
			exit;
		};

		// 1. The nonce. Without it the request did not come from our form.
		$nonce = isset( $_POST['es_nonce'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['es_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			$fail( 'invalid' );
		}

		// 2. The honeypot. A real visitor never sees this field, so a filled one
		// is a bot. It reports success so the bot does not learn to adapt.
		$honeypot = isset( $_POST['es_website'] ) ? trim( (string) wp_unslash( $_POST['es_website'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Only compared to empty.
		if ( '' !== $honeypot ) {
			wp_safe_redirect( add_query_arg( self::STATUS_ARG, 'ok', $redirect ) . '#contact' );
			exit;
		}

		// 3. Time on form. Absent or malformed is treated as too fast.
		$started = isset( $_POST['es_started'] ) ? (int) $_POST['es_started'] : 0;
		if ( $started <= 0 || ( time() - $started ) < self::MIN_SECONDS ) {
			$fail( 'slow' );
		}

		// 4. Rate limit, keyed on a hash so no raw address is stored.
		if ( self::is_throttled() ) {
			$fail( 'throttled' );
		}

		// 5. The form must be one this theme renders.
		$form = isset( $_POST['es_form'] ) ? sanitize_key( wp_unslash( (string) $_POST['es_form'] ) ) : '';
		if ( ! isset( self::SCHEMAS[ $form ] ) ) {
			$fail( 'invalid' );
		}

		// 6. Fields, read strictly by name and trimmed to their caps.
		$payload = array( '_form' => $form );
		$schema  = self::SCHEMAS[ $form ];

		$provided = array();

		foreach ( array_merge( $schema['required'], $schema['optional'] ) as $field ) {
			$raw = isset( $_POST[ $field ] ) ? wp_unslash( (string) $_POST[ $field ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitised per field below.
			$cap = self::MAX_LENGTH[ $field ] ?? 200;

			// Remember what the visitor actually typed. `sanitize_email()` turns a
			// malformed address into an empty string, so without this an invalid
			// e-mail would be silently dropped and the reply would go nowhere.
			$provided[ $field ] = '' !== trim( $raw );

			switch ( $field ) {
				case 'email':
					$value = sanitize_email( $raw );
					break;
				case 'message':
					$value = sanitize_textarea_field( $raw );
					break;
				case 'phone':
					// Persian and Arabic-Indic digits are normalised so the number
					// is dialable whichever keyboard the visitor used.
					$value = self::normalise_digits( $raw );
					$value = (string) preg_replace( '/[^0-9+\-\s()]/', '', $value );
					break;
				default:
					$value = sanitize_text_field( $raw );
					break;
			}

			$payload[ $field ] = mb_substr( trim( (string) $value ), 0, $cap );
		}

		// 7. Required fields must survive sanitising, not merely be present.
		foreach ( $schema['required'] as $field ) {
			$value = (string) ( $payload[ $field ] ?? '' );
			if ( '' === $value ) {
				$fail( 'invalid' );
			}
			if ( 'phone' === $field && strlen( preg_replace( '/\D/', '', $value ) ) < 8 ) {
				$fail( 'invalid' );
			}
			if ( 'email' === $field && ! is_email( $value ) ) {
				$fail( 'invalid' );
			}
		}

		// A typed e-mail must either be valid or be rejected. Silently emptying it
		// loses the only way to answer the enquiry.
		if ( ! empty( $provided['email'] ) ) {
			$email = (string) ( $payload['email'] ?? '' );
			if ( '' === $email || ! is_email( $email ) ) {
				$fail( 'invalid' );
			}
		}

		// 8. Store first, then notify. A failed mail must not lose the message.
		$id = Inbox::store( $payload, $form );
		if ( $id <= 0 ) {
			$fail( 'failed' );
		}

		Inbox::notify( $id, $payload );
		self::mark_throttled();

		/**
		 * Fires after a public form has been stored.
		 *
		 * @param int                  $id      Message id.
		 * @param array<string, mixed> $payload Validated fields.
		 * @param string               $form    Form key.
		 */
		do_action( 'erfansanat_form_submitted', $id, $payload, $form );

		wp_safe_redirect( add_query_arg( self::STATUS_ARG, 'ok', $redirect ) . '#contact' );
		exit;
	}

	/**
	 * Convert Persian / Arabic-Indic digits to ASCII.
	 *
	 * @param string $value Raw input.
	 * @return string
	 */
	private static function normalise_digits( string $value ): string {
		$persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
		$arabic  = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
		$latin   = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );

		$value = str_replace( $persian, $latin, $value );
		return str_replace( $arabic, $latin, $value );
	}

	/**
	 * Per-address submission count for the current window.
	 *
	 * @return array{count: int, expires: int}
	 */
	private static function bucket(): array {
		$key = 'es_form_' . substr( wp_hash( self::address() ), 0, 20 );
		$row = get_transient( $key );

		if ( ! is_array( $row ) || ! isset( $row['count'], $row['expires'] ) ) {
			return array(
				'count'   => 0,
				'expires' => time() + self::RATE_WINDOW,
			);
		}

		return array(
			'count'   => (int) $row['count'],
			'expires' => (int) $row['expires'],
		);
	}

	/**
	 * Whether this address has spent its allowance.
	 *
	 * @return bool
	 */
	public static function is_throttled(): bool {
		$bucket = self::bucket();
		return $bucket['count'] >= self::RATE_LIMIT;
	}

	/**
	 * Record one accepted submission.
	 *
	 * @return void
	 */
	private static function mark_throttled(): void {
		$bucket = self::bucket();
		$key    = 'es_form_' . substr( wp_hash( self::address() ), 0, 20 );

		set_transient(
			$key,
			array(
				'count'   => $bucket['count'] + 1,
				'expires' => $bucket['expires'],
			),
			max( 60, $bucket['expires'] - time() )
		);
	}

	/**
	 * The sender's address, for rate limiting only.
	 *
	 * @return string
	 */
	private static function address(): string {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? (string) sanitize_text_field( wp_unslash( (string) $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	}

	/**
	 * Clear the rate-limit counter. Test and maintenance helper only.
	 *
	 * @return void
	 */
	public static function reset_limits(): void {
		delete_transient( 'es_form_' . substr( wp_hash( self::address() ), 0, 20 ) );
	}
}
