<?php
/**
 * P06-T023 — مودال سبد و fragmentهای واقعی Woo
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-023.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T023', 'مودال سبد و fragmentهای واقعی Woo' );

es_check( 'target: CartFragments.php', es_file( 'inc/Core/CartFragments.php', ES_THEME ) );
es_check( 'target: cart-modal.php', es_file( 'template-parts/header/cart-modal.php', ES_THEME ) );
es_check( 'AC-02: فیلتر fragment استاندارد Woo', es_contains( 'inc/Core/CartFragments.php', "woocommerce_add_to_cart_fragments" ) );
es_check( 'AC-01: endpointهای AJAX add/remove/update', es_contains( 'inc/Core/CartFragments.php', 'wp_ajax_es_cart_' ) && es_contains( 'inc/Core/CartFragments.php', 'wp_ajax_nopriv_es_cart_' ) );
es_check( 'AC-02: nonce محافظ', es_contains( 'inc/Core/CartFragments.php', 'wp_verify_nonce' ) );
es_check( 'AC-03: مودال از سبد واقعی + data-cart-real', es_contains( 'template-parts/header/cart-modal.php', 'get_cart_contents_count' ) && es_contains( 'template-parts/header/cart-modal.php', 'data-cart-real="true"' ) );
es_check( 'AC-03: localStorage جای سبد واقعی را نمی‌گیرد (realCart config)', es_contains( 'inc/Core/CartFragments.php', "'realCart'=> true" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	es_check( 'AC-02: فیلتر fragment ثبت شد', false !== has_filter( 'woocommerce_add_to_cart_fragments', 'ErfanSanat\\Theme\\Core\\CartFragments::add_to_cart_fragments' ) );
	es_check( 'AC-01: اکشن‌های AJAX ثبت شدند', false !== has_action( 'wp_ajax_es_cart_add' ) && false !== has_action( 'wp_ajax_nopriv_es_cart_add' ) && false !== has_action( 'wp_ajax_es_cart_remove' ) && false !== has_action( 'wp_ajax_es_cart_update' ) );

	$FQCN = 'ErfanSanat\\Theme\\Core\\CartFragments';

	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول سبد', 'post_name' => 'frag-p1' ) );
	update_post_meta( $pid, '_price', '150000' );
	update_post_meta( $pid, 'true_sale', 'true' );
	$inq = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'استعلامی', 'post_name' => 'frag-inq' ) );
	update_post_meta( $inq, 'true_sale', 'false' );

	$cart = WC()->cart;
	$cart->empty_cart();

	// SC-01: add updates badge + modal count (pure logic).
	$res = $FQCN::add_item( $pid, 3 );
	es_check( 'SC-01: افزودن موفق', ! empty( $res['success'] ) );
	es_check( 'SC-01: شمارنده = ۳', isset( $res['count'] ) && 3 === $res['count'] );
	es_check( 'SC-01: fragment badge به‌روز شد', isset( $res['fragments']['span[data-header-cart-badge="true"]'] ) && false !== strpos( $res['fragments']['span[data-header-cart-badge="true"]'], number_format_i18n( 3 ) ) );
	es_check( 'SC-01: fragment شمارنده مودال به‌روز شد', isset( $res['fragments']['span[data-cart-modal-count="true"]'] ) && false !== strpos( $res['fragments']['span[data-cart-modal-count="true"]'], number_format_i18n( 3 ) . ' کالا' ) );

	// AC-02: nonce verification.
	es_check( 'AC-02: nonce معتبر تأیید می‌شود', true === $FQCN::verify_nonce_string( wp_create_nonce( $FQCN::NONCE_ACTION ) ) );
	es_check( 'AC-02: nonce نامعتبر رد می‌شود', false === $FQCN::verify_nonce_string( 'bad_nonce' ) );

	// AC-03: inquiry product never enters real cart.
	$res_inq = $FQCN::add_item( $inq, 1 );
	es_check( 'AC-03: محصول استعلامی وارد سبد واقعی نمی‌شود', empty( $res_inq['success'] ) && 'inquiry_only' === $res_inq['message'] );
	es_check( 'AC-03: سبد همچنان ۳ قلم دارد', 3 === $cart->get_cart_contents_count() );

	// SC-02: remove zeroes the cart.
	$key     = array_key_first( $cart->get_cart() );
	$res_rem = $FQCN::remove_item( $key );
	es_check( 'SC-02: حذف شمارنده را صفر می‌کند', isset( $res_rem['count'] ) && 0 === $res_rem['count'] );
	es_check( 'SC-02: جمع مودال صفر شد', false !== strpos( $res_rem['fragments']['div[data-cart-modal-total="true"]'], '0' ) );

	// update endpoint.
	$k2      = $cart->add_to_cart( $pid, 2 );
	$res_upd = $FQCN::update_item( $k2, 5 );
	es_check( 'AC-01: به‌روزرسانی تعداد → ۵', isset( $res_upd['count'] ) && 5 === $res_upd['count'] );

	// fragments filter returns the three real-cart selectors.
	$frags = $FQCN::add_to_cart_fragments( array() );
	es_check( 'AC-02: سه fragment واقعی (badge/count/total)', isset( $frags['span[data-header-cart-badge="true"]'], $frags['span[data-cart-modal-count="true"]'], $frags['div[data-cart-modal-total="true"]'] ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $inq, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
