<?php
/**
 * Emits a rendered archive shell (20 purchasable products, varied price) for the
 * P06-T021 JSDOM behaviour test. stdout = HTML only.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';
require ES_ROOT . '/temp/tests/woocommerce-shim.php';

$ids = array();
for ( $i = 1; $i <= 20; $i++ ) {
	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول ' . $i, 'post_name' => 't21-dom-' . $i ) );
	update_post_meta( $pid, 'true_sale', 'true' );
	update_post_meta( $pid, '_price', (string) ( $i * 100000 ) );
	update_post_meta( $pid, '_regular_price', (string) ( $i * 100000 ) );
	$ids[] = $pid;
}
$q = new WP_Query( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'post__in' => $ids, 'orderby' => 'post__in', 'no_found_rows' => true ) );
ob_start();
get_template_part( 'template-parts/archive/product', 'archive-shell', array( 'query' => $q, 'show_hero' => false, 'per_page' => 9 ) );
$html = ob_get_clean();
foreach ( $ids as $pid ) {
	wp_delete_post( $pid, true );
}
echo $html;
