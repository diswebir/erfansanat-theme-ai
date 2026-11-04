<?php
/**
 * P03-T021 — متاباکس تاریخ و محصولات پروژه
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-021.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ProjectMetaboxes;
use ErfanSanat\Theme\Core\LegacyMeta;

es_task( 'P03-T021', 'متاباکس تاریخ و محصولات پروژه' );

es_check( 'target: inc/Admin/ProjectMetaboxes.php', es_file( 'inc/Admin/ProjectMetaboxes.php' ) );
es_check( 'AC-02: املای mortabet_mahsole_2 در سورس دقیق است', es_contains( 'inc/Admin/ProjectMetaboxes.php', 'mortabet_mahsole_2' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';

	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	wp_set_current_user( 1 );
	unset( $_POST['es_project_details'], $_POST['es_project_details_nonce'] );

	$prod1 = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول الف' ) );
	$prod2 = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول ب' ) );
	$pid   = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه تاریخ' ) );
	$nonce = wp_create_nonce( 'es_project_details' );

	// SC-01: Jalali completion date round-trips as text.
	$_POST['es_project_details']         = array( 'finish-pro-time' => '1404/01/01' );
	$_POST['es_project_details_nonce']   = $nonce;
	ProjectMetaboxes::save_details( $pid );
	es_check( 'SC-01: تاریخ شمسی round-trip می‌شود', '1404/01/01' === get_post_meta( $pid, 'finish-pro-time', true ) );

	// SC-02: two related products save.
	$_POST['es_project_details'] = array( 'mortabet_mahsole_2' => array( $prod1, $prod2 ) );
	ProjectMetaboxes::save_details( $pid );
	es_check( 'SC-02: دو محصول مرتبط ذخیره می‌شوند', array( $prod1, $prod2 ) === LegacyMeta::get( $pid, 'mortabet_mahsole_2' ) );
	es_check( 'AC-02: خواندن محصولات معتبر', array( $prod1, $prod2 ) === ProjectMetaboxes::get_related_products( $pid ) );

	// AC-02: existing JSON format is preserved.
	update_post_meta( $pid, 'mortabet_mahsole_2', wp_slash( wp_json_encode( array( $prod1 ) ) ) );
	$_POST['es_project_details'] = array( 'mortabet_mahsole_2' => array( $prod1, $prod2 ) );
	ProjectMetaboxes::save_details( $pid );
	$raw = get_post_meta( $pid, 'mortabet_mahsole_2', true );
	es_check( 'AC-02: فرمت JSON حفظ می‌شود', is_string( $raw ) && is_array( json_decode( $raw, true ) ) );

	// AC-03: a deleted product is cleaned up, relation not broken.
	wp_delete_post( $prod2, true );
	$_POST['es_project_details'] = array( 'mortabet_mahsole_2' => array( $prod1, $prod2 ) );
	ProjectMetaboxes::save_details( $pid );
	es_check( 'AC-03: محصول حذف‌شده پاک‌سازی می‌شود', array( $prod1 ) === ProjectMetaboxes::get_related_products( $pid ) );

	// Nonce guard.
	unset( $_POST['es_project_details_nonce'] );
	$_POST['es_project_details'] = array( 'finish-pro-time' => 'HACK' );
	ProjectMetaboxes::save_details( $pid );
	es_check( 'بدون nonce ذخیره نمی‌شود', '1404/01/01' === get_post_meta( $pid, 'finish-pro-time', true ) );

	// Metabox renders both fields.
	ob_start();
	ProjectMetaboxes::render_details( get_post( $pid ) );
	$html = ob_get_clean();
	es_check( 'متاباکس: فیلد تاریخ رندر می‌شود', false !== strpos( $html, 'es_project_details[finish-pro-time]' ) );
	es_check( 'متاباکس: فیلد محصولات رندر می‌شود', false !== strpos( $html, 'es_project_details[mortabet_mahsole_2]' ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $prod1, true );
	unset( $_POST['es_project_details'], $_POST['es_project_details_nonce'] );
	unregister_post_type( 'product' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
