<?php
/**
 * Per-type option sanitization with field-level error reporting.
 *
 * A single invalid field never aborts the whole save: valid fields are
 * persisted and invalid ones are returned as errors. HTML is only allowed
 * in `rich` fields (wp_kses_post); everything else is stripped/typed.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Admin\FieldRenderer;
use WP_Error;

final class Sanitizer {

	/**
	 * Sanitize a batch of candidate values.
	 *
	 * @param array<string,mixed> $values Raw input keyed by option key.
	 * @return array{values:array<string,mixed>,errors:array<string,string>}
	 */
	public static function sanitize_values( array $values ): array {
		$clean  = array();
		$errors = array();

		foreach ( $values as $key => $value ) {
			$key = (string) $key;
			if ( ! Schema::is_known( $key ) ) {
				continue; // Unknown keys are silently dropped.
			}

			$field  = Schema::get_field( $key );
			$result = self::sanitize_field( $field, $value );

			if ( is_wp_error( $result ) ) {
				$errors[ $key ] = $result->get_error_message();
			} else {
				$clean[ $key ] = $result;
			}
		}

		return array(
			'values' => $clean,
			'errors' => $errors,
		);
	}

	/**
	 * Sanitize one field by its declared type.
	 *
	 * @param array<string,mixed> $field Field definition.
	 * @param mixed             $value Raw value.
	 * @return mixed|WP_Error
	 */
	public static function sanitize_field( array $field, $value ) {
		$type = $field['type'] ?? 'text';

		switch ( $type ) {
			case 'text':
				return sanitize_text_field( (string) $value );

			case 'textarea':
				return sanitize_textarea_field( (string) $value );

			case 'rich':
				return wp_kses_post( (string) $value );

			case 'number':
				return self::sanitize_number( $value, $field );

			case 'select':
				return self::sanitize_select( $value, $field );

			case 'toggle':
			case 'checkbox':
				return (bool) $value;

			case 'color':
				return self::sanitize_color( $value );

			case 'email':
				return self::sanitize_email( $value );

			case 'url':
				return self::sanitize_url( $value );

			case 'media':
				return absint( $value );

			case 'icon':
				return self::sanitize_icon( $value );

			case 'link':
				return self::sanitize_link( $value );

			case 'repeater':
				return self::sanitize_repeater( $value, $field );

			case 'code':
				return self::sanitize_code( $value );

			default:
				return sanitize_text_field( (string) $value );
		}
	}

	/**
	 * @param array<string,mixed> $field Field definition.
	 * @return int|WP_Error
	 */
	private static function sanitize_number( $value, array $field ) {
		if ( '' === $value || null === $value ) {
			return (int) ( $field['default'] ?? 0 );
		}

		if ( ! is_numeric( $value ) ) {
			return new WP_Error( 'invalid_number', __( 'مقدار عددی معتبر نیست.', 'erfansanat' ) );
		}

		$number = (int) $value;
		if ( isset( $field['min'] ) ) {
			$number = max( (int) $field['min'], $number );
		}
		if ( isset( $field['max'] ) ) {
			$number = min( (int) $field['max'], $number );
		}

		return $number;
	}

	/**
	 * @param array<string,mixed> $field Field definition.
	 * @return string|WP_Error
	 */
	private static function sanitize_select( $value, array $field ) {
		$choices = (array) ( $field['choices'] ?? array() );
		$value   = (string) $value;

		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}

		return new WP_Error( 'invalid_choice', __( 'گزینه انتخابی معتبر نیست.', 'erfansanat' ) );
	}

	/**
	 * @return string|WP_Error
	 */
	private static function sanitize_color( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}

		$hex = sanitize_hex_color( $value );
		if ( null === $hex || '' === $hex ) {
			return new WP_Error( 'invalid_color', __( 'رنگ معتبر نیست (hex).', 'erfansanat' ) );
		}

		return $hex;
	}

	/**
	 * @return string|WP_Error
	 */
	private static function sanitize_email( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}

		$email = sanitize_email( $value );
		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', __( 'ایمیل معتبر نیست.', 'erfansanat' ) );
		}

		return $email;
	}

	/**
	 * @return string|WP_Error
	 */
	private static function sanitize_url( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}

		if ( preg_match( '/^(javascript|data|vbscript)\s*:/i', $value ) ) {
			return new WP_Error( 'invalid_url', __( 'پروتکل لینک مجاز نیست.', 'erfansanat' ) );
		}

		$url = esc_url_raw( $value );
		if ( '' === $url ) {
			return new WP_Error( 'invalid_url', __( 'لینک معتبر نیست.', 'erfansanat' ) );
		}

		return $url;
	}

	/**
	 * @return string|WP_Error
	 */
	private static function sanitize_icon( $value ) {
		$value = (string) $value;
		if ( '' === $value ) {
			return '';
		}

		$allowed = FieldRenderer::allowed_icons();
		if ( isset( $allowed[ $value ] ) ) {
			return $value;
		}

		return new WP_Error( 'invalid_icon', __( 'آیکون انتخابی مجاز نیست.', 'erfansanat' ) );
	}

	/**
	 * @param mixed $value Raw link value.
	 * @return array<string,string>
	 */
	private static function sanitize_link( $value ): array {
		$link = FieldRenderer::normalize_link( $value );

		return array(
			'url'    => self::sanitize_url_or_empty( $link['url'] ),
			'label'  => sanitize_text_field( $link['label'] ),
			'target' => $link['target'],
			'rel'    => $link['rel'],
		);
	}

	private static function sanitize_url_or_empty( string $url ): string {
		$result = self::sanitize_url( $url );
		return is_wp_error( $result ) ? '' : $result;
	}

	/**
	 * @param mixed             $value Raw items.
	 * @param array<string,mixed> $field Field definition.
	 * @return array<int,array<string,mixed>>|WP_Error
	 */
	private static function sanitize_repeater( $value, array $field ) {
		if ( is_string( $value ) ) {
			$decoded = json_decode( $value, true );
			$value   = is_array( $decoded ) ? $decoded : array();
		}

		if ( ! is_array( $value ) ) {
			return new WP_Error( 'invalid_repeater', __( 'ساختار آیتم‌های تکرارشونده معتبر نیست.', 'erfansanat' ) );
		}

		$subfields = (array) ( $field['subfields'] ?? array() );
		$max       = (int) ( $field['max'] ?? 20 );
		$items     = array();

		foreach ( array_values( $value ) as $item ) {
			if ( count( $items ) >= $max ) {
				break;
			}
			if ( ! is_array( $item ) ) {
				continue;
			}

			$clean_item = array();
			foreach ( $subfields as $subkey => $subfield ) {
				$raw    = $item[ $subkey ] ?? ( $subfield['default'] ?? '' );
				$result = self::sanitize_field( $subfield, $raw );
				$clean_item[ $subkey ] = is_wp_error( $result ) ? ( $subfield['default'] ?? '' ) : $result;
			}

			$items[] = $clean_item;
		}

		return $items;
	}

	/**
	 * Custom CSS/JS is admin-only (manage_options). Preserve content but ensure
	 * valid UTF-8 and strip null bytes. Output context is controlled in BrandTab.
	 *
	 * @param mixed $value Raw code.
	 */
	private static function sanitize_code( $value ): string {
		$value = (string) $value;
		$value = str_replace( "\0", '', $value );

		return wp_check_invalid_utf8( $value );
	}
}
