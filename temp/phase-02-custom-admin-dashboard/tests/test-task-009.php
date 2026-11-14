<?php
/**
 * P02-T009 — AJAX Save: sanitize و validation
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Sanitizer;
use ErfanSanat\Theme\Options\Schema;

es_task( 'P02-T009', 'AJAX Save: sanitize و validation' );

es_check( 'target: inc/Options/Sanitizer.php', es_file( 'inc/Options/Sanitizer.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// SC-02: Persian text + emoji survive.
	$persian = 'سلام دنیا 🌟 نورپردازی';
	es_check( 'SC-02: متن فارسی و ایموجی سالم می‌ماند', Sanitizer::sanitize_field( array( 'type' => 'text' ), $persian ) === $persian );

	// AC-03: HTML only in rich fields.
	$text = Sanitizer::sanitize_field( array( 'type' => 'text' ), '<b>تیتر</b> متن' );
	es_check( 'AC-03: فیلد text تگ HTML را حذف می‌کند', false === strpos( $text, '<b>' ) );
	$rich = Sanitizer::sanitize_field( array( 'type' => 'rich' ), '<strong>مهم</strong><script>bad()</script>' );
	es_check( 'AC-03: فیلد rich HTML مجاز را نگه می‌دارد', false !== strpos( $rich, '<strong>' ) );
	es_check( 'AC-03: فیلد rich اسکریپت را حذف می‌کند', false === strpos( $rich, '<script>' ) );

	// SC-01: invalid color / email / URL rejected.
	es_check( 'SC-01: رنگ معتبر پذیرفته می‌شود', '#047857' === Sanitizer::sanitize_field( array( 'type' => 'color' ), '#047857' ) );
	es_check( 'SC-01: رنگ نامعتبر رد می‌شود', is_wp_error( Sanitizer::sanitize_field( array( 'type' => 'color' ), 'notacolor' ) ) );
	es_check( 'SC-01: ایمیل معتبر پذیرفته می‌شود', 'info@erfansanat.com' === Sanitizer::sanitize_field( array( 'type' => 'email' ), 'info@erfansanat.com' ) );
	es_check( 'SC-01: ایمیل نامعتبر رد می‌شود', is_wp_error( Sanitizer::sanitize_field( array( 'type' => 'email' ), 'not-an-email' ) ) );
	es_check( 'SC-01: URL معتبر پذیرفته می‌شود', 'https://example.com' === Sanitizer::sanitize_field( array( 'type' => 'url' ), 'https://example.com' ) );
	es_check( 'SC-01: javascript: رد می‌شود', is_wp_error( Sanitizer::sanitize_field( array( 'type' => 'url' ), 'javascript:alert(1)' ) ) );

	// AC-01: per-type sanitizers.
	$num_field = array( 'type' => 'number', 'min' => 1, 'max' => 48, 'default' => 12 );
	es_check( 'AC-01: number به max محدود می‌شود', 48 === Sanitizer::sanitize_field( $num_field, 999 ) );
	es_check( 'AC-01: number نامعتبر رد می‌شود', is_wp_error( Sanitizer::sanitize_field( $num_field, 'abc' ) ) );
	$select_field = Schema::get_field( 'pagination_type' );
	es_check( 'AC-01: select معتبر', 'loadmore' === Sanitizer::sanitize_field( $select_field, 'loadmore' ) );
	es_check( 'AC-01: select نامعتبر رد می‌شود', is_wp_error( Sanitizer::sanitize_field( $select_field, 'evil' ) ) );
	es_check( 'AC-01: toggle به bool تبدیل می‌شود', true === Sanitizer::sanitize_field( array( 'type' => 'toggle' ), 1 ) );
	es_check( 'AC-01: media به absint تبدیل می‌شود', 42 === Sanitizer::sanitize_field( array( 'type' => 'media' ), '42' ) );
	es_check( 'AC-01: آیکون مجاز پذیرفته می‌شود', 'instagram' === Sanitizer::sanitize_field( array( 'type' => 'icon' ), 'instagram' ) );
	es_check( 'AC-01: آیکون غیرمجاز رد می‌شود', is_wp_error( Sanitizer::sanitize_field( array( 'type' => 'icon' ), 'evil_icon' ) ) );

	// Repeater sanitization.
	$rep_field = Schema::get_field( 'social_links' );
	$items     = array(
		array( 'network' => 'instagram', 'url' => 'https://instagram.com/a' ),
		array( 'network' => 'telegram', 'url' => 'https://t.me/b' ),
	);
	$clean_rep = Sanitizer::sanitize_field( $rep_field, $items );
	es_check( 'AC-01: repeater آیتم‌ها را پاک‌سازی می‌کند', is_array( $clean_rep ) && 2 === count( $clean_rep ) && 'https://instagram.com/a' === $clean_rep[0]['url'] );

	// AC-02: one invalid field does not abort the whole save.
	$batch = Sanitizer::sanitize_values(
		array(
			'brand_name'    => 'نام سالم',
			'contact_email' => 'invalid-email',
			'contact_phone' => '031-91091011',
		)
	);
	es_check( 'AC-02: فیلد سالم ذخیره می‌شود', isset( $batch['values']['brand_name'] ) && 'نام سالم' === $batch['values']['brand_name'] );
	es_check( 'AC-02: فیلد نامعتبر خطا برمی‌گرداند', isset( $batch['errors']['contact_email'] ) );
	es_check( 'AC-02: سایر فیلدهای سالم باقی می‌مانند', isset( $batch['values']['contact_phone'] ) );
	es_check( 'AC-02: فیلد نامعتبر در values نیست', ! isset( $batch['values']['contact_email'] ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
