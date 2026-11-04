<?php
/** Global compatibility helpers for post and term metadata. */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'es_meta' ) ) {
	/** Read a decoded post meta value through the request cache. */
	function es_meta( $post_id, $key, $fallback = null ) {
		return \ErfanSanat\Theme\Meta\MetaRepository::get( (int) $post_id, (string) $key, $fallback );
	}
}

if ( ! function_exists( 'es_term_meta' ) ) {
	/** Read a decoded term meta value through the request cache. */
	function es_term_meta( $term_id, $key, $fallback = null ) {
		return \ErfanSanat\Theme\Meta\MetaRepository::get_term( (int) $term_id, (string) $key, $fallback );
	}
}

if ( ! function_exists( 'es_get_views' ) ) {
	/** Read the new view counter with a fallback to its historic key. */
	function es_get_views( $post_id ) {
		return \ErfanSanat\Theme\Meta\Views::get_views( (int) $post_id );
	}
}

if ( ! function_exists( 'es_product_gallery' ) ) {
	/** Return product gallery attachment IDs using the legacy fallback reader. */
	function es_product_gallery( $post_id = 0 ) {
		$id = $post_id ? absint( $post_id ) : absint( get_the_ID() );
		return \ErfanSanat\Theme\Woo\ProductGallery::get( $id );
	}
}
