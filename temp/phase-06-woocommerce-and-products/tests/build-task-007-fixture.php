<?php
/**
 * Emits a rendered PDP purchase card (variable product, no-Woo fallback) for
 * the JSDOM behaviour test. stdout = HTML only.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\AttributeMap;

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';
require ES_ROOT . '/temp/tests/woocommerce-shim.php';
AttributeMap::register_missing();

$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'ریسه فندقی', 'post_name' => 'fandoghi-string' ) );
update_post_meta( $pid, '_product_type', 'variable' );
update_post_meta( $pid, 'true_sale', 'true' );
update_post_meta( $pid, '_price', '100000' );
update_post_meta( $pid, '_regular_price', '100000' );
update_post_meta( $pid, '_stock_status', 'instock' );
update_post_meta( $pid, '_sku', 'ESE-FAN-1' );
update_post_meta( $pid, '_min_qty', '5' );
update_post_meta( $pid, '_max_qty', '20' );
update_post_meta( $pid, '_step_qty', '5' );
update_post_meta( $pid, '_unit', 'شاخه' );
$green = wp_insert_term( 'سبز', 'pa_color', array( 'slug' => 'green' ) );
$red   = wp_insert_term( 'قرمز', 'pa_color', array( 'slug' => 'red' ) );
$s10   = wp_insert_term( '10m', 'pa_size', array( 'slug' => '10m' ) );
$s20   = wp_insert_term( '20m', 'pa_size', array( 'slug' => '20m' ) );
wp_set_object_terms( $pid, array( (int) $green['term_id'], (int) $red['term_id'] ), 'pa_color' );
wp_set_object_terms( $pid, array( (int) $s10['term_id'], (int) $s20['term_id'] ), 'pa_size' );

$GLOBALS['post'] = get_post( $pid );
setup_postdata( $GLOBALS['post'] );
ob_start();
include ES_THEME . '/template-parts/product/purchase.php';
$html = ob_get_clean();

wp_delete_post( $pid, true );
wp_delete_term( (int) $green['term_id'], 'pa_color' );
wp_delete_term( (int) $red['term_id'], 'pa_color' );
wp_delete_term( (int) $s10['term_id'], 'pa_size' );
wp_delete_term( (int) $s20['term_id'], 'pa_size' );

echo $html;
