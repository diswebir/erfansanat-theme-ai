<?php
/**
 * P03-T017 — Repeater مشخصات محصول
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ProductMetaboxes;
use ErfanSanat\Theme\Core\LegacyMeta;

es_task( 'P03-T017', 'Repeater مشخصات محصول' );

es_check( 'target: inc/Admin/ProductMetaboxes.php', es_file( 'inc/Admin/ProductMetaboxes.php' ) );
es_check( 'AC-02: املای vaule_of_variable در سورس دقیق است', es_contains( 'inc/Admin/ProductMetaboxes.php', 'vaule_of_variable' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';

	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	wp_set_current_user( 1 );
	unset( $_POST['es_features'], $_POST['es_product_features_nonce'] );
	$pid   = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول مشخصات' ) );
	$nonce = wp_create_nonce( 'es_product_features' );

	// AC-01/03: existing JetEngine item-0 JSON is read and stays item-0 on save.
	update_post_meta( $pid, 'product-feature', wp_slash( '{"item-0":{"add-new-feature":"الف"},"item-1":{"add-new-feature":"ب"},"item-2":{"add-new-feature":"ج"}}' ) );
	es_check( 'AC-01: product-feature موجود خوانده می‌شود', 3 === count( LegacyMeta::get( $pid, 'product-feature' ) ) );

	// SC-01: remove the middle item, order of the rest survives.
	$_POST['es_features'] = array(
		'product-feature' => array(
			array( 'add-new-feature' => 'الف' ),
			array( 'add-new-feature' => 'ج' ),
		),
	);
	$_POST['es_product_features_nonce'] = $nonce;
	ProductMetaboxes::save_features( $pid );
	$items = LegacyMeta::get( $pid, 'product-feature' );
	es_check( 'SC-01: حذف آیتم وسط درست ذخیره می‌شود', array( 'الف', 'ج' ) === array_column( $items, 'add-new-feature' ) );
	$raw = get_post_meta( $pid, 'product-feature', true );
	es_check( 'AC-03: فرمت item-0 حفظ و بازفهرست می‌شود', is_string( $raw ) && false !== strpos( $raw, 'item-0' ) && false !== strpos( $raw, 'item-1' ) && false === strpos( $raw, 'item-2' ) );

	// SC-01: add an item in the middle.
	$_POST['es_features'] = array(
		'product-feature' => array(
			array( 'add-new-feature' => 'الف' ),
			array( 'add-new-feature' => 'وسط' ),
			array( 'add-new-feature' => 'ج' ),
		),
	);
	ProductMetaboxes::save_features( $pid );
	es_check( 'SC-01: افزودن آیتم وسط ترتیب را حفظ می‌کند', array( 'الف', 'وسط', 'ج' ) === array_column( LegacyMeta::get( $pid, 'product-feature' ), 'add-new-feature' ) );

	// AC-02 / SC-02: product-feature2_copy keeps both historical subkeys.
	$_POST['es_features'] = array(
		'product-feature2_copy' => array(
			array( 'add-new-feature' => 'توان', 'vaule_of_variable' => '۶۰ وات' ),
			array( 'add-new-feature' => 'رنگ', 'vaule_of_variable' => 'آفتابی' ),
		),
	);
	ProductMetaboxes::save_features( $pid );
	$copy = LegacyMeta::get( $pid, 'product-feature2_copy' );
	es_check( 'AC-02: هر دو زیرکلید تاریخی ذخیره می‌شوند', 'توان' === $copy[0]['add-new-feature'] && '۶۰ وات' === $copy[0]['vaule_of_variable'] );
	$raw_copy = get_post_meta( $pid, 'product-feature2_copy', true );
	$raw_str  = is_array( $raw_copy ) ? wp_json_encode( $raw_copy ) : (string) $raw_copy;
	es_check( 'SC-02: املای vaule_of_variable تغییر نمی‌کند', false !== strpos( $raw_str, 'vaule_of_variable' ) );
	es_check( 'SC-02: کلید با املای اصلاح‌شده ساخته نمی‌شود', '' === get_post_meta( $pid, 'value_of_variable', true ) );

	// Completely empty rows are dropped; rows with content stay.
	$_POST['es_features'] = array(
		'product-feature' => array(
			array( 'add-new-feature' => 'الف' ),
			array( 'add-new-feature' => '' ),
			array( 'add-new-feature' => 'ج' ),
		),
	);
	ProductMetaboxes::save_features( $pid );
	es_check( 'ردیف کاملاً خالی حذف می‌شود', array( 'الف', 'ج' ) === array_column( LegacyMeta::get( $pid, 'product-feature' ), 'add-new-feature' ) );

	// Nonce guard.
	unset( $_POST['es_product_features_nonce'] );
	$_POST['es_features'] = array( 'product-feature' => array( array( 'add-new-feature' => 'HACK' ) ) );
	ProductMetaboxes::save_features( $pid );
	es_check( 'بدون nonce ذخیره نمی‌شود', array( 'الف', 'ج' ) === array_column( LegacyMeta::get( $pid, 'product-feature' ), 'add-new-feature' ) );

	// Metabox renders both repeaters with the exact subkey names.
	ob_start();
	ProductMetaboxes::render_features( get_post( $pid ) );
	$html = ob_get_clean();
	es_check( 'متاباکس: product-feature رندر می‌شود', false !== strpos( $html, 'es_features[product-feature]' ) );
	es_check( 'متاباکس: زیرکلید vaule_of_variable رندر می‌شود', false !== strpos( $html, 'vaule_of_variable' ) );

	wp_delete_post( $pid, true );
	unset( $_POST['es_features'], $_POST['es_product_features_nonce'] );
	unregister_post_type( 'product' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
