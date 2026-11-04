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
