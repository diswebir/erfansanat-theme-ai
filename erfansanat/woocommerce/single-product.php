<?php
/**
 * Single product shell (P06-T002). Overrides WooCommerce's single-product
 * template with the reference layout. Standard Woo hooks are preserved
 * (AC-02): woocommerce_before_main_content / woocommerce_after_main_content
 * wrap the loop, and content-single-product.php fires the summary/gallery/
 * tabs hooks. The breadcrumb/summary/gallery/tabs order matches the mirror
 * (AC-01). Out-of-stock / invalid products never fatal (AC-03).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Hook: woocommerce_before_main_content.
 */
do_action( 'woocommerce_before_main_content' );

while ( have_posts() ) :
	the_post();
	// Load the theme's content-single-product.php (works with or without Woo).
	get_template_part( 'woocommerce/content', 'single-product' );
endwhile;

/**
 * Hook: woocommerce_after_main_content.
 */
do_action( 'woocommerce_after_main_content' );

get_footer();
