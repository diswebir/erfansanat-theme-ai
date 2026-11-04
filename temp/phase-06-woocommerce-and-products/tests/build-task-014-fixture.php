<?php
/**
 * Emits a rendered product reviews panel (open comments, one existing review)
 * for the JSDOM behaviour test. stdout = HTML only.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';
require ES_ROOT . '/temp/tests/woocommerce-shim.php';

$pid = (int) wp_insert_post( array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'post_title'     => 'درخت نوری دلسا',
	'post_name'      => 'reviews-dom',
	'comment_status' => 'open',
) );
$cid = (int) wp_insert_comment( array(
	'comment_post_ID'      => $pid,
	'comment_author'       => 'مهندس رضایی',
	'comment_content'      => 'کیفیت عالی بود.',
	'comment_approved'     => 1,
	'comment_type'         => 'review',
	'comment_author_email' => 'test@example.com',
) );
add_comment_meta( $cid, 'rating', 5 );

$GLOBALS['post'] = get_post( $pid );
setup_postdata( $GLOBALS['post'] );
$product = wc_get_product( $pid );
ob_start();
include ES_THEME . '/template-parts/product/reviews.php';
$html = ob_get_clean();

wp_delete_comment( $cid, true );
wp_delete_post( $pid, true );
echo $html;
