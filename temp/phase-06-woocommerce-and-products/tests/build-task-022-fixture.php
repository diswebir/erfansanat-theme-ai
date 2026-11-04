<?php
/**
 * Emits a rendered cart page (2 items) for the P06-T022 JSDOM behaviour test.
 * stdout = HTML only.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';
require ES_ROOT . '/temp/tests/woocommerce-shim.php';

$p1 = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'ریسه بلوطی', 'post_name' => 'cart-dom-p1' ) );
$p2 = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'پوینت لایت', 'post_name' => 'cart-dom-p2' ) );
update_post_meta( $p1, '_price', '100000' );
update_post_meta( $p1, 'true_sale', 'true' );
update_post_meta( $p1, '_min_qty', '5' );
update_post_meta( $p1, '_step_qty', '5' );
update_post_meta( $p2, '_price', '200000' );
update_post_meta( $p2, 'true_sale', 'true' );
WC()->cart->add_to_cart( $p1, 5 );
WC()->cart->add_to_cart( $p2, 2 );

ob_start();
include ES_THEME . '/woocommerce/cart/cart.php';
$html = ob_get_clean();

wp_delete_post( $p1, true );
wp_delete_post( $p2, true );
echo $html;
