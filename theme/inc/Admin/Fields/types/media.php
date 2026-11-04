<?php
/** Media field control markup shared by single and multiple attachment fields. */

namespace ErfanSanat\Theme\Admin\Fields\Types;

defined( 'ABSPATH' ) || exit;

/**
 * Produces a wp.media-compatible attachment control.
 */
final class MediaControl {

	/** Render the hidden ID/CSV input, picker buttons and attachment previews. */
	public static function render( array $field, $value, string $name, string $id ): string {
		$multiple = ! empty( $field['multiple'] );
		$ids      = self::ids( $value, $multiple, $field );
		$preview  = '';
		if ( function_exists( 'wp_get_attachment_image_url' ) ) {
			foreach ( $ids as $attachment_id ) {
				$url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
				if ( $url ) {
					$preview .= '<img class="es-media__preview-image" src="' . esc_url( $url ) . '" alt="">';
				}
			}
		}

		$mode = $multiple ? 'multiple' : 'single';
		$html  = '<div class="es-media-control" data-es-media data-es-media-mode="' . esc_attr( $mode ) . '" data-es-media-kind="' . esc_attr( $field['media_kind'] ?? 'any' ) . '">';
		$html .= '<input class="es-field__control es-media__value" id="' . esc_attr( $id ) . '" type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( implode( ',', $ids ) ) . '">';
		$html .= '<button class="button" type="button" data-es-media-select aria-controls="' . esc_attr( $id . '-preview' ) . '">' . esc_html__( 'انتخاب رسانه', 'erfansanat' ) . '</button> ';
		$html .= '<button class="button" type="button" data-es-media-remove>' . esc_html__( 'حذف', 'erfansanat' ) . '</button>';
		$html .= '<div class="es-media__preview" id="' . esc_attr( $id . '-preview' ) . '" aria-live="polite">' . $preview . '</div></div>';
		return $html;
	}

	/** Parse saved ID or CSV values, rejecting non-positive IDs. */
	private static function ids( $value, bool $multiple, array $field ): array {
		$raw = is_array( $value ) ? $value : ( is_scalar( $value ) ? explode( ',', (string) $value ) : array() );
		$ids = array();
		foreach ( $raw as $item ) {
			if ( is_scalar( $item ) && preg_match( '/^[1-9][0-9]*$/', trim( (string) $item ) ) ) {
				$id = (int) $item;
				$post = function_exists( 'get_post' ) ? get_post( $id ) : null;
				$valid = $post && 'attachment' === ( $post->post_type ?? '' );
				if ( $valid && 'image' === ( $field['media_kind'] ?? '' ) ) {
					$valid = function_exists( 'wp_attachment_is_image' ) && wp_attachment_is_image( $id );
				}
				if ( $valid && ! in_array( $id, $ids, true ) ) {
					$ids[] = $id;
				}
			}
			if ( ! $multiple ) {
				break;
			}
		}
		return $ids;
	}
}
