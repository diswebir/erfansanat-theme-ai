<?php
/**
 * P02-T003 — منوی ادمین و دسترسی
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ThemeOptions;

es_task( 'P02-T003', 'منوی ادمین و دسترسی' );

es_check( 'target: inc/Admin/ThemeOptions.php', es_file( 'inc/Admin/ThemeOptions.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/user.php';

	es_check( 'AC-01: hook admin_menu ثبت شده', false !== has_action( 'admin_menu', 'ErfanSanat\\Theme\\Admin\\ThemeOptions::register_menu' ) );

	// Act as administrator.
	wp_set_current_user( 1 );
	ThemeOptions::register_menu();

	global $admin_page_hooks, $menu;
	es_check( 'AC-01: صفحه منو ثبت شد', isset( $admin_page_hooks[ ThemeOptions::SLUG ] ) );

	$cap_ok = false;
	foreach ( (array) $menu as $item ) {
		if ( isset( $item[2] ) && ThemeOptions::SLUG === $item[2] ) {
			$cap_ok = ( ThemeOptions::CAP === $item[1] );
		}
	}
	es_check( 'AC-01: capability منو manage_options است', $cap_ok );
	es_check( 'AC-01: ادمین دسترسی دارد', ThemeOptions::can_manage() );
	es_check( 'AC-03: لینک منو در پیشخوان', false !== strpos( ThemeOptions::page_url(), 'page=' . ThemeOptions::SLUG ) );

	// SC-01: an allowed admin can render the page.
	$rendered = false;
	ob_start();
	try {
		ThemeOptions::render_page();
		$rendered = true;
	} catch ( \Throwable $e ) {
		$rendered = false;
	}
	$html = ob_get_clean();
	es_check( 'SC-01: ادمین مجاز صفحه را می‌بیند', $rendered && false !== strpos( $html, 'es-dashboard' ) );

	// SC-02: a subscriber is blocked with a 403.
	$sub_id = wp_insert_user(
		array(
			'user_login' => 'es_subscriber_test',
			'user_pass'  => wp_generate_password( 20 ),
			'user_email' => 'es_subscriber_test@example.com',
			'role'       => 'subscriber',
		)
	);
	wp_set_current_user( $sub_id );
	es_check( 'AC-02: subscriber دسترسی manage ندارد', ! ThemeOptions::can_manage() );

	$response_code = 0;
	$handler       = function () use ( &$response_code ) {
		return function ( $message, $title, $args ) use ( &$response_code ) {
			$response_code = (int) ( $args['response'] ?? 0 );
			throw new \RuntimeException( 'wp_die' );
		};
	};
	add_filter( 'wp_die_handler', $handler );
	try {
		ThemeOptions::render_page();
	} catch ( \Throwable $e ) {
		// Expected 403 die.
	}
	remove_filter( 'wp_die_handler', $handler );
	es_check( 'SC-02: subscriber پاسخ 403 می‌گیرد', 403 === $response_code );

	// Cleanup.
	wp_set_current_user( 1 );
	wp_delete_user( $sub_id );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
