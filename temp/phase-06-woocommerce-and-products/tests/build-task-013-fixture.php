<?php
/**
 * Emits a rendered product FAQ accordion for the JSDOM behaviour test.
 * stdout = HTML only.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';
require ES_ROOT . '/temp/tests/woocommerce-shim.php';

$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'درخت نوری دلسا', 'post_name' => 'faq-dom' ) );
$faq = array(
	'item-0' => array( 'name_question' => 'شرایط گارانتی چگونه است؟', 'answer_question' => '۲۴ ماه گارانتی تعویض.' ),
	'item-1' => array( 'name_question' => 'امکان سفارشی‌سازی هست؟', 'answer_question' => 'بله؛ تولیدکننده مستقیم هستیم.' ),
	'item-2' => array( 'name_question' => 'زمان تحویل چقدر است؟', 'answer_question' => '۲ تا ۷ روز کاری.' ),
);
update_post_meta( $pid, 'faq', json_encode( $faq, JSON_UNESCAPED_UNICODE ) );

$GLOBALS['post'] = get_post( $pid );
setup_postdata( $GLOBALS['post'] );
$product = wc_get_product( $pid );
ob_start();
include ES_THEME . '/template-parts/product/faq.php';
$html = ob_get_clean();

wp_delete_post( $pid, true );
echo $html;
