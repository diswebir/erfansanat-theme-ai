<?php
/**
 * Real WordPress shop archive fixture for P08-T006+ JSDOM tests.
 * Usage: php build-shop-fixture.php [url-query]
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';
if ( ! es_load_wp() ) { exit( 1 ); }
require_once ES_THEME . '/functions.php';
require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';
\ErfanSanat\Theme\Core\AttributeMap::register_missing();

// Deterministic mini shop: two categories, three products.
$cats = array();
foreach ( array( 'ریشه‌ها T006DOM', 'درختان T006DOM' ) as $name ) {
	$t = wp_insert_term( $name, 'product_cat' );
	$cats[] = is_array( $t ) ? (int) $t['term_id'] : 0;
}
$ids = array();
foreach ( array( array( 'ریسه سوزنی DOM', 0, '100000', 'true' ), array( 'ریسه بلوطی DOM', 0, '900000', 'false' ), array( 'درخت نوری DOM', 1, '5000000', 'true' ) ) as $def ) {
	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => $def[0] ) );
	if ( $cats[ $def[1] ] ) { wp_set_object_terms( $pid, array( $cats[ $def[1] ] ), 'product_cat' ); }
	update_post_meta( $pid, '_price', $def[2] );
	update_post_meta( $pid, 'true_sale', $def[3] );
	$ids[] = $pid;
}

$q = new WP_Query( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 20 ) );
$args = array( 'query' => $q, 'show_hero' => false );
ob_start();
include ES_THEME . '/template-parts/archive/product-archive-shell.php';
$html = ob_get_clean();

// Posts stay in the shared SQLite DB so the AJAX bridge can query them;
// the DOM test removes them afterwards via the bridge cleanup mode.
$meta = '<script type="application/json" id="es-fixture-meta">' . wp_json_encode( array( 'cats' => $cats, 'ids' => $ids, 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ) ) ) . '</script>';
echo '<!doctype html><html><head>' . $meta . '</head><body>' . $html . '</body></html>';
