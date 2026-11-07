<?php
/** Classic single product template assembled from the theme's Woo-compatible parts. */
/** @version 1.6.4 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
do_action( 'woocommerce_before_main_content' );

while ( have_posts() ) :
	the_post();
	global $product;
	$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	do_action( 'woocommerce_before_single_product' );
	if ( post_password_required() ) :
		echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress returns the password form markup.
	elseif ( is_object( $product ) ) :
		?>
		<main id="es-product-page" class="bg-white" data-product-page="true">
			<?php get_template_part( 'template-parts/product/hero', null, array( 'product' => $product ) ); ?>
			<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
				<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
				<div class="grid items-start gap-8 lg:grid-cols-3 lg:gap-10">
					<div class="space-y-4 lg:col-span-7"><?php get_template_part( 'template-parts/product/gallery', null, array( 'product' => $product ) ); do_action( 'woocommerce_product_thumbnails' ); ?></div>
					<div class="lg:col-span-5"><?php get_template_part( 'template-parts/product/purchase-box', null, array( 'product' => $product ) ); do_action( 'woocommerce_single_product_summary' ); ?></div>
				</div>
			</section>
			<?php get_template_part( 'template-parts/product/tabs', null, array( 'product' => $product ) ); ?>
			<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
			<?php get_template_part( 'template-parts/product/faq', null, array( 'product' => $product ) ); ?>
			<?php get_template_part( 'template-parts/product/related', null, array( 'product' => $product ) ); ?>
			<?php get_template_part( 'template-parts/product/mobile-purchase', null, array( 'product' => $product ) ); ?>
		</main>
		<?php
	endif;
	do_action( 'woocommerce_after_single_product' );
endwhile;

do_action( 'woocommerce_after_main_content' );
do_action( 'woocommerce_sidebar' );
get_footer( 'shop' );
