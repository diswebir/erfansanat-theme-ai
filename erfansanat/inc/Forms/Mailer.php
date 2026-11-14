<?php
/**
 * Form entry email notifications (P08-T018).
 *
 * Sends the admin a plain-text notice for every stored form entry. The
 * recipient is configurable (option `es_form_recipient`, filter
 * `es_form_recipient`, falling back to admin_email) and a failed email must
 * never lose or invalidate the stored entry (AC-03 / SC-03).
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Forms;

defined( 'ABSPATH' ) || exit;

final class Mailer {

	/** Configurable recipient option key (AC-01). */
	public const OPTION_KEY = 'es_form_recipient';

	/**
	 * AC-01: resolve the notification recipient: option → filter → admin_email.
	 */
	public static function recipient(): string {
		$to = trim( (string) get_option( self::OPTION_KEY, '' ) );
		if ( '' === $to ) {
			$to = trim( (string) get_option( 'admin_email', '' ) );
		}
		$to = trim( (string) apply_filters( 'es_form_recipient', $to ) );
		if ( ! is_email( $to ) ) {
			$to = (string) get_option( 'admin_email', '' );
		}
		return $to;
	}

	/**
	 * Notify the recipient about one stored entry. Never throws; a failure
	 * simply returns false and the caller keeps the already-stored entry.
	 *
	 * @param string $type     Form type (contact|quote|recruitment|feedback).
	 * @param int    $entry_id Stored entry id.
	 * @param array  $data     Validated entry data.
	 */
	public static function send_entry( string $type, int $entry_id, array $data ): bool {
		$types = Schema::types();
		$label = (string) ( $types[ $type ] ?? $type );
		$to     = self::recipient();
		if ( ! is_email( $to ) ) {
			return false;
		}
		$site    = wp_specialchars_decode( (string) get_bloginfo( 'name' ), ENT_QUOTES );
		$subject = sprintf( '[%s] %s — %s', $site, $label, (string) ( $data['name'] ?? '' ) );
		$lines   = array( $label, str_repeat( '=', 30 ) );
		foreach ( $data as $field => $value ) {
			$value = (string) $value;
			if ( '' === $value ) {
				continue;
			}
			$lines[] = $field . ': ' . $value;
		}
		$lines[] = 'entry: ' . $entry_id;
		$lines[] = 'date: ' . gmdate( 'Y-m-d H:i' );
		try {
			$sent = wp_mail(
				$to,
				$subject,
				implode( "\n", $lines ),
				array( 'Content-Type: text/plain; charset=UTF-8' )
			);
		} catch ( \Throwable $e ) { // AC-03: a mail exception must not break the form flow.
			$sent = false;
		}
		return (bool) $sent;
	}
}
