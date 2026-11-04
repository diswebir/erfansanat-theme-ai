<?php
/** Product catalog file and legacy-compatible product gallery fields. */

namespace ErfanSanat\Theme\Admin\Metaboxes\Tabs;

use ErfanSanat\Theme\Meta\MetaRepository;

defined( 'ABSPATH' ) || exit;

/** Renders and persists the catalog PDF and image gallery. */
final class ProductMediaTab {

	/** Render existing media values using the shared admin media picker. */
	public static function render( int $post_id ): string {
		$catalog = get_post_meta( $post_id, 'product-catalog-file', true );
		$catalog = is_scalar( $catalog ) ? (string) $catalog : '';
		$gallery = es_meta( $post_id, 'galerry_produ', array() );
		if ( ! is_array( $gallery ) || ! $gallery ) { $gallery = \es_product_gallery( $post_id ); }
		$gallery_value = implode( ',', array_map( 'absint', $gallery ) );
		$gallery_preview = '';
		if ( function_exists( 'wp_get_attachment_image' ) ) { foreach ( $gallery as $image_id ) { $image_id = absint( $image_id ); if ( $image_id && ( ! function_exists( 'wp_attachment_is_image' ) || wp_attachment_is_image( $image_id ) ) ) { $gallery_preview .= '<span class="es-media__item">' . wp_get_attachment_image( $image_id, 'thumbnail' ) . '</span>'; } } }
		$html  = '<div class="es-product-meta__media">';
		$html .= '<p><label for="es-product-catalog-file">' . esc_html__( 'فایل کاتالوگ (PDF)', 'erfansanat-theme' ) . '</label></p>';
		$html .= '<div class="es-media" data-es-media data-es-media-mode="single" data-es-media-kind="pdf" data-es-media-value="url"><input class="es-media__value widefat" type="url" id="es-product-catalog-file" name="es_product_meta[product-catalog-file]" value="' . esc_url( $catalog ) . '"><div class="es-media__preview">' . ( $catalog ? '<a href="' . esc_url( $catalog ) . '" target="_blank" rel="noopener">' . esc_html( wp_basename( $catalog ) ) . '</a>' : '' ) . '</div><button type="button" class="button" data-es-media-select>' . esc_html__( 'انتخاب فایل', 'erfansanat-theme' ) . '</button> <button type="button" class="button" data-es-media-remove>' . esc_html__( 'حذف', 'erfansanat-theme' ) . '</button></div>';
		$html .= '<p><label for="es-product-gallery">' . esc_html__( 'گالری محصول', 'erfansanat-theme' ) . '</label></p>';
		$html .= '<div class="es-media" data-es-media data-es-media-mode="multiple" data-es-media-kind="image"><input class="es-media__value widefat" type="text" id="es-product-gallery" name="es_product_meta[galerry_produ]" value="' . esc_attr( $gallery_value ) . '"><div class="es-media__preview">' . $gallery_preview . '</div><button type="button" class="button" data-es-media-select>' . esc_html__( 'انتخاب تصاویر', 'erfansanat-theme' ) . '</button> <button type="button" class="button" data-es-media-remove>' . esc_html__( 'پاک‌کردن گالری', 'erfansanat-theme' ) . '</button></div></div>';
		return $html;
	}

	/** Save only attachment-backed catalog URLs and valid image attachment IDs. */
	public static function save( int $post_id, array $input ): void {
		if ( array_key_exists( 'product-catalog-file', $input ) && is_scalar( $input['product-catalog-file'] ) ) {
			$url = esc_url_raw( (string) $input['product-catalog-file'] );
			$attachment_id = $url && function_exists( 'attachment_url_to_postid' ) ? absint( attachment_url_to_postid( $url ) ) : 0;
			if ( $attachment_id && 'application/pdf' === get_post_mime_type( $attachment_id ) ) {
				$canonical = wp_get_attachment_url( $attachment_id );
				if ( is_string( $canonical ) && '' !== $canonical ) { update_post_meta( $post_id, 'product-catalog-file', esc_url_raw( $canonical ) ); }
			} elseif ( '' === $url ) {
				delete_post_meta( $post_id, 'product-catalog-file' );
			}
		}
		if ( array_key_exists( 'galerry_produ', $input ) && is_scalar( $input['galerry_produ'] ) ) {
			$ids = self::valid_image_ids( (string) $input['galerry_produ'] );
			MetaRepository::set( $post_id, 'galerry_produ', implode( ',', $ids ) );
			update_post_meta( $post_id, '_product_image_gallery', implode( ',', $ids ) );
		}
	}

	/** Return unique IDs that resolve to image attachments. */
	public static function valid_image_ids( string $raw ): array {
		$ids = array();
		foreach ( explode( ',', $raw ) as $candidate ) {
			$id = absint( trim( $candidate ) );
			if ( $id < 1 || in_array( $id, $ids, true ) || 'attachment' !== get_post_type( $id ) || ! wp_attachment_is_image( $id ) ) { continue; }
			$ids[] = $id;
		}
		return $ids;
	}
}
