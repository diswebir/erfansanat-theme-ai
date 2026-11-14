<?php
/**
 * P03-T019 — Quick Edit و Bulk Edit محصول
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ProductQuickEdit;

es_task( 'P03-T019', 'Quick Edit و Bulk Edit محصول' );

es_check( 'target: inc/Admin/ProductQuickEdit.php', es_file( 'inc/Admin/ProductQuickEdit.php' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';
	require_once ABSPATH . 'wp-admin/includes/user.php';

	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	wp_set_current_user( 1 );
	unset( $_POST['es_qe'], $_POST['action'], $_POST['bulk_edit'], $_POST['_inline_edit'], $_POST['_wpnonce'] );
	$p1 = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول یک' ) );
	$p2 = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول دو' ) );
	update_post_meta( $p1, 'zireonvan', 'زیرعنوان یک' );
	update_post_meta( $p2, 'zireonvan', 'زیرعنوان دو' );
	update_post_meta( $p1, 'gs1_id', '00111' );
	update_post_meta( $p2, 'gs1_id', '00222' );

	// SC-01: Quick Edit one product saves the allowed keys.
	$_POST['action']       = 'inline-save';
	$_POST['_inline_edit'] = wp_create_nonce( 'inlineeditnonce' );
	$_POST['es_qe']        = array( 'gs1_id' => '00999', 'true_sale' => 'true', 'evil_key' => 'hack' );
	ProductQuickEdit::save( $p1, get_post( $p1 ) );
	es_check( 'SC-01: Quick Edit مقدار را ذخیره می‌کند', '00999' === get_post_meta( $p1, 'gs1_id', true ) && 'true' === get_post_meta( $p1, 'true_sale', true ) );
	es_check( 'AC-01: کلید غیرمجاز ویرایش نمی‌شود', '' === get_post_meta( $p1, 'evil_key', true ) );

	// SC-02 + AC-02: Bulk Edit two products; empty value does not clear; other meta intact.
	unset( $_POST['action'], $_POST['_inline_edit'] );
	$_POST['bulk_edit'] = 'Update';
	$_POST['_wpnonce']  = wp_create_nonce( 'bulk-posts' );
	$_POST['es_qe']     = array( 'true_sale' => 'false', 'gs1_id' => '' );
	ProductQuickEdit::save( $p1, get_post( $p1 ) );
	ProductQuickEdit::save( $p2, get_post( $p2 ) );
	es_check( 'SC-02: Bulk وضعیت فروش هر دو را عوض می‌کند', 'false' === get_post_meta( $p1, 'true_sale', true ) && 'false' === get_post_meta( $p2, 'true_sale', true ) );
	es_check( 'AC-02: bulk خالی gs1_id را پاک نمی‌کند', '00999' === get_post_meta( $p1, 'gs1_id', true ) && '00222' === get_post_meta( $p2, 'gs1_id', true ) );
	es_check( 'SC-02: متای دیگر خراب نمی‌شود', 'زیرعنوان یک' === get_post_meta( $p1, 'zireonvan', true ) && 'زیرعنوان دو' === get_post_meta( $p2, 'zireonvan', true ) );

	// AC-03: invalid nonce → no save.
	$_POST['_wpnonce'] = 'bad-nonce';
	$_POST['es_qe']    = array( 'gs1_id' => 'HACK' );
	ProductQuickEdit::save( $p1, get_post( $p1 ) );
	es_check( 'AC-03: nonce نامعتبر ذخیره نمی‌کند', '00999' === get_post_meta( $p1, 'gs1_id', true ) );

	// AC-03: subscriber cannot save.
	$sub = wp_insert_user( array( 'user_login' => 'es_qe_sub_' . wp_rand( 100, 999 ), 'user_pass' => wp_generate_password( 16 ), 'role' => 'subscriber' ) );
	wp_set_current_user( $sub );
	$_POST['_wpnonce'] = wp_create_nonce( 'bulk-posts' );
	$_POST['es_qe']    = array( 'gs1_id' => 'SUBHACK' );
	ProductQuickEdit::save( $p1, get_post( $p1 ) );
	es_check( 'AC-03: کاربر بدون دسترسی ذخیره نمی‌کند', '00999' === get_post_meta( $p1, 'gs1_id', true ) );
	wp_delete_user( $sub );
	wp_set_current_user( 1 );

	// true_sale only accepts true/false.
	$_POST['es_qe'] = array( 'true_sale' => 'maybe' );
	$_POST['_wpnonce'] = wp_create_nonce( 'bulk-posts' );
	ProductQuickEdit::save( $p1, get_post( $p1 ) );
	es_check( 'true_sale نامعتبر رد می‌شود', 'false' === get_post_meta( $p1, 'true_sale', true ) );

	// Quick/Bulk edit fields render with the allowed keys.
	ob_start();
	ProductQuickEdit::quick_edit_fields( 'es_true_sale', 'product' );
	$qe = ob_get_clean();
	es_check( 'فیلد Quick Edit رندر می‌شود', false !== strpos( $qe, 'es_qe[gs1_id]' ) && false !== strpos( $qe, 'es_qe[true_sale]' ) );
	ob_start();
	ProductQuickEdit::bulk_edit_fields( 'es_true_sale', 'product' );
	$be = ob_get_clean();
	es_check( 'فیلد Bulk Edit با گزینه بدون تغییر رندر می‌شود', false !== strpos( $be, 'es_qe[true_sale]' ) );
	// Non-product / other column renders nothing.
	ob_start();
	ProductQuickEdit::quick_edit_fields( 'title', 'post' );
	es_check( 'روی ستون/تایپ دیگر چیزی رندر نمی‌شود', '' === ob_get_clean() );

	// Sale-status column renders with data attributes for JS population.
	ob_start();
	ProductQuickEdit::render_column( 'es_true_sale', $p1 );
	$col = ob_get_clean();
	es_check( 'ستون وضعیت فروش data-attribute دارد', false !== strpos( $col, 'data-gs1_id' ) && false !== strpos( $col, 'es-qe-data' ) );

	wp_delete_post( $p1, true );
	wp_delete_post( $p2, true );
	unset( $_POST['es_qe'], $_POST['bulk_edit'], $_POST['_wpnonce'] );
	unregister_post_type( 'product' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
