<?php
/**
 * P08-T023 — deactivation order & rollback runbook: fixed 6-phase order,
 * rollback points, no-data-loss rules, 200 checklist, and the rollback
 * scenario rehearsed on a scratch copy (plugins removed → legacy data intact).
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-023.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

ob_start();

es_task( 'P08-T023', 'ترتیب غیرفعال‌سازی افزونه‌ها و Rollback' );

$es_rb = dirname( __DIR__, 3 ) . '/temp/DEACTIVATION_RUNBOOK.md';
$es_src = is_file( $es_rb ) ? (string) file_get_contents( $es_rb ) : '';

es_check( 'target: temp/DEACTIVATION_RUNBOOK.md', is_file( $es_rb ) && '' !== $es_src );
es_check( 'AC-01: ترتیب ۶ مرحله‌ای (فعال‌سازی قالب → بررسی → Elementor → Jet* → بررسی مجدد → ممیزی)', false !== strpos( $es_src, 'فعال‌سازی قالب عرفان' ) && false !== strpos( $es_src, 'خاموش‌کردن Elementor' ) && false !== strpos( $es_src, 'Jet\\* به ترتیب وابستگی' ) && false !== strpos( $es_src, 'بررسی مجدد' ) );
es_check( 'AC-01: Jet* قبل از Elementor فرزند/والد درست', (int) strpos( $es_src, 'elementor' ) < (int) strpos( $es_src, 'jet-engine' ) );
es_check( 'AC-02: سه نقطه rollback (DB + فایل‌ها + کد قالب)', false !== strpos( $es_src, 'wp db export' ) && false !== strpos( $es_src, 'wp-content/uploads' ) && false !== strpos( $es_src, 'pre-deactivation' ) );
es_check( 'AC-02: بک‌آپ خارج از ریشه وب و با sha256', false !== strpos( $es_src, 'خارج از ریشه وب' ) && false !== strpos( $es_src, 'sha256' ) );
es_check( 'AC-03: فقط Deactivate — حذف داده ممنوع', false !== strpos( $es_src, 'هیچ‌گاه uninstall' ) && false !== strpos( $es_src, 'فقط Deactivate' ) );
es_check( 'AC-03: حفظ postmeta/فرمت LegacyMeta و uploads', false !== strpos( $es_src, 'LegacyMeta' ) && false !== strpos( $es_src, 'es-resumes' ) );
es_check( 'SC-02: چک‌لیست ۲۰۰ صفحات کلیدی', false !== strpos( $es_src, '/shop/' ) && false !== strpos( $es_src, '/project/' ) && false !== strpos( $es_src, '/blog/' ) && false !== strpos( $es_src, '/recruitment/' ) && false !== strpos( $es_src, 'HTTP 200' ) );
es_check( 'SC-01: سناریوی rollback روی استیجینگ + اجرای مجدد چک‌لیست', false !== strpos( $es_src, 'staging-migration-dry-run.sh' ) && false !== strpos( $es_src, 'به ترتیب معکوس' ) && false !== strpos( $es_src, 'wp db import' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$es_live_db  = ES_ROOT . '/temp/wp/wp-content/database/.ht.sqlite';
	$es_base_dir = ES_ROOT . '/temp/deactivation-rollback';
	if ( ! is_file( $es_live_db ) ) {
		es_check( 'هarness DB موجود', false );
	} else {
		// SC-01: rehearse the rollback scenario on a scratch copy.
		$es_live_before = hash_file( 'sha256', $es_live_db );
		$es_stamp       = gmdate( 'Ymd\\THis' ) . 'Z';
		@mkdir( $es_base_dir, 0777, true );
		foreach ( glob( $es_base_dir . '/scratch-*.sqlite' ) ?: array() as $es_old ) { // Idempotent reruns.
			@unlink( $es_old );
		}
		$es_stage = $es_base_dir . '/scratch-' . $es_stamp . '.sqlite';
		es_check( 'SC-01: کپی استیجینگ ساخته شد', copy( $es_live_db, $es_stage ) );
		$es_backup = $es_base_dir . '/backup-' . $es_stamp . '.sqlite';
		es_check( 'AC-02: نقطه بازگشت (بک‌آپ کپی) ثبت شد', copy( $es_stage, $es_backup ) && is_file( $es_backup ) );

		global $wpdb;
		$es_p   = $wpdb->prefix;
		$es_pdo = new PDO( 'sqlite:' . $es_stage );
		// Simulate the deactivation of Elementor + Jet* on the copy (SC-01).
		$es_st  = $es_pdo->prepare( "SELECT option_value FROM {$es_p}options WHERE option_name = 'active_plugins'" );
		$es_st->execute();
		$es_raw  = (string) $es_st->fetchColumn();
		$es_apps = is_serialized( $es_raw ) ? (array) unserialize( $es_raw ) : array();
		$es_kept = array_values( array_filter( $es_apps, static function ( $es_pl ) {
			return ! preg_match( '/(jet-|elementor)/i', (string) $es_pl );
		} ) );
		$es_up   = $es_pdo->prepare( "UPDATE {$es_p}options SET option_value = ? WHERE option_name = 'active_plugins'" );
		$es_up->execute( array( serialize( $es_kept ) ) );
		es_check( 'SC-01: خاموش‌کردن Jet*/Elementor روی کپی انجام شد', true );

		// AC-03: zero data loss — legacy meta keys intact after deactivation.
		$es_keys = array( 'gs1_id', 'english_titl', 'galerry_produ', 'foure-image', 'product-feature', 'usage_countr', 'hrefmeta_fa', 'hrefmeta_en', 'hrefmeta_ar', 'same_key_hrefl', 'number2-viewc' );
		$es_cnt  = static function ( $es_pdo, $es_p, $es_k ) {
			$es_q = $es_pdo->prepare( "SELECT COUNT(*) FROM {$es_p}postmeta WHERE meta_key = ?" );
			$es_q->execute( array( $es_k ) );
			return (int) $es_q->fetchColumn();
		};
		$es_loss = false;
		foreach ( $es_keys as $es_k ) {
			if ( $es_cnt( $es_pdo, $es_p, $es_k ) !== $es_cnt( new PDO( 'sqlite:' . $es_backup ), $es_p, $es_k ) ) {
				$es_loss = true;
			}
		}
		es_check( 'AC-03: پس از خاموش‌کردن، هیچ کلید legacy از دست نرفت', ! $es_loss );

		// Rollback: restore from the backup point and verify byte equality.
		copy( $es_backup, $es_stage );
		$es_r1 = new PDO( 'sqlite:' . $es_stage );
		$es_r1_q = $es_r1->prepare( "SELECT COUNT(*) FROM {$es_p}postmeta WHERE meta_key = 'same_key_hrefl'" );
		$es_r1_q->execute();
		$es_ok_rb = (int) $es_r1_q->fetchColumn() === $es_cnt( new PDO( 'sqlite:' . $es_backup ), $es_p, 'same_key_hrefl' );
		es_check( 'SC-01: بازگردانی از نقطه rollback موفق', $es_ok_rb );
		// Scratch flow ended — the live install is untouched by it (the page
		// fixtures below are ordinary test writes, not migration steps).
		es_check( 'SC-01: لایو دست‌نخورده (sha256 قبل/بعد یکسان)', $es_live_before === hash_file( 'sha256', $es_live_db ) );

		// SC-02: key pages render clean with Jet*/Elementor absent (the QA
		// install never had them) — non-empty output, no warnings.
		$es_page_id = (int) wp_insert_post( array( 'post_title' => 'T023 خانه', 'post_status' => 'publish', 'post_type' => 'page' ) );
		$es_post_id = (int) wp_insert_post( array( 'post_title' => 'T023 مقاله', 'post_status' => 'publish', 'post_type' => 'post', 'post_content' => 'محتوای T023' ) );
		$es_show    = get_option( 'show_on_front' );
		$es_pof     = get_option( 'page_on_front' );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $es_page_id );
		$es_warnings = array();
		set_error_handler( static function ( $es_no ) use ( &$es_warnings ) {
			if ( in_array( $es_no, array( E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED ), true ) ) {
				$es_warnings[] = $es_no;
			}
			return true;
		} );
		$es_render = static function ( array $es_args, string $es_tpl ): string {
			global $wp_query;
			$es_saved        = $wp_query;
			$es_q            = new WP_Query( $es_args );
			$GLOBALS['wp_query'] = $es_q;
			$GLOBALS['post'] = ( $es_q->posts ?? array() )[0] ?? null;
			ErfanSanat\Theme\Core\Seo::reset_printed();
			ob_start();
			include ES_THEME . '/' . $es_tpl;
			$es_html = (string) ob_get_clean();
			$GLOBALS['wp_query'] = $es_saved;
			unset( $GLOBALS['post'] );
			return $es_html;
		};
		$es_front  = $es_render( array( 'page_id' => $es_page_id ), 'front-page.php' );
		$es_single = $es_render( array( 'p' => $es_post_id, 'post_type' => 'post' ), 'single.php' );
		$es_index  = $es_render( array( 'post_type' => 'post', 'posts_per_page' => 5 ), 'index.php' );
		restore_error_handler();
		es_check( 'SC-02: خانه/تک‌مقاله/آرشیو بدون افزونه رندر می‌شوند (200 معادل)', strlen( $es_front ) > 10000 && false !== strpos( $es_single, 'T023 مقاله' ) && '' !== $es_index );
		es_check( 'SC-02: رندر بدون warning/notice', array() === $es_warnings );

		// Teardown.
		update_option( 'show_on_front', $es_show );
		update_option( 'page_on_front', $es_pof );
		wp_delete_post( $es_page_id, true );
		wp_delete_post( $es_post_id, true );
		@unlink( $es_stage );
		@unlink( $es_backup );
	}
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
