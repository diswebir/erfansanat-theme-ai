<?php
/**
 * P03-T013 — متاباکس شناسه‌های صنعتی محصول
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ProductMetaboxes;

es_task( 'P03-T013', 'متاباکس شناسه‌های صنعتی محصول' );

es_check( 'target: inc/Admin/ProductMetaboxes.php', es_file( 'inc/Admin/ProductMetaboxes.php' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';
	require_once ABSPATH . 'wp-admin/includes/user.php';

	// WooCommerce is absent in QA: register a product stub (shim, D7).
	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	wp_set_current_user( 1 );
	$pid = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول صنعتی' ) );

	$keys = array(
		'gs1_id'        => '0012345678905',
		'national_code' => '0098765432',
		'hs_id'         => '94054090',
		'isic_id'       => '2740',
		'cpc_id'        => '46539',
		'product_id'    => 'ES-007',
	);

	// SC-01: each key round-trips with a valid nonce.
	$_POST['es_ids']                 = $keys;
	$_POST['es_product_ids_nonce']   = wp_create_nonce( 'es_product_ids' );
	ProductMetaboxes::save_ids( $pid );
	$all_ok = true;
	foreach ( $keys as $k => $v ) {
		$all_ok = $all_ok && ( get_post_meta( $pid, $k, true ) === $v );
	}
	es_check( 'SC-01: هر ۶ کلید round-trip می‌شود', $all_ok );

	// AC-02: leading zeros are preserved (text, not integer).
	es_check( 'AC-02: صفر پیشرو gs1_id حفظ می‌شود', '0012345678905' === get_post_meta( $pid, 'gs1_id', true ) );
	es_check( 'AC-02: صفر پیشرو national_code حفظ می‌شود', '0098765432' === get_post_meta( $pid, 'national_code', true ) );

	// AC-01: exact historical keys exist.
	es_check( 'AC-01: کلیدها دقیقاً ذخیره شده‌اند', '' !== get_post_meta( $pid, 'hs_id', true ) && '' !== get_post_meta( $pid, 'isic_id', true ) && '' !== get_post_meta( $pid, 'cpc_id', true ) );

	// AC-03: missing nonce → nothing changes.
	update_post_meta( $pid, 'gs1_id', 'KEEP' );
	unset( $_POST['es_product_ids_nonce'] );
	$_POST['es_ids'] = array( 'gs1_id' => 'HACKED' );
	ProductMetaboxes::save_ids( $pid );
	es_check( 'AC-03: بدون nonce ذخیره نمی‌شود', 'KEEP' === get_post_meta( $pid, 'gs1_id', true ) );

	// AC-03: capability guard in save() (subscriber cannot edit).
	$sub = wp_insert_user( array( 'user_login' => 'es_prod_sub_' . wp_rand( 100, 999 ), 'user_pass' => wp_generate_password( 16 ), 'role' => 'subscriber' ) );
	wp_set_current_user( $sub );
	$_POST['es_ids']               = array( 'gs1_id' => 'SUB-HACK' );
	$_POST['es_product_ids_nonce'] = wp_create_nonce( 'es_product_ids' );
	ProductMetaboxes::save( $pid, get_post( $pid ) );
	es_check( 'AC-03: کاربر بدون دسترسی ذخیره نمی‌کند', 'KEEP' === get_post_meta( $pid, 'gs1_id', true ) );
	wp_delete_user( $sub );
	wp_set_current_user( 1 );

	// SC-02: re-saving keeps the text format (quick-edit safe).
	$_POST['es_ids']               = array( 'gs1_id' => '0012345678905' );
	$_POST['es_product_ids_nonce'] = wp_create_nonce( 'es_product_ids' );
	ProductMetaboxes::save_ids( $pid );
	es_check( 'SC-02: ذخیره مجدد متن را خراب نمی‌کند', '0012345678905' === get_post_meta( $pid, 'gs1_id', true ) );

	// Metabox renders the six fields + nonce.
	ob_start();
	ProductMetaboxes::render_ids( get_post( $pid ) );
	$html = ob_get_clean();
	es_check( 'متاباکس: فیلد gs1_id رندر می‌شود', false !== strpos( $html, 'es_ids[gs1_id]' ) );
	es_check( 'متاباکس: nonce دارد', false !== strpos( $html, 'es_product_ids_nonce' ) );

	wp_delete_post( $pid, true );
	unset( $_POST['es_ids'], $_POST['es_product_ids_nonce'] );
	unregister_post_type( 'product' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
