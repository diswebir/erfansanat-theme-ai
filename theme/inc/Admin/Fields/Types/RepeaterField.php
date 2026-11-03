<?php
/** Repeater field markup with stable item-N row keys. */

namespace ErfanSanat\Theme\Admin\Fields\Types;

use ErfanSanat\Theme\Admin\Fields\FieldRenderer;

defined( 'ABSPATH' ) || exit;

/** Renders existing repeater rows and a progressive-enhancement add control. */
final class RepeaterField {

	public static function render( array $field, $value, string $name, string $id ): string {
		if ( is_string( $value ) ) {
			$decoded = json_decode( $value, true );
			$value   = is_array( $decoded ) ? $decoded : array();
		}
		if ( ! is_array( $value ) ) {
			$value = array();
		}
		$html = '<div class="es-repeater" data-es-repeater data-es-repeater-name="' . esc_attr( $name ) . '">';
		foreach ( $value as $index => $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$key = is_string( $index ) && preg_match( '/^item-\d+$/', $index ) ? $index : 'item-' . absint( $index );
			$html .= self::render_row( $field, $row, $name, $id, $key );
		}
		$html .= '<button class="button" type="button" data-es-repeater-add>' . esc_html__( 'افزودن مورد', 'erfansanat' ) . '</button>';
		$html .= '</div>';
		return $html;
	}

	/** Render one stable row with only schema-declared child fields. */
	private static function render_row( array $field, array $row, string $name, string $id, string $key ): string {
		$html = '<fieldset class="es-repeater__row" data-es-repeater-item="' . esc_attr( $key ) . '"><legend>' . esc_html__( 'مورد', 'erfansanat' ) . '</legend>';
		foreach ( (array) ( $field['fields'] ?? array() ) as $subfield ) {
			if ( ! is_array( $subfield ) || empty( $subfield['id'] ) || ! is_string( $subfield['id'] ) || ! preg_match( '/^[A-Za-z][A-Za-z0-9_-]*$/', $subfield['id'] ) ) {
				continue;
			}
			$child_name = $name . '[' . $key . '][' . $subfield['id'] . ']';
			$child_id   = $id . '-' . $key . '-' . $subfield['id'];
			$html      .= FieldRenderer::render( $subfield, $child_name, $child_id, $row[ $subfield['id'] ] ?? null );
		}
		return $html . '<button class="button-link-delete" type="button" data-es-repeater-remove>' . esc_html__( 'حذف مورد', 'erfansanat' ) . '</button></fieldset>';
	}
}
