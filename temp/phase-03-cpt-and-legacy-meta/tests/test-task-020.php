<?php
/**
 * P03-T020 — متاباکس گالری پروژه
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-020.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ProjectMetaboxes;

es_task( 'P03-T020', 'متاباکس گالری پروژه' );

es_check( 'target: inc/Admin/ProjectMetaboxes.php', es_file( 'inc/Admin/ProjectMetaboxes.php' ) );
es_check( 'AC-03: املای foure-image در سورس دقیق است', es_contains( 'inc/Admin/ProjectMetaboxes.php', 'foure-image' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';

	wp_set_current_user( 1 );
	unset( $_POST['es_project_gallery'], $_POST['es_project_gallery_nonce'] );
	$pid   = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه گالری' ) );
	$nonce = wp_create_nonce( 'es_project_gallery' );

	// AC-01/02 + SC-01: modern gallery maps order to the five legacy keys.
	$_POST['es_project_gallery']       = '2,3,4,5,6';
	$_POST['es_project_gallery_nonce'] = $nonce;
	ProjectMetaboxes::save_gallery( $pid );
	es_check( 'AC-01: second-image درست است', '2' === (string) get_post_meta( $pid, 'second-image', true ) );
	es_check( 'AC-01: three-image درست است', '3' === (string) get_post_meta( $pid, 'three-image', true ) );
	es_check( 'SC-01: تصویر چهارم در foure-image ذخیره می‌شود', '4' === (string) get_post_meta( $pid, 'foure-image', true ) );
	es_check( 'AC-01: five-image درست است', '5' === (string) get_post_meta( $pid, 'five-image', true ) );
	es_check( 'AC-01: six-image درست است', '6' === (string) get_post_meta( $pid, 'six-image', true ) );
	es_check( 'AC-03: کلید با املای اصلاح‌شده ساخته نمی‌شود', '' === get_post_meta( $pid, 'four-image', true ) );

	// AC-02: the modern gallery key is synced and readable.
	es_check( 'AC-02: گالری مدرن همگام می‌شود', array( 2, 3, 4, 5, 6 ) === ProjectMetaboxes::get_gallery( $pid ) );

	// SC-02: removal + reorder keeps legacy keys synced; unused slots cleared.
	$_POST['es_project_gallery'] = '6,4,2';
	ProjectMetaboxes::save_gallery( $pid );
	es_check( 'SC-02: جابجایی second-image را همگام می‌کند', '6' === (string) get_post_meta( $pid, 'second-image', true ) );
	es_check( 'SC-02: جابجایی foure-image را همگام می‌کند', '2' === (string) get_post_meta( $pid, 'foure-image', true ) );
	es_check( 'SC-02: اسلات‌های خالی پاک می‌شوند', '' === (string) get_post_meta( $pid, 'five-image', true ) && '' === (string) get_post_meta( $pid, 'six-image', true ) );
	es_check( 'SC-02: گالری مدرن ترتیب جدید را دارد', array( 6, 4, 2 ) === ProjectMetaboxes::get_gallery( $pid ) );

	// Legacy-only project (no modern key) is read from the legacy keys.
	// Clear $_POST so creating it doesn't trigger the save hook.
	unset( $_POST['es_project_gallery'], $_POST['es_project_gallery_nonce'] );
	$p2 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه legacy' ) );
	update_post_meta( $p2, 'second-image', 21 );
	update_post_meta( $p2, 'foure-image', 24 );
	es_check( 'AC-02: خواندن از کلیدهای legacy', array( 21, 24 ) === ProjectMetaboxes::get_gallery( $p2 ) );

	// Nonce guard.
	unset( $_POST['es_project_gallery_nonce'] );
	$_POST['es_project_gallery'] = '99';
	ProjectMetaboxes::save_gallery( $pid );
	es_check( 'بدون nonce ذخیره نمی‌شود', array( 6, 4, 2 ) === ProjectMetaboxes::get_gallery( $pid ) );

	// Metabox renders the gallery field.
	ob_start();
	ProjectMetaboxes::render_gallery( get_post( $pid ) );
	$html = ob_get_clean();
	es_check( 'متاباکس: فیلد گالری رندر می‌شود', false !== strpos( $html, 'es_project_gallery' ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $p2, true );
	unset( $_POST['es_project_gallery'], $_POST['es_project_gallery_nonce'] );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
