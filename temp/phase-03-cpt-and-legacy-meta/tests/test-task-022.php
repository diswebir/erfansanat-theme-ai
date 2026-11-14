<?php
/**
 * P03-T022 — متای دسته محصول: thumbnail و FAQ
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-022.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\TermMetaboxes;
use ErfanSanat\Theme\Core\LegacyMeta;

es_task( 'P03-T022', 'متای دسته محصول: thumbnail و FAQ' );

es_check( 'target: inc/Admin/TermMetaboxes.php', es_file( 'inc/Admin/TermMetaboxes.php' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';
	require_once ABSPATH . 'wp-admin/includes/user.php';

	if ( ! taxonomy_exists( 'product_cat' ) ) {
		register_taxonomy( 'product_cat', 'product', array( 'hierarchical' => true ) );
	}
	wp_set_current_user( 1 );
	unset( $_POST['es_term_meta'], $_POST['es_term_meta_nonce'] );

	// AC-03: hooks are product_cat-specific.
	es_check( 'AC-03: هوک edited_product_cat ثبت شده', false !== has_action( 'edited_product_cat', array( TermMetaboxes::class, 'save' ) ) );
	es_check( 'AC-03: روی category عمومی اعمال نمی‌شود', false === has_action( 'edited_category', array( TermMetaboxes::class, 'save' ) ) );

	// SC-01: save on a Persian term.
	$term = wp_insert_term( 'ریسه و تزئینات', 'product_cat', array( 'slug' => 'term-meta-t022' ) );
	$tid  = $term['term_id'];
	$nonce = wp_create_nonce( 'es_term_meta' );

	$_POST['es_term_meta']       = array(
		'thumbnail' => '42',
		'faq'       => array(
			array( 'name_question' => 'گارانتی دارد؟', 'answer_question' => 'بله، ۲۴ ماه' ),
			array( 'name_question' => 'نصب دارد؟', 'answer_question' => 'اختیاری' ),
		),
	);
	$_POST['es_term_meta_nonce'] = $nonce;
	TermMetaboxes::save( $tid, $term['term_taxonomy_id'] );

	es_check( 'AC-01: thumbnail شناسه رسانه ذخیره می‌کند', 42 === absint( get_term_meta( $tid, 'thumbnail', true ) ) );
	$faq = LegacyMeta::get_for_term( $tid, 'faq' );
	es_check( 'AC-02: faq round-trip می‌شود', 'گارانتی دارد؟' === $faq[0]['name_question'] && 'بله، ۲۴ ماه' === $faq[0]['answer_question'] );
	es_check( 'SC-01: ذخیره روی ترم فارسی کار می‌کند', 2 === count( $faq ) );

	// SC-02: empty FAQ renders nothing; populated FAQ renders.
	// Clear $_POST so creating the term doesn't trigger the save hook.
	unset( $_POST['es_term_meta'], $_POST['es_term_meta_nonce'] );
	$empty_term = wp_insert_term( 'دسته خالی', 'product_cat', array( 'slug' => 'empty-t022' ) );
	es_check( 'SC-02: FAQ خالی رندر نمی‌شود', '' === TermMetaboxes::render_faq_html( $empty_term['term_id'] ) );
	$html = TermMetaboxes::render_faq_html( $tid );
	es_check( 'SC-02: FAQ پر رندر می‌شود', false !== strpos( $html, 'گارانتی دارد؟' ) && false !== strpos( $html, 'es-faq-item' ) );

	// Empty rows are dropped on save.
	$_POST['es_term_meta']       = array( 'faq' => array( array( 'name_question' => '', 'answer_question' => '' ), array( 'name_question' => 'سوال', 'answer_question' => 'جواب' ) ) );
	$_POST['es_term_meta_nonce'] = $nonce;
	TermMetaboxes::save( $tid, $term['term_taxonomy_id'] );
	es_check( 'ردیف خالی حذف می‌شود', 1 === count( LegacyMeta::get_for_term( $tid, 'faq' ) ) );

	// Nonce guard.
	unset( $_POST['es_term_meta_nonce'] );
	$_POST['es_term_meta'] = array( 'thumbnail' => '999' );
	TermMetaboxes::save( $tid, $term['term_taxonomy_id'] );
	es_check( 'بدون nonce ذخیره نمی‌شود', 42 === absint( get_term_meta( $tid, 'thumbnail', true ) ) );

	// Capability guard.
	$sub = wp_insert_user( array( 'user_login' => 'es_term_sub_' . wp_rand( 100, 999 ), 'user_pass' => wp_generate_password( 16 ), 'role' => 'subscriber' ) );
	wp_set_current_user( $sub );
	$_POST['es_term_meta']       = array( 'thumbnail' => '777' );
	$_POST['es_term_meta_nonce'] = wp_create_nonce( 'es_term_meta' );
	TermMetaboxes::save( $tid, $term['term_taxonomy_id'] );
	es_check( 'کاربر بدون دسترسی ذخیره نمی‌کند', 42 === absint( get_term_meta( $tid, 'thumbnail', true ) ) );
	wp_delete_user( $sub );
	wp_set_current_user( 1 );

	// Edit form renders the fields with current values.
	ob_start();
	TermMetaboxes::render_edit_fields( get_term( $tid ), 'product_cat' );
	$form = ob_get_clean();
	es_check( 'فرم ویرایش: فیلد thumbnail رندر می‌شود', false !== strpos( $form, 'es_term_meta[thumbnail]' ) );

	wp_delete_term( $tid, 'product_cat' );
	wp_delete_term( $empty_term['term_id'], 'product_cat' );
	unset( $_POST['es_term_meta'], $_POST['es_term_meta_nonce'] );
	unregister_taxonomy( 'product_cat' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
