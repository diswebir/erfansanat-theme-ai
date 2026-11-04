<?php
/**
 * P03-T018 — Repeater کاربرد و FAQ محصول
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ProductMetaboxes;
use ErfanSanat\Theme\Core\LegacyMeta;

es_task( 'P03-T018', 'Repeater کاربرد و FAQ محصول' );

es_check( 'target: inc/Admin/ProductMetaboxes.php', es_file( 'inc/Admin/ProductMetaboxes.php' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';

	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	wp_set_current_user( 1 );
	unset( $_POST['es_usage_faq'], $_POST['es_product_usage_faq_nonce'] );
	$pid   = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول FAQ' ) );
	$nonce = wp_create_nonce( 'es_product_usage_faq' );

	// AC-01: usage_product / add-new-usage exact subkey.
	$_POST['es_usage_faq'] = array(
		'usage_product' => array(
			array( 'add-new-usage' => 'نورپردازی میدان' ),
			array( 'add-new-usage' => 'نورپردازی پل' ),
		),
	);
	$_POST['es_product_usage_faq_nonce'] = $nonce;
	ProductMetaboxes::save_usage_faq( $pid );
	es_check( 'AC-01: usage_product/add-new-usage ذخیره می‌شود', array( 'نورپردازی میدان', 'نورپردازی پل' ) === array_column( LegacyMeta::get( $pid, 'usage_product' ), 'add-new-usage' ) );

	// SC-01: several FAQs save in order with exact subkeys.
	$faqs = array(
		array( 'name_question' => 'گارانتی؟', 'answer_question' => '۲۴ ماه' ),
		array( 'name_question' => 'نصب؟', 'answer_question' => "خط اول\nخط دوم" ),
		array( 'name_question' => 'ارسال؟', 'answer_question' => 'سراسر کشور' ),
	);
	$_POST['es_usage_faq'] = array( 'faq' => $faqs );
	ProductMetaboxes::save_usage_faq( $pid );
	$saved = LegacyMeta::get( $pid, 'faq' );
	es_check( 'SC-01: چند FAQ به‌ترتیب ذخیره می‌شوند', array_column( $saved, 'name_question' ) === array( 'گارانتی؟', 'نصب؟', 'ارسال؟' ) );
	es_check( 'AC-02: زیرکلیدهای name_question/answer_question دقیق‌اند', isset( $saved[0]['name_question'], $saved[0]['answer_question'] ) && '۲۴ ماه' === $saved[0]['answer_question'] );
	es_check( 'پاسخ چندخطی حفظ می‌شود', false !== strpos( $saved[1]['answer_question'], "\n" ) );

	// AC-03: a question with an empty answer is kept (section not broken).
	$_POST['es_usage_faq'] = array(
		'faq' => array(
			array( 'name_question' => 'سوال بی‌جواب', 'answer_question' => '' ),
			array( 'name_question' => '', 'answer_question' => '' ), // fully empty → dropped
			array( 'name_question' => 'سوال دوم', 'answer_question' => 'جواب' ),
		),
	);
	ProductMetaboxes::save_usage_faq( $pid );
	$saved = LegacyMeta::get( $pid, 'faq' );
	es_check( 'AC-03: پاسخ خالی FAQ را خراب نمی‌کند', 2 === count( $saved ) && 'سوال بی‌جواب' === $saved[0]['name_question'] && '' === $saved[0]['answer_question'] );
	es_check( 'SC-02: آیتم کاملاً خالی حذف می‌شود', 'سوال دوم' === $saved[1]['name_question'] );

	// item-0 format compatibility for an existing JetEngine faq.
	update_post_meta( $pid, 'faq', wp_slash( '{"item-0":{"name_question":"قدیمی","answer_question":"پاسخ"}}' ) );
	$_POST['es_usage_faq'] = array( 'faq' => array( array( 'name_question' => 'قدیمی', 'answer_question' => 'پاسخ' ), array( 'name_question' => 'جدید', 'answer_question' => 'پاسخ ۲' ) ) );
	ProductMetaboxes::save_usage_faq( $pid );
	$raw = get_post_meta( $pid, 'faq', true );
	es_check( 'فرمت item-0 سازگار می‌ماند', is_string( $raw ) && false !== strpos( $raw, 'item-1' ) );

	// Nonce guard.
	unset( $_POST['es_product_usage_faq_nonce'] );
	$_POST['es_usage_faq'] = array( 'faq' => array( array( 'name_question' => 'HACK', 'answer_question' => 'x' ) ) );
	ProductMetaboxes::save_usage_faq( $pid );
	es_check( 'بدون nonce ذخیره نمی‌شود', 'قدیمی' === LegacyMeta::get( $pid, 'faq' )[0]['name_question'] );

	// Metabox renders both repeaters.
	ob_start();
	ProductMetaboxes::render_usage_faq( get_post( $pid ) );
	$html = ob_get_clean();
	es_check( 'متاباکس: usage_product رندر می‌شود', false !== strpos( $html, 'es_usage_faq[usage_product]' ) );
	es_check( 'متاباکس: faq با زیرکلیدها رندر می‌شود', false !== strpos( $html, 'name_question' ) && false !== strpos( $html, 'answer_question' ) );

	wp_delete_post( $pid, true );
	unset( $_POST['es_usage_faq'], $_POST['es_product_usage_faq_nonce'] );
	unregister_post_type( 'product' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
