<?php
/**
 * Real WordPress projects archive fixture for P08-T010+ JSDOM tests.
 * Usage: php build-projects-fixture.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';
if ( ! es_load_wp() ) { exit( 1 ); }
require_once ES_THEME . '/functions.php';
require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';

// Deterministic mini portfolio: 2 types × 2 cities × 4 projects.
$mk = static function ( $tax, $name ) {
	$found = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false, 'name' => $name, 'fields' => 'ids' ) );
	if ( is_array( $found ) && $found ) { return (int) reset( $found ); }
	$t = wp_insert_term( $name, $tax );
	return is_array( $t ) ? (int) $t['term_id'] : 0;
};
$type_a = $mk( 'project_type', 'نورپردازی T010DOM' );
$type_b = $mk( 'project_type', 'المان شهری T010DOM' );
$isf    = $mk( 'city', 'اصفهان T010DOM' );
$kh     = $mk( 'city', 'خمینی‌شهر T010DOM' );

$ids = array();
foreach ( array( // title, type, city
	array( 'میدان نوری DOM', $type_a, $isf ),
	array( 'تونل نوری DOM', $type_a, $kh ),
	array( 'المان ورودی DOM', $type_b, $isf ),
	array( 'نمایش پل DOM', $type_b, $kh ),
) as $row ) {
	$pid = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => $row[0] ) );
	if ( $row[1] ) { wp_set_object_terms( $pid, array( $row[1] ), 'project_type' ); }
	if ( $row[2] ) { wp_set_object_terms( $pid, array( $row[2] ), 'city' ); }
	$ids[] = $pid;
}

$args = array( 'query' => new WP_Query( array( 'post_type' => 'projects', 'post_status' => 'publish', 'posts_per_page' => 20 ) ), 'per_page' => 6 );
ob_start();
include ES_THEME . '/template-parts/archive/project-archive-shell.php';
$html = ob_get_clean();

// Posts stay in the shared SQLite DB so the AJAX bridge can query them; the
// DOM test removes them via the bridge cleanup mode.
$meta = '<script type="application/json" id="es-fixture-meta">' . wp_json_encode( array(
	'ids' => $ids,
	'type_a' => $type_a, 'type_b' => $type_b,
	'isf' => $isf, 'kh' => $kh,
	'nonce' => wp_create_nonce( 'es_ajax_project_filters' ),
) ) . '</script>';
echo '<!doctype html><html><head>' . $meta . '</head><body>' . $html . '</body></html>';
