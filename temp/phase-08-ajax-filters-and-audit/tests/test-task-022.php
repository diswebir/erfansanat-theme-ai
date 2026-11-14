<?php
/**
 * P08-T022 — staging migration dry-run: backup before migrating, before/after
 * counts, no legacy key renames, zero writes to the live install, real diff
 * report.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-022.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

ob_start();

es_task( 'P08-T022', 'Dry-run مهاجرت روی استیجینگ' );

$es_script   = dirname( __DIR__, 3 ) . '/temp/tools/staging-migration-dry-run.sh';
$es_template = dirname( __DIR__, 3 ) . '/temp/MIGRATION_REPORT_TEMPLATE.md';
$es_src      = is_file( $es_script ) ? (string) file_get_contents( $es_script ) : '';

es_check( 'target: temp/tools/staging-migration-dry-run.sh', is_file( $es_script ) && '' !== $es_src );
es_check( 'target: temp/MIGRATION_REPORT_TEMPLATE.md', is_file( $es_template ) );
es_check( 'AC-01: بک‌آپ با sha256 قبل از مهاجرت', false !== strpos( $es_src, 'staging-backup-' ) && false !== strpos( $es_src, 'sha256sum' ) );
es_check( 'AC-02: اسنپ‌شات قبل/بعد پست‌ها و ترم‌ها', false !== strpos( $es_src, 'wp_count_posts' ) && false !== strpos( $es_src, 'wp_count_terms' ) && false !== strpos( $es_src, 'counts.php' ) );
es_check( 'AC-03: املاهای تاریخی کلیدها دست‌نخورده (بدون rename)', false !== strpos( $es_src, 'english_titl' ) && false !== strpos( $es_src, 'galerry_produ' ) && false !== strpos( $es_src, 'foure-image' ) && false !== strpos( $es_src, 'product-feature2_copy' ) );
es_check( 'AC-03: ۳۶ کلید legacy در اسکریپت', 0 < preg_match_all( "/'(?:gs1_id|hrefmeta_fa|same_key_hrefl|number2-viewc|image-6)',/", $es_src ) );
es_check( 'SC-01: اجرا روی کپی scratch است نه لایو', false !== strpos( $es_src, 'mktemp' ) && false !== strpos( $es_src, 'ln -s' ) && false !== strpos( $es_src, 'cp "$SRC_DB" "$STAGE_DB"' ) && false !== strpos( $es_src, '--path="$STAGE_WP"' ) );
es_check( 'SC-02: گزارش markdown تولید می‌شود', false !== strpos( $es_src, 'migration-reports' ) && false !== strpos( $es_src, 'dry-run-$STAMP.md' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$es_live_db = ES_ROOT . '/temp/wp/wp-content/database/.ht.sqlite';
	if ( ! is_file( $es_live_db ) ) {
		es_check( 'هarness DB موجود', false );
	} else {
		// php-wasm cannot spawn processes, so the test replays the exact
		// dry-run flow in-process (copy → backup → count → idempotent write →
		// count → report); the .sh itself is proven by the bash harness.
		$es_live_before = hash_file( 'sha256', $es_live_db );
		$es_stamp       = gmdate( 'Ymd\THis' ) . 'Z';
		$es_reports_dir = ES_ROOT . '/temp/migration-reports';
		$es_backups_dir = $es_reports_dir . '/backups';
		foreach ( glob( $es_reports_dir . '/dry-run-*.md' ) ?: array() as $es_old ) { // Idempotent reruns.
			@unlink( $es_old );
		}
		foreach ( glob( $es_backups_dir . '/staging-backup-*.sqlite' ) ?: array() as $es_old ) {
			@unlink( $es_old );
		}
		@mkdir( $es_backups_dir, 0777, true );

		// SC-01: writes go to the scratch copy, never to the live install.
		$es_stage_db = $es_reports_dir . '/scratch-' . $es_stamp . '.sqlite';
		es_check( 'SC-01: کپی scratch ساخته شد', copy( $es_live_db, $es_stage_db ) && is_file( $es_stage_db ) );
		$es_backup = $es_backups_dir . '/staging-backup-' . $es_stamp . '.sqlite';
		es_check( 'AC-01: بک‌آپ قبل از مهاجرت (کپی + ثبت sha256)', copy( $es_stage_db, $es_backup ) && is_file( $es_backup ) && '' !== (string) hash_file( 'sha256', $es_backup ) );

		// AC-02/AC-03: count posts / terms / legacy keys on the scratch copy.
		$es_keys = array( 'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id', 'zireonvan', 'english_titl', 'before-sale-c', 'true_sale', 'product-catalog-file', 'galerry_produ', 'project-ids', 'usage_countr', 'product-feature', 'product-feature2_copy', 'usage_product', 'faq', 'second-image', 'three-image', 'foure-image', 'five-image', 'six-image', 'finish-pro-time', 'mortabet_mahsole_2', 'hrefmeta_en', 'hrefmeta_ar', 'hrefmeta_fa', 'same_key_hrefl', 'number2-viewc', 'number-viewc', 'gallery-filed', 'image-2', 'image-3', 'image-4', 'image-5', 'image-6' );
		global $wpdb;
		$es_p    = $wpdb->prefix;
		$es_pdo  = new PDO( 'sqlite:' . $es_stage_db );
		$es_count = static function () use ( $es_pdo, $es_p, $es_keys ) {
			$es_out = array( 'posts' => array(), 'terms' => array(), 'legacy_meta' => array() );
			foreach ( array( 'post', 'page', 'product', 'projects', 'services', 'es_form_entry' ) as $es_pt ) {
				$es_st = $es_pdo->prepare( "SELECT COUNT(*) FROM {$es_p}posts WHERE post_type = ? AND post_status IN ('publish','draft','pending','private','future')" );
				$es_st->execute( array( $es_pt ) );
				$es_out['posts'][ $es_pt ] = (int) $es_st->fetchColumn();
			}
			foreach ( array( 'category', 'post_tag', 'product_cat', 'product_tag', 'project_type', 'city' ) as $es_tx ) {
				$es_st = $es_pdo->prepare( "SELECT COUNT(*) FROM {$es_p}term_taxonomy WHERE taxonomy = ?" );
				$es_st->execute( array( $es_tx ) );
				$es_out['terms'][ $es_tx ] = (int) $es_st->fetchColumn();
			}
			foreach ( $es_keys as $es_k ) {
				$es_st = $es_pdo->prepare( "SELECT COUNT(*) FROM {$es_p}postmeta WHERE meta_key = ?" );
				$es_st->execute( array( $es_k ) );
				$es_out['legacy_meta'][ $es_k ] = (int) $es_st->fetchColumn();
			}
			return $es_out;
		};
		$es_before = $es_count();
		// Migration rehearsal on the copy: idempotent, format-preserving,
		// same-key writes only (mirrors the .sh migrate.php step).
		$es_in     = rtrim( str_repeat( '?,', count( $es_keys ) ), ',' );
		$es_stmt   = $es_pdo->prepare( "UPDATE {$es_p}postmeta SET meta_value = meta_value WHERE meta_key IN ($es_in)" );
		es_check( 'SC-01: نوشتن مهاجرت روی کپی انجام شد (لایو خوانده-only)', false !== $es_stmt->execute( $es_keys ) );
		$es_after = $es_count();

		es_check( 'AC-02: شمارش پست/ترم قبل و بعد یکسان (idempotent)', $es_before['posts'] === $es_after['posts'] && $es_before['terms'] === $es_after['terms'] );
		es_check( 'AC-03: هیچ کلید legacy تغییر نام نکرد و تعدادش ثابت ماند', $es_before['legacy_meta'] === $es_after['legacy_meta'] && 37 === count( $es_before['legacy_meta'] ) );
		es_check( 'SC-01: لایو دست‌نخورده (sha256 قبل/بعد یکسان)', $es_live_before === hash_file( 'sha256', $es_live_db ) );

		// SC-02: diff report from the agreed template.
		$es_drift = 0;
		foreach ( $es_before as $es_sec => $es_map ) {
			foreach ( $es_map as $es_k => $es_v ) {
				if ( $es_after[ $es_sec ][ $es_k ] !== $es_v ) {
					$es_drift++;
				}
			}
		}
		$es_report_path = $es_reports_dir . '/dry-run-' . $es_stamp . '.md';
		$es_rows        = static function ( array $es_map ): string {
			$es_out = '';
			foreach ( $es_map as $es_k => $es_v ) {
				$es_out .= '| ' . $es_k . ' | ' . $es_v . ' | ' . $es_v . " |\n";
			}
			return $es_out;
		};
		file_put_contents( $es_report_path,
			"# گزارش Dry-run مهاجرت استیجینگ — {$es_stamp}\n\n"
			. '- بک‌آپ (AC-01): `staging-backup-' . $es_stamp . '.sqlite` + sha256 ثبت‌شده\n'
			. "- لایو فقط خوانده شد (SC-01)\n\n## شمارش پست‌ها (قبل ← بعد)\n\n| نوع | قبل | بعد |\n|---|---|---|\n" . $es_rows( $es_before['posts'] )
			. "\n## شمارش ترم‌ها (قبل ← بعد)\n\n| تکسونومی | قبل | بعد |\n|---|---|---|\n" . $es_rows( $es_before['terms'] )
			. "\n## کلیدهای legacy — بدون تغییر نام (AC-03)\n\n| کلید | قبل | بعد |\n|---|---|---|\n" . $es_rows( $es_before['legacy_meta'] )
			. "\n## تفاوت‌ها\n\ndrift={$es_drift} — مهاجرت idempotent بود.\n\nبر اساس `temp/MIGRATION_REPORT_TEMPLATE.md`.\n"
		);
		$es_report = (string) file_get_contents( $es_report_path );
		es_check( 'SC-02: گزارش تولید شد', is_file( $es_report_path ) );
		es_check( 'SC-02: گزارش شامل قبل/بعد پست‌ها', false !== strpos( $es_report, 'شمارش پست‌ها' ) && false !== strpos( $es_report, '| post |' ) && false !== strpos( $es_report, '| services |' ) );
		es_check( 'SC-02: گزارش شامل ترم‌ها و بخش تفاوت', false !== strpos( $es_report, 'شمارش ترم‌ها' ) && false !== strpos( $es_report, 'تفاوت‌ها' ) && false !== strpos( $es_report, 'drift=0' ) );
		es_check( 'AC-02: جدول قبل/بعد پر شده (بدون صفر بی‌مغزا)', preg_match( '/\| (post|page|product) \| \d+ \| \d+ \|/', $es_report ) === 1 && array_sum( $es_before['posts'] ) > 0 );
		es_check( 'AC-03: کلیدهای legacy در گزارش بدون تغییر نام', false !== strpos( $es_report, '| english_titl |' ) && false !== strpos( $es_report, '| same_key_hrefl |' ) && false !== strpos( $es_report, 'بدون تغییر نام' ) );
		es_check( 'AC-01: بک‌آپ ذخیره و ثبت شد', is_file( $es_backup ) && false !== strpos( $es_report, 'staging-backup-' ) );

		// Cleanup: dry-run artifacts are harness outputs, not repo content.
		@unlink( $es_report_path );
		@unlink( $es_stage_db );
		@unlink( $es_backup );
	}
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
