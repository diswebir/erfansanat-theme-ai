<?php
/**
 * P08-T017 — admin entries UI: filtered list (type/status/date), escaped
 * details, nonce-gated read/unread/delete, capability denied for subscribers.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\FormEntries;
use ErfanSanat\Theme\Forms\Entries;
use ErfanSanat\Theme\Forms\FeedbackForm;

ob_start();

es_task( 'P08-T017', 'پنل مدیریت ورودی فرم‌ها' );

es_check( 'target: inc/Admin/FormEntries.php', es_file( 'inc/Admin/FormEntries.php' ) );
es_check( 'وابستگی: boot در functions.php', es_contains( 'functions.php', 'Admin\\FormEntries::boot' ) );
es_check( 'AC-03: اکشن‌ها با nonce', es_contains( 'inc/Admin/FormEntries.php', 'es_entries_nonce' ) && es_contains( 'inc/Admin/FormEntries.php', 'wp_verify_nonce' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';
	require_once ABSPATH . 'wp-admin/includes/user.php';
	foreach ( array( 'es_t017_admin', 'es_t017_sub' ) as $es_stale ) { // Leftovers from an aborted run would fail wp_insert_user.
		$es_old = get_user_by( 'login', $es_stale );
		if ( $es_old ) { wp_delete_user( (int) $es_old->ID ); }
	}
	$admin = (int) wp_insert_user( array( 'user_login' => 'es_t017_admin', 'user_pass' => wp_generate_password( 20 ), 'user_email' => 't017admin@example.test', 'role' => 'administrator' ) );
	$sub   = (int) wp_insert_user( array( 'user_login' => 'es_t017_sub', 'user_pass' => wp_generate_password( 20 ), 'user_email' => 't017sub@example.test', 'role' => 'subscriber' ) );

	// Fixtures: contact unread, quote read, feedback pending (older date).
	$c1 = Entries::store( 'contact', array( 'name' => 'تماس الف', 'phone' => '09131111111', 'message' => 'پیام تماس' ), array() );
	$q1 = Entries::store( 'quote', array( 'name' => 'استعلام الف', 'phone' => '09132222222', 'message' => 'پیام استعلام' ), array() );
	$f1 = Entries::store( 'feedback', array( 'name' => 'بازخورد الف', 'rating' => '5', 'message' => 'پیام بازخورد' ), array() );
	Entries::mark_read( $q1 );
	FeedbackForm::approve( $f1 );
	update_post_meta( $f1, '_es_feedback_status', 'approve' );
	// Age the feedback entry by a month.
	global $wpdb;
	$old_date = gmdate( 'Y-m-d H:i:s', time() - 30 * DAY_IN_SECONDS );
	$wpdb->update( $wpdb->posts, array( 'post_date' => $old_date, 'post_date_gmt' => $old_date ), array( 'ID' => $f1 ) );
	clean_post_cache( $f1 );

	// Menu registration under the ErfanSanat dashboard.
	wp_set_current_user( $admin );
	FormEntries::register_menu();
	global $submenu;
	es_check( 'منوی زیرمجموعه پیشخوان ثبت شد', is_array( $submenu ) && isset( $submenu[ \ErfanSanat\Theme\Admin\ThemeOptions::SLUG ] ) && in_array( FormEntries::SLUG, wp_list_pluck( (array) $submenu[ \ErfanSanat\Theme\Admin\ThemeOptions::SLUG ], 2 ), true ) );

	// AC-01: filters work (admin context).
	$all = FormEntries::get_entries( array() );
	es_check( 'SC-01: ادمین همه ورودی‌ها را می‌بیند', is_array( $all ) && in_array( $c1, $all, true ) && in_array( $q1, $all, true ) && in_array( $f1, $all, true ) );
	$only_contact = FormEntries::get_entries( array( 'type' => 'contact' ) );
	es_check( 'AC-01: فیلتر نوع فرم', is_array( $only_contact ) && in_array( $c1, $only_contact, true ) && ! in_array( $q1, $only_contact, true ) && ! in_array( $f1, $only_contact, true ) );
	$only_unread = FormEntries::get_entries( array( 'status' => 'unread' ) );
	es_check( 'AC-01: فیلتر وضعیت خوانده‌شدن', is_array( $only_unread ) && in_array( $c1, $only_unread, true ) && ! in_array( $q1, $only_unread, true ) );
	$recent = FormEntries::get_entries( array( 'date_from' => date( 'Y-m-d', time() - 2 * DAY_IN_SECONDS ) ) );
	es_check( 'AC-01: فیلتر تاریخ از', is_array( $recent ) && in_array( $c1, $recent, true ) && ! in_array( $f1, $recent, true ) );

	// AC-02: page render is escaped (no raw script tags, data visible).
	ob_start();
	FormEntries::render_page( array( 'type' => 'contact' ) );
	$html = ob_get_clean();
	es_check( 'AC-02: خروجی ادمین تگ اسکریپت ندارد', false === strpos( $html, '<script' ) );
	es_check( 'AC-02: نام و شماره ورودی نمایش داده می‌شود', false !== strpos( $html, 'تماس الف' ) && false !== strpos( $html, '09131111111' ) );
	es_check( 'AC-02: اکشن‌ها توکن nonce دارند', false !== strpos( $html, '_wpnonce' ) && false !== strpos( $html, 'es_entries_action' ) );

	// AC-03 + SC-01: nonce-gated actions from the admin.
	$bad = FormEntries::perform_action( 'read', $c1, 'bad-token' );
	es_check( 'AC-03: nonce نامعتبر → خطا', is_wp_error( $bad ) );
	$ok_read = FormEntries::perform_action( 'read', $c1, wp_create_nonce( 'es_entries_nonce' ) );
	es_check( 'SC-01: ادمین ورودی را read می‌کند', ! is_wp_error( $ok_read ) && 'read' === Entries::status( $c1 ) );
	$ok_unread = FormEntries::perform_action( 'unread', $c1, wp_create_nonce( 'es_entries_nonce' ) );
	es_check( 'SC-01: برگشت به unread', ! is_wp_error( $ok_unread ) && 'unread' === Entries::status( $c1 ) );
	$ok_delete = FormEntries::perform_action( 'delete', $c1, wp_create_nonce( 'es_entries_nonce' ) );
	es_check( 'AC-03: حذف با nonce انجام می‌شود', ! is_wp_error( $ok_delete ) && null === get_post( $c1 ) );

	// SC-02: subscriber cannot list or act.
	wp_set_current_user( $sub );
	es_check( 'SC-02: کوئری برای subscriber خطا می‌دهد', is_wp_error( FormEntries::get_entries( array() ) ) );
	es_check( 'SC-02: اکشن برای subscriber خطا می‌دهد', is_wp_error( FormEntries::perform_action( 'unread', $q1, wp_create_nonce( 'es_entries_nonce' ) ) ) );

	wp_set_current_user( $admin );
	Entries::delete( $q1 );
	Entries::delete( $f1 );
	wp_delete_user( $admin );
	wp_delete_user( $sub );
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
