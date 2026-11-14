<?php
/**
 * P08-T014 — quote popup form: product/project id resolution with inquiry
 * CTA opener, sanitized quantity, honeypot/nonce guards, invalid id rejected.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Forms\Entries;
use ErfanSanat\Theme\Forms\QuoteForm;

ob_start();

es_task( 'P08-T014', 'فرم استعلام قیمت' );

es_check( 'target: template-parts/forms/quote.php', es_file( 'template-parts/forms/quote.php' ) );
es_check( 'target: inc/Forms/QuoteForm.php', es_file( 'inc/Forms/QuoteForm.php' ) );
es_check( 'وابستگی ثبت‌شده: CTA استعلامی محصول پاپ‌آپ را باز می‌کند', es_contains( 'template-parts/product/purchase.php', 'data-quote-open' ) );
es_check( 'وابستگی: boot در functions.php', es_contains( 'functions.php', 'Forms\\QuoteForm::boot' ) );
es_check( 'AC-02: متن مرجع CTA حفظ شده', es_contains( 'template-parts/product/purchase.php', 'درخواست پیش‌فاکتور رسمی و استعلام قیمت' ) );

/** Capture an AJAX callback's JSON output + HTTP status without dying. */
function es_capture_ajax( callable $fn ): array {
	$captured = array( 'status' => 200, 'json' => null, 'output' => '' );
	$status_filter = function ( $header, $code ) use ( &$captured ) {
		$captured['status'] = (int) $code;
		return $header;
	};
	add_filter( 'status_header', $status_filter, 10, 2 );
	$die_handler = function () {
		return function ( $message, $title, $args ) { throw new RuntimeException( 'wp_die' ); };
	};
	add_filter( 'wp_die_ajax_handler', $die_handler );
	add_filter( 'wp_die_handler', $die_handler );
	ob_start();
	try { $fn(); } catch ( Throwable $e ) { /* expected wp_die */ }
	$output = ob_get_clean();
	remove_filter( 'status_header', $status_filter );
	remove_filter( 'wp_die_ajax_handler', $die_handler );
	remove_filter( 'wp_die_handler', $die_handler );
	$captured['output'] = $output;
	$captured['json']   = json_decode( $output, true );
	return $captured;
}

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';
	if ( ! defined( 'DOING_AJAX' ) ) { define( 'DOING_AJAX', true ); }
	wp_set_current_user( 0 );

	es_check( 'endpoint عمومی es_quote_submit ثبت شده', false !== has_action( 'wp_ajax_nopriv_es_quote_submit' ) );

	// Part: reference-styled modal with named fields + honeypot + nonce.
	ob_start();
	include ES_THEME . '/template-parts/forms/quote.php';
	$part = ob_get_clean();
	es_check( 'پارت: مودال + فرم با name های استاندارد', false !== strpos( $part, 'data-quote-modal="true"' ) && false !== strpos( $part, 'data-quote-form="true"' ) && false !== strpos( $part, 'name="es_qf_name"' ) && false !== strpos( $part, 'name="es_qf_phone"' ) && false !== strpos( $part, 'name="es_qf_quantity"' ) );
	es_check( 'پارت: شناسه محتوا/honeypot/nonce/خطا', false !== strpos( $part, 'name="es_qf_product_id"' ) && false !== strpos( $part, 'name="es_qf_project_id"' ) && false !== strpos( $part, 'name="es_qf_hp"' ) && false !== strpos( $part, 'name="nonce"' ) && false !== strpos( $part, 'data-quote-error="true"' ) );

	// Fixtures: published product + project.
	$product = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'درخت نوری T014' ) );
	$project = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'میدان T014' ) );
	$draft_product = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'draft', 'post_title' => 'پیش‌نویس T014' ) );

	// SC-01: valid product quote stored and linked.
	$ok = QuoteForm::submit( array(
		'es_qf_name'       => 'خریدار T014',
		'es_qf_phone'      => '09135556677',
		'es_qf_product'    => 'درخت نوری دلسا',
		'es_qf_quantity'   => '۲۴<svg onload=alert(1)>',
		'es_qf_message'    => 'استعلام ۲۴ عدد',
		'es_qf_product_id' => (string) $product,
	), array( 'page' => '/product/x/' ) );
	es_check( 'SC-01: استعلام محصول ذخیره می‌شود', is_array( $ok ) && ( $ok['entry'] ?? 0 ) > 0 );
	if ( is_array( $ok ) ) {
		$eid = (int) $ok['entry'];
		es_check( 'SC-01: به محصول لینک می‌شود', 'quote' === get_post_meta( $eid, '_es_entry_form', true ) && ( get_post_meta( $eid, '_es_entry_meta', true )['content_url'] ?? '' ) === get_permalink( $product ) );
		$data = get_post_meta( $eid, '_es_entry_data', true );
		es_check( 'AC-03: تعداد sanitize می‌شود (تگ حذف)', is_array( $data ) && false === strpos( (string) ( $data['quantity'] ?? 'x' ), '<' ) && false !== strpos( (string) ( $data['quantity'] ?? '' ), '۲۴' ) );
		es_check( 'SC-01: وضعیت unread', 'unread' === Entries::status( $eid ) );
		Entries::delete( $eid );
	}

	// Valid project quote.
	$okp = QuoteForm::submit( array( 'es_qf_name' => 'مدیر پروژه T014', 'es_qf_phone' => '09135556688', 'es_qf_message' => 'استعلام پروژه', 'es_qf_project_id' => (string) $project ), array() );
	es_check( 'SC-01: استعلام پروژه به پروژه لینک می‌شود', is_array( $okp ) && ( get_post_meta( (int) $okp['entry'], '_es_entry_meta', true )['content_url'] ?? '' ) === get_permalink( $project ) );
	if ( is_array( $okp ) ) { Entries::delete( (int) $okp['entry'] ); }

	// SC-02: invalid ids rejected, nothing stored.
	$before = wp_count_posts( 'es_form_entry' );
	es_check( 'SC-02: شناسه نامعتبر رد می‌شود', is_wp_error( QuoteForm::submit( array( 'es_qf_name' => 'x', 'es_qf_phone' => '09131112233', 'es_qf_message' => 'm', 'es_qf_product_id' => '999999' ), array() ) ) );
	es_check( 'SC-02: شناسه غیرعددی رد می‌شود', is_wp_error( QuoteForm::submit( array( 'es_qf_name' => 'x', 'es_qf_phone' => '09131112233', 'es_qf_message' => 'm', 'es_qf_product_id' => 'DROP TABLE', ), array() ) ) );
	$draft_ok = QuoteForm::submit( array( 'es_qf_name' => 'x', 'es_qf_phone' => '09131112233', 'es_qf_message' => 'm', 'es_qf_product_id' => (string) $draft_product ), array() );
	es_check( 'SC-02: محصول پیش‌نویس رد می‌شود', is_wp_error( $draft_ok ) );
	$after = wp_count_posts( 'es_form_entry' );
	es_check( 'SC-02: هیچ ورودی نامعتبر ذخیره نشد', ( $after->publish ?? 0 ) === ( $before->publish ?? 0 ) );

	// Generic quote without ids still works.
	$gen = QuoteForm::submit( array( 'es_qf_name' => 'عمومی T014', 'es_qf_phone' => '09135550000', 'es_qf_message' => 'استعلام عمومی' ), array() );
	es_check( 'استعلام بدون شناسه هم ثبت می‌شود', is_array( $gen ) && ( $gen['entry'] ?? 0 ) > 0 );
	if ( is_array( $gen ) ) { Entries::delete( (int) $gen['entry'] ); }

	// Honeypot + nonce guards.
	es_check( 'SC-02: honeypot پر → رد', is_wp_error( QuoteForm::submit( array( 'es_qf_name' => 'ربات', 'es_qf_phone' => '09130000000', 'es_qf_message' => 'spam', 'es_qf_hp' => 'x' ), array() ) ) );
	$_REQUEST = array( 'nonce' => 'bad' );
	$res = es_capture_ajax( static function () { ErfanSanat\Theme\Core\Ajax::dispatch( 'quote_submit' ); } );
	es_check( 'SC-02: nonce نامعتبر → 403', 403 === $res['status'] );
	$_REQUEST = array(
		'nonce'            => wp_create_nonce( 'es_ajax_quote_submit' ),
		'es_qf_name'       => 'endpoint T014',
		'es_qf_phone'      => '09131112244',
		'es_qf_message'    => 'از endpoint',
		'es_qf_product_id' => (string) $product,
	);
	$res = es_capture_ajax( static function () { ErfanSanat\Theme\Core\Ajax::dispatch( 'quote_submit' ); } );
	es_check( 'SC-01: endpoint معتبر موفق', 200 === $res['status'] && true === ( $res['json']['success'] ?? false ) );
	if ( true === ( $res['json']['success'] ?? false ) ) {
		$eid = (int) ( $res['json']['data']['entry'] ?? 0 );
		es_check( 'SC-01: endpoint پاسخ لینک محصول دارد', ( $res['json']['data']['content_url'] ?? '' ) === get_permalink( $product ) );
		Entries::delete( $eid );
	}
	$_REQUEST = array();

	wp_delete_post( $product, true ); wp_delete_post( $project, true ); wp_delete_post( $draft_product, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
