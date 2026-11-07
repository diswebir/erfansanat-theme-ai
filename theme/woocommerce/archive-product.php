<?php
/** Classic WooCommerce shop and product-taxonomy archive template. */
/** @version 8.6.0 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
do_action( 'woocommerce_before_main_content' );
$queried_term = function_exists( 'get_queried_object' ) ? get_queried_object() : null;
get_template_part( 'template-parts/archive/hero', null, array( 'term' => $queried_term ) );
?>
<main id="es-product-archive">
	<?php do_action( 'woocommerce_before_shop_loop' ); ?>
	<?php get_template_part( 'template-parts/archive/grid', null, array( 'term' => $queried_term ) ); ?>
	<?php do_action( 'woocommerce_after_shop_loop' ); ?>
	<?php if ( is_object( $queried_term ) && 'product_cat' === ( $queried_term->taxonomy ?? '' ) && ( ! class_exists( '\\ErfanSanat\\Theme\\Frontend\\ArchiveConfig' ) || \ErfanSanat\Theme\Frontend\ArchiveConfig::show_faq() ) ) : ?>
		<?php get_template_part( 'template-parts/archive/product-cat-faq', null, array( 'term' => $queried_term ) ); ?>
	<?php endif; ?>
</main>
<?php
do_action( 'woocommerce_after_main_content' );
do_action( 'woocommerce_sidebar' );
get_footer( 'shop' );
