<?php
/** Cart cross-sells rendered through the shared WooCommerce product loop. */
/** @version 9.6.0 */

defined( 'ABSPATH' ) || exit;
if ( empty( $cross_sells ) || ! is_array( $cross_sells ) ) { wp_reset_postdata(); return; }
$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'محصولات مکمل و پیشنهادی', 'erfansanat-theme' ) );
?>
<section class="cross-sells mt-8 lg:col-span-7 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7"><h2 class="mb-5 text-lg font-extrabold text-slate-900"><?php echo esc_html( $heading ); ?></h2>
	<?php woocommerce_product_loop_start(); ?>
		<?php foreach ( $cross_sells as $cross_sell ) : if ( ! is_object( $cross_sell ) || ! method_exists( $cross_sell, 'get_id' ) ) { continue; } $post_object = get_post( $cross_sell->get_id() ); if ( ! $post_object ) { continue; } setup_postdata( $GLOBALS['post'] = $post_object ); wc_get_template_part( 'content', 'product' ); endforeach; ?>
	<?php woocommerce_product_loop_end(); ?>
</section>
<?php wp_reset_postdata(); ?>
