<?php
/**
 * P02-T016 — UI متاباکس: ترتیب/فعال/افزودن/حذف (drag&drop + AJAX امن)
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\SectionMetabox;
use ErfanSanat\Theme\Sections\SectionManager;

// Keep output buffered so headers_sent() stays false and status_header() runs.
ob_start();

es_task( 'P02-T016', 'UI متاباکس: drag&drop + AJAX امن' );

es_check( 'target: inc/Admin/SectionMetabox.php', es_file( 'inc/Admin/SectionMetabox.php' ) );
es_check( 'target: assets/admin/sections.js', es_file( 'assets/admin/sections.js' ) );
es_check( 'AC-01: JS از fetch بدون رفرش استفاده می‌کند', es_contains( 'assets/admin/sections.js', 'fetch(' ) && es_contains( 'assets/admin/sections.js', 'dataTransfer' ) );

/**
 * Capture an AJAX callback's JSON output + HTTP status without dying.
 */
function es_capture_ajax( callable $fn ): array {
	$captured = array( 'status' => 200, 'json' => null, 'output' => '' );

	$status_filter = function ( $header, $code ) use ( &$captured ) {
		$captured['status'] = (int) $code;
		return $header;
	};
	add_filter( 'status_header', $status_filter, 10, 2 );

	$die_handler = function () {
		return function ( $message, $title, $args ) {
			throw new RuntimeException( 'wp_die' );
		};
	};
	add_filter( 'wp_die_ajax_handler', $die_handler );
	add_filter( 'wp_die_handler', $die_handler );

	ob_start();
	try {
		$fn();
	} catch ( Throwable $e ) {
		// Expected wp_die.
	}
	$output = ob_get_clean();

	remove_filter( 'status_header', $status_filter );
	remove_filter( 'wp_die_ajax_handler', $die_handler );
	remove_filter( 'wp_die_handler', $die_handler );

	$captured['output'] = $output;
	$captured['json']   = json_decode( $output, true );

	return $captured;
}

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ABSPATH . 'wp-admin/includes/user.php';
	require_once ES_THEME . '/functions.php';

	if ( ! defined( 'DOING_AJAX' ) ) {
		define( 'DOING_AJAX', true );
	}

	wp_set_current_user( 1 );
	$nonce   = wp_create_nonce( SectionMetabox::NONCE_ACTION );
	$page_id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'صفحه متاباکس' ) );

	SectionManager::save_sections(
		$page_id,
		array(
			array( 'id' => 'hero', 'type' => 'hero', 'enabled' => true ),
			array( 'id' => 'about', 'type' => 'about', 'enabled' => false ),
		),
		'home'
	);

	// Metabox renders nonce + section rows.
	ob_start();
	SectionMetabox::render_metabox( get_post( $page_id ) );
	$metabox_html = ob_get_clean();
	es_check( 'AC-03: متاباکس فیلد nonce دارد', false !== strpos( $metabox_html, SectionMetabox::NONCE_NAME ) );
	es_check( 'متاباکس: ردیف سکشن‌ها رندر می‌شود', false !== strpos( $metabox_html, 'data-section-id="hero"' ) && false !== strpos( $metabox_html, 'data-es-sections' ) );

	// AC-03 / SC-02: invalid nonce rejected.
	$_POST = array( SectionMetabox::NONCE_NAME => 'invalid', 'post_id' => $page_id, 'context' => 'home', 'sections' => '[]' );
	$_REQUEST = $_POST;
	$bad = es_capture_ajax( array( SectionMetabox::class, 'ajax_save' ) );
	es_check( 'AC-03: nonce نامعتبر رد می‌شود (403)', 403 === $bad['status'] );

	// SC-02: non-editor rejected.
	$editor = wp_insert_user( array( 'user_login' => 'es_sub_t016_' . wp_rand( 100, 999 ), 'user_pass' => wp_generate_password( 16 ), 'role' => 'subscriber' ) );
	wp_set_current_user( $editor );
	$_POST = array( SectionMetabox::NONCE_NAME => wp_create_nonce( SectionMetabox::NONCE_ACTION ), 'post_id' => $page_id, 'context' => 'home', 'sections' => '[]' );
	$_REQUEST = $_POST;
	$unauth = es_capture_ajax( array( SectionMetabox::class, 'ajax_save' ) );
	es_check( 'SC-02: کاربر بدون دسترسی رد می‌شود (403)', 403 === $unauth['status'] );
	wp_delete_user( $editor );
	wp_set_current_user( 1 );

	// SC-01: reorder via AJAX persists (swap hero/about).
	$_POST = array(
		SectionMetabox::NONCE_NAME => $nonce,
		'post_id' => $page_id,
		'context' => 'home',
		'sections' => wp_slash( wp_json_encode( array( array( 'id' => 'about', 'type' => 'about', 'enabled' => false ), array( 'id' => 'hero', 'type' => 'hero', 'enabled' => true ) ) ) ),
	);
	$_REQUEST = $_POST;
	$reorder = es_capture_ajax( array( SectionMetabox::class, 'ajax_save' ) );
	es_check( 'SC-01: ذخیره ترتیب موفق است (200)', 200 === $reorder['status'] && ! empty( $reorder['json']['success'] ) );
	$after = SectionManager::get_sections( $page_id, 'home' );
	es_check( 'SC-01: ترتیب جدید اعمال شد', 'about' === $after[0]['type'] && 'hero' === $after[1]['type'] );

	// AC-02: disabled section stays disabled after save.
	es_check( 'AC-02: سکشن غیرفعال، غیرفعال می‌ماند', false === $after[0]['enabled'] );

	// Malformed payload -> 400.
	$_POST = array( SectionMetabox::NONCE_NAME => $nonce, 'post_id' => $page_id, 'context' => 'home', 'sections' => 'not-json' );
	$_REQUEST = $_POST;
	$malformed = es_capture_ajax( array( SectionMetabox::class, 'ajax_save' ) );
	es_check( 'داده نامعتبر پاسخ 400 می‌گیرد', 400 === $malformed['status'] );

	// Hook registered.
	es_check( 'AC-01: اکشن AJAX ثبت شده است', false !== has_action( 'wp_ajax_es_save_sections' ) );

	wp_delete_post( $page_id, true );
	unset( $_POST, $_REQUEST );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

ob_end_flush();
es_summary();
