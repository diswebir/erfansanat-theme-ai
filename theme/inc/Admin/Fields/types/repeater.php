<?php
/** Repeater field HTML with stable JetEngine-compatible row keys. */

namespace ErfanSanat\Theme\Admin\Fields\Types;

use ErfanSanat\Theme\Admin\Fields\FieldRenderer;
use ErfanSanat\Theme\Admin\Fields\RepeaterCodec;

defined( 'ABSPATH' ) || exit;

/** Renders existing rows plus a safe template for client-side additions. */
final class RepeaterControl {

	public static function render( array $field, $value, string $name, string $id ): string {
		$rows = class_exists( RepeaterCodec::class ) ? RepeaterCodec::decode( $value ) : ( is_array( $value ) ? $value : array() );
		$html = '<div class="es-repeater" data-es-repeater data-es-repeater-name="' . esc_attr( $name ) . '">';
		foreach ( $rows as $key => $row ) {
			if ( is_array( $row ) ) {
				$html .= self::render_row( $field, $row, $name, $id, (string) $key );
			}
		}

		$defaults = array();
		foreach ( (array) ( $field['fields'] ?? array() ) as $subfield ) {
			if ( is_array( $subfield ) && isset( $subfield['id'] ) ) {
				$defaults[ $subfield['id'] ] = $subfield['default'] ?? '';
			}
		}
		$html .= '<template data-es-repeater-template>' . self::render_row( $field, $defaults, $name, $id, 'item-__INDEX__' ) . '</template>';
		$html .= '<button class="button" type="button" data-es-repeater-add>' . esc_html__( 'افزودن مورد', 'erfansanat' ) . '</button>';
		$html .= '</div>';
		return $html;
	}

	/** Render one row, with every input name nested beneath its stable item key. */
	private static function render_row( array $field, array $row, string $name, string $id, string $key ): string {
		$html  = '<fieldset class="es-repeater__row" data-es-repeater-item="' . esc_attr( $key ) . '">';
		$html .= '<legend class="screen-reader-text">' . esc_html__( 'مورد تکرارشونده', 'erfansanat' ) . '</legend>';
		$html .= '<div class="es-repeater__actions">';
		$html .= '<button class="button es-repeater__handle" type="button" draggable="true" data-es-repeater-handle aria-label="' . esc_attr( esc_html__( 'برای جابه‌جایی بکشید یا از کلیدهای جهت استفاده کنید', 'erfansanat' ) ) . '">⠿</button>';
		$html .= '<button class="button" type="button" data-es-repeater-up aria-label="' . esc_attr( esc_html__( 'انتقال به بالا', 'erfansanat' ) ) . '">↑</button>';
		$html .= '<button class="button" type="button" data-es-repeater-down aria-label="' . esc_attr( esc_html__( 'انتقال به پایین', 'erfansanat' ) ) . '">↓</button>';
		$html .= '<button class="button-link-delete" type="button" data-es-repeater-remove>' . esc_html__( 'حذف مورد', 'erfansanat' ) . '</button>';
		$html .= '</div>';
		foreach ( (array) ( $field['fields'] ?? array() ) as $subfield ) {
			if ( ! is_array( $subfield ) || empty( $subfield['id'] ) || ! is_string( $subfield['id'] ) || ! preg_match( '/^[A-Za-z][A-Za-z0-9_-]*$/', $subfield['id'] ) ) {
				continue;
			}
			$child_name = $name . '[' . $key . '][' . $subfield['id'] . ']';
			$child_id   = $id . '-' . $key . '-' . $subfield['id'];
			$html      .= FieldRenderer::render( $subfield, $child_name, $child_id, $row[ $subfield['id'] ] ?? null );
		}
		return $html . '</fieldset>';
	}
}
