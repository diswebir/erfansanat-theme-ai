<?php
/**
 * Emits a rendered checkout page for the P06-T024 JSDOM buyer-type test.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';
require ES_ROOT . '/temp/tests/woocommerce-shim.php';

$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول', 'post_name' => 'checkout-dom-p1' ) );
update_post_meta( $pid, '_price', '250000' );
update_post_meta( $pid, 'true_sale', 'true' );
WC()->cart->add_to_cart( $pid, 1 );

ob_start();
include ES_THEME . '/woocommerce/checkout/form-checkout.php';
$html = ob_get_clean();

wp_delete_post( $pid, true );
echo $html;
