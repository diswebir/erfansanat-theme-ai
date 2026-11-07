<?php
/** Render the native WooCommerce review flow through WordPress comments. */

defined( 'ABSPATH' ) || exit;
$product = \ErfanSanat\Theme\Woo\ProductTemplate::product( $args['product'] ?? null );
if ( ! $product || ! function_exists( 'comments_template' ) ) { return; }
comments_template( '/woocommerce/single-product-reviews.php' );
