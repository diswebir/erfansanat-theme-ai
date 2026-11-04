<?php
/**
 * P03-T016 — متاباکس روابط محصول
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ProductMetaboxes;
use ErfanSanat\Theme\Core\LegacyMeta;

es_task( 'P03-T016', 'متاباکس روابط محصول' );

es_check( 'target: inc/Admin/ProductMetaboxes.php', es_file( 'inc/Admin/ProductMetaboxes.php' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';

	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	wp_set_current_user( 1 );

	$proj1 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه الف' ) );
	$proj2 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه ب' ) );
	$t1    = wp_insert_term( 'تونل نوری', 'project_type', array( 'slug' => 'rel-tunnel-t016' ) );
	$t2    = wp_insert_term( 'پل نوری', 'project_type', array( 'slug' => 'rel-bridge-t016' ) );
	$pid   = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول روابط' ) );
	$nonce = wp_create_nonce( 'es_product_relations' );

	// AC-01: project-ids accepts only valid project ids.
	$_POST['es_relations']               = array( 'project-ids' => array( $proj1, $proj2, 999999 ) );
	$_POST['es_product_relations_nonce'] = $nonce;
	ProductMetaboxes::save_relations( $pid );
	es_check( 'AC-01: فقط ID پروژه معتبر ذخیره می‌شود', LegacyMeta::get( $pid, 'project-ids' ) === array( $proj1, $proj2 ) );

	// AC-02: usage_countr accepts only valid project_type term ids.
	$_POST['es_relations'] = array( 'usage_countr' => array( $t1['term_id'], $t2['term_id'], 999999 ) );
	ProductMetaboxes::save_relations( $pid );
	es_check( 'AC-02: فقط term_id نوع پروژه معتبر ذخیره می‌شود', LegacyMeta::get( $pid, 'usage_countr' ) === array( $t1['term_id'], $t2['term_id'] ) );

	// AC-03: existing JSON storage format is preserved.
	update_post_meta( $pid, 'project-ids', wp_json_encode( array( $proj1 ) ) );
	$_POST['es_relations'] = array( 'project-ids' => array( $proj1, $proj2 ) );
	ProductMetaboxes::save_relations( $pid );
	$raw = get_post_meta( $pid, 'project-ids', true );
	es_check( 'AC-03: فرمت JSON حفظ می‌شود', is_string( $raw ) && is_array( json_decode( $raw, true ) ) );
	es_check( 'AC-03: محتوای JSON درست است', LegacyMeta::get( $pid, 'project-ids' ) === array( $proj1, $proj2 ) );

	// SC-01: saving relations does not corrupt the referenced project.
	es_check( 'SC-01: پروژه مرجع سالم می‌ماند', 'پروژه الف' === get_post( $proj1 )->post_title );

	// SC-02: a deleted project id is cleaned up on next save.
	wp_delete_post( $proj2, true );
	$_POST['es_relations'] = array( 'project-ids' => array( $proj1, $proj2 ) );
	ProductMetaboxes::save_relations( $pid );
	es_check( 'SC-02: ID حذف‌شده پاک‌سازی می‌شود', LegacyMeta::get( $pid, 'project-ids' ) === array( $proj1 ) );

	// AC-03: nonce guard.
	unset( $_POST['es_product_relations_nonce'] );
	$_POST['es_relations'] = array( 'project-ids' => array( 999999 ) );
	ProductMetaboxes::save_relations( $pid );
	es_check( 'AC-03: بدون nonce ذخیره نمی‌شود', LegacyMeta::get( $pid, 'project-ids' ) === array( $proj1 ) );

	// Metabox renders both relation fields.
	ob_start();
	ProductMetaboxes::render_relations( get_post( $pid ) );
	$html = ob_get_clean();
	es_check( 'متاباکس: فیلد project-ids رندر می‌شود', false !== strpos( $html, 'es_relations[project-ids]' ) );
	es_check( 'متاباکس: فیلد usage_countr رندر می‌شود', false !== strpos( $html, 'es_relations[usage_countr]' ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $proj1, true );
	wp_delete_term( $t1['term_id'], 'project_type' );
	wp_delete_term( $t2['term_id'], 'project_type' );
	unset( $_POST['es_relations'], $_POST['es_product_relations_nonce'] );
	unregister_post_type( 'product' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
