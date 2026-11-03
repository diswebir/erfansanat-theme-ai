<?php
/** Media picker control markup; interaction is added by the media task. */

namespace ErfanSanat\Theme\Admin\Fields\Types;

defined( 'ABSPATH' ) || exit;

/** Renders an attachment ID field with an accessible preview area. */
final class MediaField {

	public static function render( array $field, $value, string $name, string $id ): string {
		$attachment_id = is_array( $value ) ? 0 : absint( $value );
		$multiple      = ! empty( $field['multiple'] );
		if ( $multiple && is_array( $value ) ) {
			$attachment_id = implode( ',', array_filter( array_map( 'absint', $value ) ) );
		}
		$preview = '';
		if ( $attachment_id && ! $multiple && function_exists( 'wp_get_attachment_image_url' ) ) {
			$url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
			if ( $url ) {
				$preview = '<img class="es-media__preview-image" src="' . esc_url( $url ) . '" alt="">';
			}
		}
		$mode = $multiple ? 'multiple' : 'single';
		$html  = '<div class="es-media-control" data-es-media data-es-media-mode="' . esc_attr( $mode ) . '">';
		$html .= '<input class="es-field__control es-media__value" id="' . esc_attr( $id ) . '" type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $attachment_id ) . '">';
		$html .= '<button class="button" type="button" data-es-media-select aria-controls="' . esc_attr( $id . '-preview' ) . '">' . esc_html__( 'انتخاب رسانه', 'erfansanat' ) . '</button> ';
		$html .= '<button class="button" type="button" data-es-media-remove>' . esc_html__( 'حذف', 'erfansanat' ) . '</button>';
		$html .= '<div class="es-media__preview" id="' . esc_attr( $id . '-preview' ) . '" aria-live="polite">' . $preview . '</div></div>';
		return $html;
	}
}
