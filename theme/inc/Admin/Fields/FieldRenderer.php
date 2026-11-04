<?php
/**
 * Escaped renderer for declarative admin fields.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin\Fields;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/Types/BasicField.php';
require_once __DIR__ . '/Types/MediaField.php';
require_once __DIR__ . '/Types/RepeaterField.php';

/**
 * Renders field schema to accessible HTML controls.
 */
final class FieldRenderer {

	/** Render a collection of fields with a page-specific nonce. */
	public static function render_fields( array $fields, string $nonce_action ): string {
		$html       = '';
		$nonce_name = 'es_options_nonce_' . sanitize_key( $nonce_action );
		if ( function_exists( 'wp_nonce_field' ) ) {
			$html .= (string) wp_nonce_field( $nonce_action, $nonce_name, false, false );
		}
		foreach ( $fields as $field ) {
			if ( is_array( $field ) ) {
				$html .= self::render( $field );
			}
		}
		return $html;
	}

	/** Render one field definition and its associated accessible label. */
	public static function render( array $field, ?string $name = null, ?string $id = null, $value = null ): string {
		$type = $field['type'] ?? '';
		$path = isset( $field['id'] ) && is_string( $field['id'] ) ? $field['id'] : '';
		if ( ! in_array( $type, Schema::types(), true ) || ! Schema::valid_path( $path ) ) {
			return '';
		}

		$input_name = $name ?? self::input_name( $path );
		$input_id   = $id ?? 'es-field-' . str_replace( '.', '-', $path );
		$label      = (string) ( $field['label'] ?? '' );
		$description = isset( $field['description'] ) ? (string) $field['description'] : '';
		$current    = null === $value ? ( $field['default'] ?? '' ) : $value;
		$control    = self::control( $field, $current, $input_name, $input_id );
		if ( '' === $control ) {
			return '';
		}

		$html  = '<div class="es-field es-field--' . esc_attr( $type ) . '">';
		$html .= '<label class="es-field__label" for="' . esc_attr( $input_id ) . '">' . esc_html( $label ) . '</label>';
		$html .= $control;
		if ( '' !== $description ) {
			$html .= '<p class="es-field__description" id="' . esc_attr( $input_id . '-description' ) . '">' . esc_html( $description ) . '</p>';
		}
		$html .= '</div>';
		return $html;
	}

	/** Render a field control; separated so repeaters can reuse it with nested names. */
	public static function control( array $field, $value, string $name, string $id ): string {
		$type = $field['type'] ?? '';
		if ( 'media' === $type ) {
			return \ErfanSanat\Theme\Admin\Fields\Types\MediaField::render( $field, $value, $name, $id );
		}
		if ( 'repeater' === $type ) {
			return \ErfanSanat\Theme\Admin\Fields\Types\RepeaterField::render( $field, $value, $name, $id );
		}
		return \ErfanSanat\Theme\Admin\Fields\Types\BasicField::render( $field, $value, $name, $id );
	}

	/** Map a schema path to the options form's bracketed name. */
	public static function input_name( string $path ): string {
		if ( ! Schema::valid_path( $path ) ) {
			return '';
		}
		return 'es_options[' . implode( '][', explode( '.', $path ) ) . ']';
	}
}
