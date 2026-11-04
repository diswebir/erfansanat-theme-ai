<?php
/** Restrictive KSES policy shared by code settings and frontend output. */

namespace ErfanSanat\Theme\Helpers;

defined( 'ABSPATH' ) || exit;

/** Sanitizes the limited script markup accepted by administrator code fields. */
final class CustomCodeSanitizer {

	/** Keep script tags but deny inline event-handler and unrelated markup. */
	public static function sanitize( string $value ): string {
		if ( '' === trim( $value ) || ! function_exists( 'wp_kses' ) ) {
			return '';
		}
		$allowed = array(
			'script' => array(
				'src'            => true,
				'type'           => true,
				'async'          => true,
				'defer'          => true,
				'crossorigin'    => true,
				'integrity'      => true,
				'referrerpolicy' => true,
			),
		);
		return (string) wp_kses( $value, $allowed, array( 'http', 'https' ) );
	}
}
