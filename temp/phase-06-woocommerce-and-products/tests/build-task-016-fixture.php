<?php
/**
 * Emits a rendered product-archive shell (12 products, varied price/mode/views)
 * for the JSDOM behaviour test. stdout = HTML only.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';
require ES_ROOT . '/temp/tests/woocommerce-shim.php';

$ids = array();
for ( $i = 1; $i <= 12; $i++ ) {
	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول ' . $i, 'post_name' => 'arch-prod-' . $i ) );
	// Alternate purchase mode; varied price and views.
	update_post_meta( $pid, 'true_sale', ( $i % 2 === 0 ) ? 'true' : 'false' );
	update_post_meta( $pid, '_price', (string) ( $i * 100000 ) );
	update_post_meta( $pid, '_regular_price', (string) ( $i * 100000 ) );
	update_post_meta( $pid, '_wc_average_rating', (string) ( ( $i % 5 ) + 1 ) );
	update_post_meta( $pid, '_es_views', (string) ( $i * 10 ) );
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
