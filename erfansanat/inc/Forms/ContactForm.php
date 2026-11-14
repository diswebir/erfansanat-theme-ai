<?php
/**
 * Contact form handler (P08-T013).
 *
 * Wires the reference contact form to a public AJAX endpoint (shared Ajax
 * contract): honeypot rejection, schema validation/sanitization and storage as
 * an unread es_form_entry. Front-end success/error messages come from here.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Forms;

use ErfanSanat\Theme\Core\Ajax;

defined( 'ABSPATH' ) || exit;

final class ContactForm {

	private static bool $booted = false;

	/** Register the public submission endpoint (T013). */
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
			'contact_submit',
			array( __CLASS__, 'ajax_submit' ),
			array(
				'public' => true,
				'inputs' => array(
					'es_cf_name'         => array( 'type' => 'text', 'default' => '' ),
					'es_cf_phone'        => array( 'type' => 'text', 'default' => '' ),
					'es_cf_organization' => array( 'type' => 'text', 'default' => '' ),
					'es_cf_message'      => array( 'type' => 'text', 'default' => '' ),
					'es_cf_hp'           => array( 'type' => 'text', 'default' => '' ),
					'page'               => array( 'type' => 'text', 'default' => '' ),
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

	/**
	 * Full submission pipeline: honeypot (AC-02) → validate/sanitize → store.
	 *
	 * @param array<string,mixed> $payload Raw payload (reference field names).
	 * @param array<string,mixed> $context  Optional context.
	 * @return array{entry:int,message:string}|WP_Error
	 */
	public static function submit( array $payload, array $context = array() ) {
		// P08-T018: shared anti-spam — per-IP rate limit then the honeypot.
		$es_rate = SpamGuard::enforce_rate( 'contact' );
		if ( is_wp_error( $es_rate ) ) {
			return $es_rate;
		}
		// AC-02: a filled honeypot means a bot — reject without storing.
		if ( ! SpamGuard::honeypot_ok( $payload['es_cf_hp'] ?? '' ) ) {
			return new \WP_Error( 'es_form_honeypot', 'ارسال درخواست ممکن نیست. لطفاً صفحه را دوباره بارگذاری کنید.' );
		}
		$data = Schema::validate(
			'contact',
			array(
				'name'         => $payload['es_cf_name'] ?? '',
				'phone'        => $payload['es_cf_phone'] ?? '',
				'organization' => $payload['es_cf_organization'] ?? '',
				'message'      => $payload['es_cf_message'] ?? '',
			)
		);
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$entry_id = Entries::store( 'contact', $data, $context );
		if ( is_wp_error( $entry_id ) ) {
			return $entry_id;
		}
		SpamGuard::hit( 'contact' );
		// SC-03: notify after storing — an email failure keeps the entry.
		Mailer::send_entry( 'contact', (int) $entry_id, $data );
		return array(
			'entry'   => (int) $entry_id,
			'message' => 'درخواست شما با موفقیت ثبت شد!',
		);
	}
}
