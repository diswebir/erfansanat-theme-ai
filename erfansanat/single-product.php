<?php
/**
 * Single product page.
 *
 * Takes over from WooCommerce's own `single-product.php` so the product page is
 * the approved reference layout rather than the unstyled default. WooCommerce's
 * template loader finds this file before its own copy, which is why it lives at
 * the theme root rather than under `woocommerce/`. Everything
 * WooCommerce owns is still called through its own API: the add-to-cart form,
 * the notices, the structured data and the comment form. No cart logic is
 * reimplemented here.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\ProductData;

get_header( 'shop' );

/**
 * Notices and cart updates must stay outside the layout, exactly where
 * WooCommerce expects to inject them.
 */
do_action( 'woocommerce_before_single_product' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce's own hook, declared by the plugin.

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core template.
	get_footer( 'shop' );
	return;
}

while ( have_posts() ) :
	the_post();

	$erfansanat_product = wc_get_product( get_the_ID() );

	if ( ! $erfansanat_product instanceof \WC_Product ) {
		continue;
	}

	$erfansanat_gallery_ids = ProductData::gallery_ids( $erfansanat_product );
	$erfansanat_categories  = ProductData::categories( $erfansanat_product );
	$erfansanat_title       = $erfansanat_product->get_name();
	$erfansanat_badge       = array() !== $erfansanat_categories ? (string) $erfansanat_categories[0]->name : '';
	$erfansanat_ip          = ProductData::ip_rating( $erfansanat_product );
	$erfansanat_faq         = ProductData::faq( (int) $erfansanat_product->get_id() );
	$erfansanat_projects    = ProductData::related_projects( (int) $erfansanat_product->get_id(), 3 );
	?>
	<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'erfansanat-product', $erfansanat_product ); ?>>

		<?php
		include get_template_directory() . '/template-parts/product/part-header.php';
		?>

		<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
			<div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-10">
				<div class="lg:col-span-7">
					<?php include get_template_directory() . '/template-parts/product/part-gallery.php'; ?>
				</div>
				<div class="lg:col-span-5 lg:sticky lg:top-28">
					<?php include get_template_directory() . '/template-parts/product/part-summary.php'; ?>
				</div>
			</div>
		</section>

		<?php include get_template_directory() . '/template-parts/product/part-tabs.php'; ?>

		<?php
		// The related-projects rail reads its variable name from ProductData.
		$erfansanat_related_projects = $erfansanat_projects;
		$erfansanat_product_name     = $erfansanat_title;
		include get_template_directory() . '/template-parts/product/part-related-projects.php';

		include get_template_directory() . '/template-parts/product/part-faq.php';
		include get_template_directory() . '/template-parts/product/part-related-products.php';
		include get_template_directory() . '/template-parts/product/part-mobile-bar.php';
		?>

		<?php
		/**
		 * WooCommerce's structured data and any third-party product hook.
		 *
		 * Kept inside the product wrapper so a plugin that targets
		 * `#product-{id}` still finds it.
		 */
		do_action( 'woocommerce_after_single_product' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce's own hook, declared by the plugin.
		?>
	</div>
	<?php
endwhile;

get_footer( 'shop' );
