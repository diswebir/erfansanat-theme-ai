<?php
/**
 * P03-T015 — متاباکس کاتالوگ و گالری محصول
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ProductMetaboxes;

es_task( 'P03-T015', 'متاباکس کاتالوگ و گالری محصول' );

es_check( 'target: inc/Admin/ProductMetaboxes.php', es_file( 'inc/Admin/ProductMetaboxes.php' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';

	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	wp_set_current_user( 1 );
	$pid   = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول گالری' ) );
	$nonce = wp_create_nonce( 'es_product_files' );

	// AC-01: catalog file stored as URL (from a media ID).
	$att = wp_insert_attachment( array( 'post_title' => 'کاتالوگ', 'post_status' => 'inherit', 'post_mime_type' => 'application/pdf' ), 'catalog-t015.pdf' );
	update_post_meta( $att, '_wp_attached_file', 'catalog-t015.pdf' );
	$_POST['es_files']               = array( 'product-catalog-file' => (string) $att );
	$_POST['es_product_files_nonce'] = $nonce;
	ProductMetaboxes::save_files( $pid );
	$catalog = get_post_meta( $pid, 'product-catalog-file', true );
	es_check( 'AC-01: کاتالوگ به‌صورت URL ذخیره می‌شود', is_string( $catalog ) && false !== strpos( $catalog, 'catalog-t015.pdf' ) && 0 === strpos( $catalog, 'http' ) );

	// AC-01: a direct URL is also accepted.
	$_POST['es_files'] = array( 'product-catalog-file' => 'https://erfansanat.com/cat.pdf' );
	ProductMetaboxes::save_files( $pid );
	es_check( 'AC-01: URL مستقیم کاتالوگ پذیرفته می‌شود', 'https://erfansanat.com/cat.pdf' === get_post_meta( $pid, 'product-catalog-file', true ) );

	// AC-02 + AC-04: gallery is a comma ID list, order preserved, synced to standard.
	$_POST['es_files'] = array( 'galerry_produ' => '30,10,20' );
	ProductMetaboxes::save_files( $pid );
	es_check( 'AC-02: galerry_produ لیست ID کامادار', '30,10,20' === get_post_meta( $pid, 'galerry_produ', true ) );
	es_check( 'AC-04: ترتیب گالری حفظ می‌شود', array( 30, 10, 20 ) === ProductMetaboxes::get_gallery( $pid ) );
	es_check( 'AC-03: _product_image_gallery همگام می‌شود', '30,10,20' === get_post_meta( $pid, '_product_image_gallery', true ) );

	// AC-03: fallback read order. Fresh product with only legacy keys.
	// Clear $_POST so creating new products doesn't trigger the save hook.
	unset( $_POST['es_files'], $_POST['es_product_files_nonce'] );
	$p2 = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول legacy' ) );
	update_post_meta( $p2, 'gallery-filed', '5,6' );
	es_check( 'AC-03: fallback gallery-filed خوانده می‌شود', array( 5, 6 ) === ProductMetaboxes::get_gallery( $p2 ) );
	update_post_meta( $p2, '_product_image_gallery', '7,8' );
	es_check( 'AC-03: _product_image_gallery بر gallery-filed مقدم است', array( 7, 8 ) === ProductMetaboxes::get_gallery( $p2 ) );
	$p3 = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول image-N' ) );
	update_post_meta( $p3, 'image-2', 11 );
	update_post_meta( $p3, 'image-4', 14 );
	es_check( 'AC-03: fallback image-2..6 خوانده می‌شود', array( 11, 14 ) === ProductMetaboxes::get_gallery( $p3 ) );

	// SC-01: legacy gallery read then saved without destroying legacy keys.
	update_post_meta( $p2, 'image-2', 7 );
	update_post_meta( $p2, 'image-3', 8 );
	$_POST['es_files']               = array( 'galerry_produ' => ProductMetaboxes::get_gallery( $p2 ) );
	$_POST['es_product_files_nonce'] = $nonce;
	ProductMetaboxes::save_files( $p2 );
	es_check( 'SC-01: گالری legacy بدون تخریب ذخیره می‌شود', '7,8' === get_post_meta( $p2, 'galerry_produ', true ) );
	es_check( 'SC-01: کلید legacy موجود همگام می‌شود', '7' === (string) get_post_meta( $p2, 'image-2', true ) );

	// SC-02: removing an image fixes the synced keys.
	$_POST['es_files'] = array( 'galerry_produ' => '30,20' ); // remove 10
	ProductMetaboxes::save_files( $pid );
	es_check( 'SC-02: حذف تصویر galerry_produ را اصلاح می‌کند', '30,20' === get_post_meta( $pid, 'galerry_produ', true ) );
	es_check( 'SC-02: حذف تصویر کلید استاندارد را همگام می‌کند', '30,20' === get_post_meta( $pid, '_product_image_gallery', true ) );

	// AC-03: nonce guard.
	unset( $_POST['es_product_files_nonce'] );
	$_POST['es_files'] = array( 'galerry_produ' => '999' );
	ProductMetaboxes::save_files( $pid );
	es_check( 'AC-03: بدون nonce ذخیره نمی‌شود', '30,20' === get_post_meta( $pid, 'galerry_produ', true ) );

	// Metabox renders catalog + gallery fields.
	ob_start();
	ProductMetaboxes::render_files( get_post( $pid ) );
	$html = ob_get_clean();
	es_check( 'متاباکس: فیلد کاتالوگ رندر می‌شود', false !== strpos( $html, 'es_files[product-catalog-file]' ) );
	es_check( 'متاباکس: فیلد گالری رندر می‌شود', false !== strpos( $html, 'es_files[galerry_produ]' ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $p2, true );
	wp_delete_post( $p3, true );
	wp_delete_attachment( $att, true );
	unset( $_POST['es_files'], $_POST['es_product_files_nonce'] );
	unregister_post_type( 'product' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
