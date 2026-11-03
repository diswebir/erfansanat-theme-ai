<?php
/** Basic HTML controls supported by the admin field renderer. */

namespace ErfanSanat\Theme\Admin\Fields\Types;

defined( 'ABSPATH' ) || exit;

/** Renders text-like, choice and toggle controls. */
final class BasicField {

	public static function render( array $field, $value, string $name, string $id ): string {
		$type        = $field['type'] ?? '';
		$description = ! empty( $field['description'] ) ? ' aria-describedby="' . esc_attr( $id . '-description' ) . '"' : '';
		$required    = ! empty( $field['required'] ) ? ' required' : '';
		$disabled    = ! empty( $field['disabled'] ) ? ' disabled' : '';
		$attributes  = $description . $required . $disabled;

		if ( 'textarea' === $type ) {
			$rows = max( 2, min( 20, absint( $field['rows'] ?? 4 ) ) );
			return '<textarea class="es-field__control" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="' . esc_attr( (string) $rows ) . '"' . $attributes . '>' . esc_textarea( is_scalar( $value ) ? (string) $value : '' ) . '</textarea>';
		}

		if ( 'toggle' === $type ) {
			$checked = in_array( $value, array( true, 1, '1', 'on', 'yes', 'true' ), true ) ? ' checked' : '';
			return '<input type="hidden" name="' . esc_attr( $name ) . '" value="0">'
				. '<label class="es-field__toggle"><input class="es-field__control" id="' . esc_attr( $id ) . '" type="checkbox" name="' . esc_attr( $name ) . '" value="1"' . $checked . $attributes . '><span>' . esc_html__( 'فعال', 'erfansanat' ) . '</span></label>';
		}

		if ( 'select' === $type ) {
			$options = is_array( $field['options'] ?? null ) ? $field['options'] : array();
			$html    = '<select class="es-field__control" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . $attributes . '>';
			foreach ( $options as $key => $label ) {
				$key      = (string) $key;
				$selected = (string) $value === $key ? ' selected' : '';
				$html    .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( (string) $label ) . '</option>';
			}
			return $html . '</select>';
		}

		if ( 'checkbox-group' === $type ) {
			$selected = is_array( $value ) ? array_map( 'strval', $value ) : array();
			$html     = '<div class="es-field__choices" role="group" aria-labelledby="' . esc_attr( $id ) . '-group-label">';
			foreach ( (array) ( $field['options'] ?? array() ) as $key => $label ) {
				$choice_id = $id . '-' . sanitize_html_class( (string) $key );
				$checked   = in_array( (string) $key, $selected, true ) ? ' checked' : '';
				$html     .= '<label class="es-field__choice" for="' . esc_attr( $choice_id ) . '"><input id="' . esc_attr( $choice_id ) . '" type="checkbox" name="' . esc_attr( $name . '[]' ) . '" value="' . esc_attr( (string) $key ) . '"' . $checked . $attributes . '><span>' . esc_html( (string) $label ) . '</span></label>';
			}
			return $html . '</div>';
		}

		$input_types = array( 'text' => 'text', 'url' => 'url', 'email' => 'email', 'tel' => 'tel', 'number' => 'number', 'color' => 'color' );
		if ( ! isset( $input_types[ $type ] ) ) {
			return '';
		}

		$html = '<input class="es-field__control" id="' . esc_attr( $id ) . '" type="' . esc_attr( $input_types[ $type ] ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( is_scalar( $value ) ? (string) $value : '' ) . '"';
		foreach ( array( 'min', 'max', 'step', 'placeholder', 'autocomplete' ) as $attribute ) {
			if ( isset( $field[ $attribute ] ) && is_scalar( $field[ $attribute ] ) ) {
				$html .= ' ' . $attribute . '="' . esc_attr( (string) $field[ $attribute ] ) . '"';
			}
		}
		return $html . $attributes . '>';
	}
}
