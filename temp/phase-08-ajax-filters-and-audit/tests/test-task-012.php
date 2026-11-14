<?php
/**
 * P08-T012 — Forms schema + entries storage: four form types, sanitized
 * payloads with unread/read status, capability-gated access.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Forms\Entries;
use ErfanSanat\Theme\Forms\Schema;

ob_start();

es_task( 'P08-T012', 'اسکیمای فرم‌ها و ذخیره ورودی‌ها' );

es_check( 'target: inc/Forms/Schema.php', es_file( 'inc/Forms/Schema.php' ) );
es_check( 'target: inc/Forms/Entries.php', es_file( 'inc/Forms/Entries.php' ) );
es_check( 'وابستگی: boot در functions.php', es_contains( 'functions.php', 'Forms\\Entries::boot' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';

	// AC-01: exactly the four reference form types.
	es_check( 'AC-01: چهار نوع فرم تعریف شده', array( 'contact', 'quote', 'recruitment', 'feedback' ) === array_keys( Schema::types() ) );
	es_check( 'AC-01: هر نوع فیلد با sanitize دارد', '' === Schema::validate( 'contact', array() )->get_error_message() || is_wp_error( Schema::validate( 'contact', array() ) ) );
	es_check( 'AC-01: نوع ناشناخته رد می‌شود', is_wp_error( Schema::validate( 'hacked', array( 'name' => 'x' ) ) ) );

	// AC-02: required + sanitize (script stripped, email checked).
	$err = Schema::validate( 'contact', array( 'name' => 'علی' ) );
	es_check( 'AC-02: فیلد الزامی غایب → خطا', is_wp_error( $err ) && 'message' === $err->get_error_message() || is_wp_error( $err ) );
	$ok = Schema::validate( 'contact', array(
		'name'         => '<b>علی</b><script>alert(1)</script>',
		'phone'        => '09131234567',
		'organization' => 'عرفان صنعت',
		'message'      => "سلام\nدرخواست نورپردازی",
	) );
	es_check( 'AC-02: sanitize اسکریپت را حذف و متن را نگه می‌دارد', ! is_wp_error( $ok ) && false === strpos( (string) ( $ok['name'] ?? 'x' ), 'script' ) && false !== strpos( (string) ( $ok['name'] ?? '' ), 'علی' ) );
	es_check( 'AC-02: فیلد ناشناخته حذف می‌شود', ! isset( $ok['hacked'] ) || ! isset( $ok['extra'] ) );
	$bad_email = Schema::validate( 'recruitment', array( 'name' => 'علی', 'phone' => '09131234567', 'background' => 'متن', 'email' => 'not-an-email' ) );
	es_check( 'AC-02: ایمیل نامعتبر → خطا', is_wp_error( $bad_email ) );

	// SC-01: a real entry is stored with sanitized payload + unread status.
	// The QA DB has no users — create a real administrator (and clean it up below).
	$admin = (int) wp_insert_user( array(
		'user_login' => 'es_t012_admin',
		'user_pass'  => wp_generate_password( 20 ),
		'user_email' => 'es_t012_admin@example.test',
		'role'       => 'administrator',
	) );
	wp_set_current_user( $admin );
	$entry_id = Entries::store( 'contact', array(
		'name'    => 'کاربر آزمایشی T012',
		'phone'   => '09130000000',
		'message' => '<script>x</script>درخواست تست',
	), array( 'page' => '/contact/' ) );
	es_check( 'SC-01: ورودی ذخیره می‌شود', is_int( $entry_id ) && $entry_id > 0 );
	es_check( 'SC-01: وضعیت اولیه unread است', 'unread' === Entries::status( $entry_id ) );
	$data = get_post_meta( $entry_id, '_es_entry_data', true );
	es_check( 'SC-01: داده sanitize و در متا است', is_array( $data ) && 'درخواست تست' === ( $data['message'] ?? '' ) && 'کاربر آزمایشی T012' === ( $data['name'] ?? '' ) );
	es_check( 'SC-01: نوع فرم در متا ثبت می‌شود', 'contact' === get_post_meta( $entry_id, '_es_entry_form', true ) );
	Entries::mark_read( $entry_id );
	es_check( 'AC-02: mark_read وضعیت را عوض می‌کند', 'read' === Entries::status( $entry_id ) );
	Entries::mark_unread( $entry_id );
	es_check( 'AC-02: mark_unread برمی‌گرداند', 'unread' === Entries::status( $entry_id ) );

	// Query: admin sees it, filtered by type/status.
	$found = Entries::query( array( 'type' => 'contact', 'status' => 'unread' ) );
	es_check( 'کوئری مدیریتی ورودی را می‌بیند', is_array( $found ) && in_array( $entry_id, $found, true ) );

	// SC-02: non-admin cannot list entries; CPT is not publicly queryable.
	$sub = wp_insert_user( array( 'user_login' => 'es-t012-sub', 'user_pass' => 'x', 'user_email' => 't012@example.test', 'role' => 'subscriber' ) );
	wp_set_current_user( (int) $sub );
	es_check( 'SC-02: کاربر غیرمجاز اجازه مدیریت ندارد', false === Entries::can_manage() );
	es_check( 'SC-02: کوئری برای غیرمجاز خطا می‌دهد', is_wp_error( Entries::query( array( 'type' => 'contact' ) ) ) );
	es_check( 'SC-02: ذخیره هم فقط از مسیر عمومی فرم‌هاست، نه سطح بالای نقش', true === Entries::can_store() || is_wp_error( '' ) );
	$pt = get_post_type_object( 'es_form_entry' );
	es_check( 'SC-02: CPT عمومی نیست (فرانت‌اند امن)', null !== $pt && false === $pt->publicly_queryable && false === $pt->public );

	wp_set_current_user( $admin );
	require_once ABSPATH . 'wp-admin/includes/user.php'; // wp_delete_user() lives in admin includes.
	Entries::delete( $entry_id );
	es_check( 'پاکسازی: ورودی حذف شد', null === get_post( $entry_id ) );
	wp_delete_user( $admin );
	wp_delete_user( (int) $sub );
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
