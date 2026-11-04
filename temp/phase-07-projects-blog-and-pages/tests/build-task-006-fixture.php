<?php
/**
 * Emits a projects archive (8 projects, per-page 6) for the P07-T006 JSDOM test.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';

$ids = array();
for ( $i = 1; $i <= 8; $i++ ) {
	$pid = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه ' . $i, 'post_name' => 'arch-dom-' . $i . '-' . uniqid() ) );
	update_post_meta( $pid, 'number2-viewc', (string) ( $i * 10 ) );
	$ids[] = $pid;
}
add_filter( 'es_projects_archive_query_args', static function ( $args ) use ( $ids ) {
	$args['post__in'] = $ids;
	$args['orderby']  = 'post__in';
	return $args;
} );

ob_start();
include ES_THEME . '/archive-projects.php';
$html = ob_get_clean();

foreach ( $ids as $pid ) {
	wp_delete_post( $pid, true );
}
echo $html;
