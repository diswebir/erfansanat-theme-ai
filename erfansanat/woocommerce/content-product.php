<?php
/**
 * Archive product card (P06-T017). WooCommerce content-product template override.
 * Delegates to the theme's standard product card so the archive grid keeps the
 * exact reference DOM and the full data-product-* contract main.js filters on
 * (AC-01). Price / purchase mode / views come from real product data (AC-02);
 * grid/hover classes are preserved (AC-03). Inquiry products show the inquiry
 * CTA instead of a price (SC-02).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

global $product;

$es_id = isset( $args['product_id'] ) ? (int) $args['product_id'] : (int) get_the_ID();
if ( ! $es_id && is_object( $product ) && method_exists( $product, 'get_id' ) ) {
	$es_id = (int) $product->get_id();
}
if ( ! $es_id || 'product' !== get_post_type( $es_id ) ) {
	return;
}

get_template_part( 'template-parts/cards/card', 'product', array( 'product_id' => $es_id ) );
