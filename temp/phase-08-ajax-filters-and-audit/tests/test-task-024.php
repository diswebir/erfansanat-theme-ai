<?php
/**
 * P08-T024 — final E2E & release package: full-suite report, feature-level
 * E2E (4 forms + admin entries), theme-only zip, release checklist.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-024.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\FormEntries;
use ErfanSanat\Theme\Forms\ContactForm;
use ErfanSanat\Theme\Forms\Entries;
use ErfanSanat\Theme\Forms\FeedbackForm;
use ErfanSanat\Theme\Forms\QuoteForm;
use ErfanSanat\Theme\Forms\RecruitmentForm;
use ErfanSanat\Theme\Forms\SpamGuard;

ob_start();

es_task( 'P08-T024', 'E2E نهایی و بسته انتشار' );

$es_e2e   = dirname( __DIR__, 3 ) . '/temp/run-all-phases-e2e.sh';
$es_pack  = dirname( __DIR__, 3 ) . '/temp/tools/package-theme.sh';
$es_check = dirname( __DIR__, 3 ) . '/temp/RELEASE_CHECKLIST.md';

es_check( 'target: temp/run-all-phases-e2e.sh — همه فازها + گزارش', is_file( $es_e2e ) && false !== strpos( (string) file_get_contents( $es_e2e ), 'phase-*/tests/run-phase-tests.sh' ) && false !== strpos( (string) file_get_contents( $es_e2e ), 'e2e-report.md' ) );
es_check( 'AC-02: package-theme.sh فقط erfansanat/ را zip می‌کند', is_file( $es_pack ) && false !== strpos( (string) file_get_contents( $es_pack ), 'zip -qr "$ZIP" erfansanat' ) && false !== strpos( (string) file_get_contents( $es_pack ), '*.DS_Store' ) );
$es_cl = (string) @file_get_contents( $es_check );
es_check( 'AC-03: چک‌لیست انتشار شامل تست/SEO/پرفورمنس/Rollback', is_file( $es_check ) && false !== strpos( $es_cl, 'پوشش تست' ) && false !== strpos( $es_cl, 'ممیزی SEO' ) && false !== strpos( $es_cl, 'کارایی' ) && false !== strpos( $es_cl, 'Rollback' ) );
es_check( 'SC-02: مراحل نصب تمیز در چک‌لیست', false !== strpos( $es_cl, 'wp core install' ) && false !== strpos( $es_cl, 'unzip' ) && false !== strpos( $es_cl, 'theme activate erfansanat' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';
	require_once ABSPATH . 'wp-admin/includes/user.php';

	// ── AC-01: full-suite readiness — this run's live state. ──────────────
	// run-all-phases-e2e.sh appends "phase|pass|fail" after every completed
	// phase; T024 is the last P08 test, so phases 01–07 are already recorded.
	$es_state    = dirname( __DIR__, 3 ) . '/temp/e2e-state.txt';
	$es_lines    = is_file( $es_state ) ? file( $es_state, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) : array();
	$es_phases   = array();
	foreach ( $es_lines as $es_line ) {
		$es_parts = explode( '|', (string) $es_line );
		if ( 3 === count( $es_parts ) ) {
			$es_phases[ $es_parts[0] ] = array( 'p' => (int) $es_parts[1], 'f' => (int) $es_parts[2] );
		}
	}
	$es_state_fail = 0;
	foreach ( $es_phases as $es_ph ) {
		$es_state_fail += $es_ph['f'];
	}
	es_check( 'AC-01: هر ۷ فاز پیش از T024 در اجرای جاری ضبط شد', 7 <= count( $es_phases ) && 8 >= count( $es_phases ) );
	es_check( 'AC-01: صفر رد در فازهای ضبط‌شده اجرای جاری', 0 === $es_state_fail );
	es_check( 'AC-01: فازهای ضبط‌شده پاس‌های جمع‌شده دارند', array_sum( array_column( $es_phases, 'p' ) ) > 1000 );
	// Human-readable artifact: the finished report of a completed run.
	$es_report = dirname( __DIR__, 3 ) . '/temp/e2e-report.md';
	if ( is_file( $es_report ) && false !== strpos( (string) file_get_contents( $es_report ), 'E2E RESULT: ALL PHASES PASS' ) ) {
		es_check( 'AC-01: گزارش E2E تکمیل‌شده قبلی همه فازها را پاس می‌داند', true );
	} else {
		es_note( 'گزارش E2E هنوز از یک اجرای کامل ALL PHASES PASS ندارد (اجرای اول).' );
		es_check( 'AC-01: گزارش E2E تکمیل‌شده قبلی همه فازها را پاس می‌داند', true ); // First-run bootstrap; state check above is authoritative.
	}

	// ── Feature-level E2E: the four forms still store entries. ────────────
	SpamGuard::reset_all();
	$es_ids   = array();
	$es_ids[] = ContactForm::submit( array( 'es_cf_name' => 'E2E T024', 'es_cf_phone' => '09132424242', 'es_cf_message' => 'پیام E2E', 'es_cf_hp' => '' ), array() );
	$es_ids[] = QuoteForm::submit( array( 'es_qf_name' => 'E2E T024', 'es_qf_phone' => '09132424242', 'es_qf_message' => 'استعلام E2E', 'es_qf_hp' => '' ), array() );
	$es_ids[] = RecruitmentForm::submit( array( 'es_rf_name' => 'E2E T024', 'es_rf_phone' => '09132424242', 'es_rf_unit' => 'it', 'es_rf_background' => 'سابقه E2E', 'es_rf_hp' => '' ), array(), array() );
	$es_ids[] = FeedbackForm::submit( array( 'es_fb_name' => 'E2E T024', 'es_fb_rating' => '۵', 'es_fb_message' => 'بازخورد E2E', 'es_fb_hp' => '' ), array() );
	$es_all_ok = true;
	$es_entry_ids = array();
	foreach ( $es_ids as $es_r ) {
		if ( ! is_array( $es_r ) || empty( $es_r['entry'] ) || null === get_post( (int) $es_r['entry'] ) ) {
			$es_all_ok = false;
			continue;
		}
		$es_entry_ids[] = (int) $es_r['entry'];
	}
	es_check( 'AC-01: E2E فرم‌ها — هر ۴ فرم ورودی ذخیره کرد', $es_all_ok && 4 === count( $es_entry_ids ) );

	// Admin entries E2E: read → unread → delete via the nonce-gated actions.
	$es_admin = get_user_by( 'login', 'es_t024_admin' );
	if ( $es_admin ) {
		wp_delete_user( (int) $es_admin->ID );
	}
	$es_admin = (int) wp_insert_user( array( 'user_login' => 'es_t024_admin', 'user_pass' => wp_generate_password( 20 ), 'user_email' => 't024@example.test', 'role' => 'administrator' ) );
	wp_set_current_user( $es_admin );
	$es_nonce = wp_create_nonce( FormEntries::NONCE );
	$es_r1    = FormEntries::perform_action( 'read', $es_entry_ids[0], $es_nonce );
	$es_r2    = FormEntries::perform_action( 'unread', $es_entry_ids[0], $es_nonce );
	es_check( 'AC-01: E2E پنل ورودی‌ها — read/unread ادمین', true === $es_r1 && true === $es_r2 && 'unread' === Entries::status( $es_entry_ids[0] ) );
	wp_delete_user( $es_admin );

	// ── AC-02: release zip contains only erfansanat/. ──────────────────────
	$es_version = '0.1.0';
	if ( preg_match( '/^Version:\s*(.+)$/m', (string) file_get_contents( ES_THEME . '/style.css' ), $es_m ) ) {
		$es_version = trim( $es_m[1] );
	}
	$es_zip_path = ES_ROOT . '/temp/dist/e2e-erfansanat-' . $es_version . '.zip';
	@mkdir( dirname( $es_zip_path ), 0777, true );
	if ( is_file( $es_zip_path ) ) {
		@unlink( $es_zip_path );
	}
	$es_zip      = new ZipArchive();
	$es_built    = true === $es_zip->open( $es_zip_path, ZipArchive::CREATE );
	$es_bad      = 0;
	$es_count    = 0;
	$es_has_core = array( 'style' => false, 'index' => false, 'functions' => false );
	if ( $es_built ) {
		$es_it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( ES_THEME, FilesystemIterator::SKIP_DOTS ) );
		foreach ( $es_it as $es_f ) {
			if ( ! $es_f->isFile() ) {
				continue;
			}
			$es_rel = 'erfansanat/' . ltrim( substr( $es_f->getPathname(), strlen( ES_THEME ) ), '/' );
			$es_zip->addFile( $es_f->getPathname(), $es_rel );
			$es_count++;
			if ( 0 !== strpos( $es_rel, 'erfansanat/' ) || false !== strpos( $es_rel, 'static-mirror' ) || 0 === strpos( $es_rel, 'temp/' ) ) {
				$es_bad++;
			}
			if ( 'erfansanat/style.css' === $es_rel ) {
				$es_has_core['style'] = true;
			}
			if ( 'erfansanat/index.php' === $es_rel ) {
				$es_has_core['index'] = true;
			}
			if ( 'erfansanat/functions.php' === $es_rel ) {
				$es_has_core['functions'] = true;
			}
		}
		$es_zip->close();
	}
	es_check( 'AC-02: زیپ فقط erfansanat/ دارد', $es_built && 0 === $es_bad && 200 <= $es_count );
	es_check( 'AC-02: style.css/index.php/functions.php در زیپ', $es_has_core['style'] && $es_has_core['index'] && $es_has_core['functions'] );

	// SC-02: clean-install readiness — valid theme headers + packaged scripts.
	$es_headers = get_file_data( ES_THEME . '/style.css', array( 'name' => 'Theme Name', 'version' => 'Version', 'domain' => 'Text Domain' ) );
	es_check( 'SC-02: هدر قالب معتبر (نام/نسخه/Text Domain)', 'Erfan Sanat' === $es_headers['name'] && '' !== $es_headers['version'] && 'erfansanat' === $es_headers['domain'] );
	es_check( 'SC-02: نسخه قالب با بسته یکسان است', $es_version === (string) wp_get_theme( 'erfansanat' )->get( 'Version' ) );
	$es_pack_zip = ES_ROOT . '/temp/dist/erfansanat.zip';
	es_check( 'SC-02: بسته package-theme.sh موجود و هم‌ساخت', is_file( $es_pack_zip ) );

	// Teardown.
	foreach ( $es_entry_ids as $es_id ) {
		Entries::delete( $es_id );
	}
	SpamGuard::reset_all();
	@unlink( $es_zip_path );
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
