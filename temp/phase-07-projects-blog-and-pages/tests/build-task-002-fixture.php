<?php
/**
 * Emits a project gallery (4 images) for the P07-T002 JSDOM lightbox test.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';

$pid = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه گالری DOM', 'post_name' => 'gallery-dom-' . uniqid() ) );
$imgs = array();
for ( $i = 0; $i < 4; $i++ ) {
	$imgs[ $i ] = (int) wp_insert_attachment( array( 'post_mime_type' => 'image/jpeg', 'post_title' => 'g' . $i, 'post_status' => 'inherit' ), 'gallery-dom/img-' . $i . '-' . uniqid() . '.jpg' );
}
set_post_thumbnail( $pid, $imgs[0] );
update_post_meta( $pid, 'second-image', $imgs[1] );
update_post_meta( $pid, 'three-image', $imgs[2] );
update_post_meta( $pid, 'foure-image', $imgs[3] );

ob_start();
echo '<!doctype html><html><body>';
get_template_part( 'template-parts/project/gallery', null, array( 'project_id' => $pid ) );
echo '</body></html>';
$html = ob_get_clean();

wp_delete_post( $pid, true );
foreach ( $imgs as $aid ) {
	wp_delete_attachment( $aid, true );
}
echo $html;
