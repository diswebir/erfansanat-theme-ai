<?php
/**
 * P08-T015 — recruitment form: whitelisted units, optional resume file with
 * extension + size guards, sanitized fields, stored as unread entry.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Forms\Entries;
use ErfanSanat\Theme\Forms\RecruitmentForm;

ob_start();

es_task( 'P08-T015', 'فرم استخدام' );

es_check( 'target: template-parts/forms/recruitment.php', es_file( 'template-parts/forms/recruitment.php' ) );
es_check( 'target: inc/Forms/RecruitmentForm.php', es_file( 'inc/Forms/RecruitmentForm.php' ) );
es_check( 'وابستگی ثبت‌شده: صفحه استخدام به پارت ارجاع می‌دهد', es_contains( 'page-templates/recruitment.php', 'forms/recruitment' ) );
es_check( 'وابستگی: boot در functions.php', es_contains( 'functions.php', 'Forms\\RecruitmentForm::boot' ) );
es_check( 'AC-02: پسوندهای مجاز رزومه', es_contains( 'inc/Forms/RecruitmentForm.php', 'pdf' ) && es_contains( 'inc/Forms/RecruitmentForm.php', 'docx' ) );
es_check( 'AC-03: سقف حجم فایل', es_contains( 'inc/Forms/RecruitmentForm.php', 'MAX_RESUME_SIZE' ) );

/** Capture an AJAX callback's JSON output + HTTP status without dying. */
function es_capture_ajax( callable $fn ): array {
	$captured = array( 'status' => 200, 'json' => null, 'output' => '' );
	$status_filter = function ( $header, $code ) use ( &$captured ) {
		$captured['status'] = (int) $code;
		return $header;
	};
	add_filter( 'status_header', $status_filter, 10, 2 );
	$die_handler = function () {
		return function ( $message, $title, $args ) { throw new RuntimeException( 'wp_die' ); };
	};
	add_filter( 'wp_die_ajax_handler', $die_handler );
	add_filter( 'wp_die_handler', $die_handler );
	ob_start();
	try { $fn(); } catch ( Throwable $e ) { /* expected wp_die */ }
	$output = ob_get_clean();
	remove_filter( 'status_header', $status_filter );
	remove_filter( 'wp_die_ajax_handler', $die_handler );
	remove_filter( 'wp_die_handler', $die_handler );
	$captured['output'] = $output;
	$captured['json']   = json_decode( $output, true );
	return $captured;
}

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	\ErfanSanat\Theme\Forms\SpamGuard::reset_all(); // P08-T018: fresh rate budget (dependency).
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';
	if ( ! defined( 'DOING_AJAX' ) ) { define( 'DOING_AJAX', true ); }
	wp_set_current_user( 0 );

	es_check( 'endpoint عمومی es_recruitment_submit ثبت شده', false !== has_action( 'wp_ajax_nopriv_es_recruitment_submit' ) );

	// Part renders with names, unit select, file input, honeypot, nonce.
	ob_start();
	include ES_THEME . '/template-parts/forms/recruitment.php';
	$part = ob_get_clean();
	es_check( 'پارت: فیلدهای مرجع با name', false !== strpos( $part, 'name="es_rf_name"' ) && false !== strpos( $part, 'name="es_rf_phone"' ) && false !== strpos( $part, 'name="es_rf_unit"' ) && false !== strpos( $part, 'name="es_rf_experience"' ) && false !== strpos( $part, 'name="es_rf_background"' ) );
	es_check( 'پارت: فایل رزومه + honeypot + nonce + خطا', false !== strpos( $part, 'name="es_rf_resume"' ) && false !== strpos( $part, 'name="es_rf_hp"' ) && false !== strpos( $part, 'name="nonce"' ) && false !== strpos( $part, 'data-recruit-error="true"' ) && false !== strpos( $part, '.pdf,.doc,.docx,.rtf,.txt' ) );

	$mk_upload = static function ( string $name, string $tmp, int $size ): array {
		return array( 'name' => $name, 'type' => 'application/octet-stream', 'tmp_name' => $tmp, 'error' => 0, 'size' => $size );
	};

	// SC-01: valid application with an allowed resume file.
	$tmp = tempnam( sys_get_temp_dir(), 'esrf' );
	file_put_contents( $tmp, 'ریزومه آزمایشی T015' );
	$res = RecruitmentForm::submit(
		array( 'es_rf_name' => 'علی رضایی T015', 'es_rf_phone' => '09131234567', 'es_rf_unit' => 'rnd', 'es_rf_experience' => '3-5', 'es_rf_background' => 'مهندس برق با سابقه' ),
		$mk_upload( 'resume-t015.pdf', $tmp, 1024 ),
		array( 'page' => '/recruitment/' )
	);
	es_check( 'SC-01: درخواست شغلی با رزومه مجاز ثبت می‌شود', is_array( $res ) && ( $res['entry'] ?? 0 ) > 0 );
	if ( is_array( $res ) ) {
		$eid = (int) $res['entry'];
		es_check( 'AC-01: واحد R&D ذخیره می‌شود', 'recruitment' === get_post_meta( $eid, '_es_entry_form', true ) && ( get_post_meta( $eid, '_es_entry_data', true )['unit'] ?? '' ) === 'rnd' );
		es_check( 'SC-01: متادیتای رزومه ذخیره شد', '' !== (string) ( get_post_meta( $eid, '_es_entry_meta', true )['resume_name'] ?? '' ) );
		es_check( 'SC-01: وضعیت unread', 'unread' === Entries::status( $eid ) );
		Entries::delete( $eid );
	}

	// All four reference units are accepted.
	foreach ( array( 'it', 'accounting', 'project', 'rnd' ) as $es_u ) {
		$r = RecruitmentForm::submit( array( 'es_rf_name' => 'واحد ' . $es_u, 'es_rf_phone' => '09131234567', 'es_rf_unit' => $es_u, 'es_rf_background' => 'سابقه' ), array(), array() );
		es_check( 'AC-01: واحد «' . $es_u . '» پذیرفته می‌شود', is_array( $r ) && ( get_post_meta( (int) $r['entry'], '_es_entry_data', true )['unit'] ?? '' ) === $es_u );
		Entries::delete( (int) $r['entry'] );
	}

	// SC-02: php/exe rejected; oversize rejected; bad unit rejected; nothing stored.
	$before = wp_count_posts( 'es_form_entry' );
	es_check( 'SC-02: فایل php رد می‌شود', is_wp_error( RecruitmentForm::submit( array( 'es_rf_name' => 'x', 'es_rf_phone' => '09131234567', 'es_rf_unit' => 'it', 'es_rf_background' => 'بک‌دور' ), $mk_upload( 'shell.php', $tmp, 512 ), array() ) ) );
	es_check( 'SC-02: فایل exe رد می‌شود', is_wp_error( RecruitmentForm::submit( array( 'es_rf_name' => 'x', 'es_rf_phone' => '09131234567', 'es_rf_unit' => 'it', 'es_rf_background' => 'bad' ), $mk_upload( 'virus.exe', $tmp, 512 ), array() ) ) );
	es_check( 'AC-03: فایل بزرگ‌تر از سقف رد می‌شود', is_wp_error( RecruitmentForm::submit( array( 'es_rf_name' => 'x', 'es_rf_phone' => '09131234567', 'es_rf_unit' => 'it', 'es_rf_background' => 'big' ), $mk_upload( 'big.pdf', $tmp, RecruitmentForm::MAX_RESUME_SIZE + 1 ), array() ) ) );
	es_check( 'AC-01: واحد ناشناخته رد می‌شود', is_wp_error( RecruitmentForm::submit( array( 'es_rf_name' => 'x', 'es_rf_phone' => '09131234567', 'es_rf_unit' => 'hacked', 'es_rf_background' => 'u' ), array(), array() ) ) );
	$after = wp_count_posts( 'es_form_entry' );
	es_check( 'SC-02: هیچ ورودی نامعتبر ذخیره نشد', ( $after->publish ?? 0 ) === ( $before->publish ?? 0 ) );

	// Without file still works (resume as text/link).
	$nof = RecruitmentForm::submit( array( 'es_rf_name' => 'بدون فایل T015', 'es_rf_phone' => '09131112233', 'es_rf_unit' => 'project', 'es_rf_experience' => 'junior', 'es_rf_background' => 'لینک رزومه: https://example.test/cv.pdf' ), array(), array() );
	es_check( 'درخواست بدون فایل رزومه ثبت می‌شود', is_array( $nof ) && ( $nof['entry'] ?? 0 ) > 0 );
	if ( is_array( $nof ) ) { Entries::delete( (int) $nof['entry'] ); }

	// Guards: honeypot + bad nonce + good endpoint.
	es_check( 'SC-02: honeypot پر → رد', is_wp_error( RecruitmentForm::submit( array( 'es_rf_name' => 'ربات', 'es_rf_phone' => '09130000000', 'es_rf_unit' => 'it', 'es_rf_background' => 'spam', 'es_rf_hp' => 'x' ), array(), array() ) ) );
	$_REQUEST = array( 'nonce' => 'bad' );
	$res = es_capture_ajax( static function () { ErfanSanat\Theme\Core\Ajax::dispatch( 'recruitment_submit' ); } );
	es_check( 'SC-02: nonce نامعتبر → 403', 403 === $res['status'] );
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_recruitment_submit' ), 'es_rf_name' => 'endpoint T015', 'es_rf_phone' => '09131112244', 'es_rf_unit' => 'accounting', 'es_rf_background' => 'از endpoint' );
	$res = es_capture_ajax( static function () { ErfanSanat\Theme\Core\Ajax::dispatch( 'recruitment_submit' ); } );
	es_check( 'SC-01: endpoint معتبر موفق', 200 === $res['status'] && true === ( $res['json']['success'] ?? false ) );
	if ( true === ( $res['json']['success'] ?? false ) ) {
		$eid = (int) ( $res['json']['data']['entry'] ?? 0 );
		es_check( 'SC-01: endpoint ورودی recruitment ذخیره کرد', 'recruitment' === get_post_meta( $eid, '_es_entry_form', true ) );
		Entries::delete( $eid );
	}
	$_REQUEST = array();
	unlink( $tmp );
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
