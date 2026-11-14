<?php
/**
 * P08-T025 — post-audit hardening: packaging must come from the committed
 * state (drift + zip↔git parity), SC-02 clean-install proof is repeatable,
 * template-loop contract (no global pollution), e2e artifacts untracked.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-025.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

ob_start();

es_task( 'P08-T025', 'سخت‌سازی پس از ممیزی نهایی' );

$es_root  = ES_ROOT;
$es_pack  = $es_root . '/temp/tools/package-theme.sh';
$es_vci   = $es_root . '/temp/tools/verify-clean-install.sh';
$es_zip   = $es_root . '/temp/dist/erfansanat.zip';
$es_check = $es_root . '/temp/RELEASE_CHECKLIST.md';
$es_task  = __FILE__;

$es_read = static function ( $p ) {
	return is_file( $p ) ? (string) file_get_contents( $p ) : '';
};

// ── Task doc + dependency ─────────────────────────────────────────────────────
$es_doc = $es_read( dirname( $es_task ) . '/../tasks/task-025.md' );
es_check( 'مستند تسک T025 با وابستگی P08-T024', '' !== $es_doc && false !== strpos( $es_doc, 'P08-T024' ) );

// ── AC-01: packaging from committed state ────────────────────────────────────
$es_pack_src = $es_read( $es_pack );
es_check( 'AC-01: قرارداد نام ثابت temp/dist/erfansanat.zip', false !== strpos( $es_pack_src, 'erfansanat.zip' ) && false !== strpos( $es_pack_src, 'P01-T012' ) );
es_check( 'AC-01: حفاظ drift — ردّ بسته‌گیری از درخت غیرcommitted', false !== strpos( $es_pack_src, 'status --porcelain -- erfansanat/' ) && false !== strpos( $es_pack_src, 'exit 3' ) );
es_check( 'AC-01: پاریتی زیپ ↔ git ls-files', false !== strpos( $es_pack_src, 'ls-files erfansanat/' ) && false !== strpos( $es_pack_src, 'parity mismatch' ) );

// ── AC-02: repeatable clean-install proof ────────────────────────────────────
$es_vci_src = $es_read( $es_vci );
es_check( 'AC-02: اسکریپت نصب تمیز موجود و اجرایی', is_file( $es_vci ) && is_executable( $es_vci ) && false !== strpos( $es_vci_src, 'temp/dist/erfansanat.zip' ) );
es_check( 'AC-02: پاک‌سازی کامل پوشه دیتابیس (دام فایل‌های نقطه‌دار)', false !== strpos( $es_vci_src, 'rm -rf "$WP/wp-content/database"' ) );
es_check( 'AC-02: نصب + زیپ + فعال‌سازی تم در اسکریپت', false !== strpos( $es_vci_src, 'core install' ) && false !== strpos( $es_vci_src, 'unzip -qo' ) && false !== strpos( $es_vci_src, 'theme activate erfansanat' ) );
es_check( 'AC-02: اثبات رندر — resolve قالب front-page', false !== strpos( $es_vci_src, 'front-page.php' ) && false !== strpos( $es_vci_src, 'front_len' ) );

// ── Release zip: exists, pure root, parity with the working tree ─────────────
es_check( 'زیپ انتشار موجود است', is_file( $es_zip ) );
$es_ziparr = new ZipArchive();
$es_opened = true === $es_ziparr->open( $es_zip );
es_check( 'زیپ باز می‌شود (ZipArchive سالم)', $es_opened );

$es_zip_files = array();
if ( $es_opened ) {
	for ( $i = 0; $i < $es_ziparr->numFiles; $i++ ) {
		$es_name = (string) $es_ziparr->getNameIndex( $i );
		if ( '' === $es_name || '/' === substr( $es_name, -1 ) ) {
			continue; // directory entries
		}
		$es_zip_files[] = $es_name;
	}
	$es_ziparr->close();
}
$es_zip_files = array_values( array_unique( $es_zip_files ) );
sort( $es_zip_files );

$es_bad_root = array();
foreach ( $es_zip_files as $es_name ) {
	if ( 0 !== strpos( $es_name, 'erfansanat/' ) || false !== strpos( $es_name, 'static-mirror/' ) || false !== strpos( $es_name, '/.git/' ) ) {
		$es_bad_root[] = $es_name;
	}
}
es_check( 'AC-01(زیپ): ریشه فقط erfansanat/ و بدون آشغال', array() === $es_bad_root );

// Parity: zip file set === working-tree erfansanat/ file set (drift guard keeps
// the tree committed, so this equals `git ls-files erfansanat/`).
$es_fs_files = array();
$es_iter = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( ES_THEME . '/', FilesystemIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ( $es_iter as $es_file ) {
	$es_path = str_replace( '\\', '/', (string) $es_file->getPathname() );
	$es_rel  = 'erfansanat/' . substr( $es_path, strlen( rtrim( ES_THEME, '/' ) ) + 1 );
	if ( false !== strpos( $es_rel, '.DS_Store' ) || false !== strpos( $es_rel, '__MACOSX' ) ) {
		continue;
	}
	$es_fs_files[] = $es_rel;
}
$es_fs_files = array_values( array_unique( $es_fs_files ) );
sort( $es_fs_files );

$es_only_zip = array_diff( $es_zip_files, $es_fs_files );
$es_only_fs  = array_diff( $es_fs_files, $es_zip_files );
es_check( 'AC-01(زیپ): پاریتی کامل زیپ ↔ درخت قالب', $es_opened && array() === $es_only_zip && array() === $es_only_fs );
es_check( 'زیپ: حداقل ۲۰۰ فایل', count( $es_zip_files ) >= 200 && count( $es_fs_files ) >= 200 );

// ── AC-03: template-loop contract — no global pollution ──────────────────────
// the_post() switches $GLOBALS['post']; it is allowed only in the three
// main-loop templates. Everything else loops arrays with explicit IDs.
$es_allow = array( 'index.php', 'single-projects.php', 'woocommerce/single-product.php' );
$es_viol  = array();
$es_setup = array();
$es_php_iter = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( ES_THEME . '/', FilesystemIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ( $es_php_iter as $es_file ) {
	if ( 'php' !== strtolower( (string) $es_file->getExtension() ) ) {
		continue;
	}
	$es_path = (string) $es_file->getPathname();
	$es_rel  = str_replace( '\\', '/', substr( $es_path, strlen( rtrim( ES_THEME, '/' ) ) + 1 ) );
	foreach ( preg_split( '/\r?\n/', (string) file_get_contents( $es_path ) ) as $es_line ) {
		if ( ! str_contains( $es_line, 'the_post(' ) ) {
			continue;
		}
		$es_trim = ltrim( $es_line );
		$es_is_comment = '' !== $es_trim && ( '*' === $es_trim[0] || '/' === $es_trim[0] || '#' === $es_trim[0] );
		if ( $es_is_comment || false !== strpos( $es_line, '->the_post(' ) ) {
			continue;
		}
		if ( ! in_array( $es_rel, $es_allow, true ) ) {
			$es_viol[] = $es_rel;
		}
	}
	if ( false !== strpos( (string) file_get_contents( $es_path ), 'setup_postdata(' ) ) {
		$es_setup[] = $es_rel;
	}
}
es_check( 'AC-03: هیچ the_post() bare خارج از main-loop', array() === $es_viol );
$es_setup_unbalanced = array();
foreach ( $es_setup as $es_rel ) {
	$es_src = (string) file_get_contents( rtrim( ES_THEME, '/' ) . '/' . $es_rel );
	if ( false === strpos( $es_src, 'wp_reset_postdata(' ) ) {
		$es_setup_unbalanced[] = $es_rel;
	}
}
es_check( 'AC-03: setup_postdata همیشه با wp_reset_postdata متوازن', array() === $es_setup_unbalanced && in_array( 'inc/Core/SmartFilters.php', $es_setup, true ) );

// ── AC-04: e2e artifacts stay untracked ──────────────────────────────────────
$es_gitignore = $es_read( $es_root . '/.gitignore' );
es_check( 'AC-04: .gitignore آرتیفکت‌های e2e را نادیده می‌گیرد', false !== strpos( $es_gitignore, 'temp/e2e-report.md' ) && false !== strpos( $es_gitignore, 'temp/e2e-state.txt' ) );

// ── Checklist references the automated SC-02 proof ───────────────────────────
$es_cl = $es_read( $es_check );
es_check( 'چک‌لیست: ارجاع به verify-clean-install.sh و حفاظ‌های بسته‌بندی', false !== strpos( $es_cl, 'verify-clean-install.sh' ) && false !== strpos( $es_cl, 'پاریتی' ) );

// ── Version single-source-of-truth ───────────────────────────────────────────
preg_match( '/^Version:\s*(.+)$/m', (string) file_get_contents( ES_THEME . '/style.css' ), $es_m );
$es_const = $es_read( ES_THEME . '/inc/Constants.php' );
es_check( 'نسخه: style.css منبع یگانه حقیقت ES_VERSION', isset( $es_m[1] ) && trim( $es_m[1] ) === '0.1.0' && false !== strpos( $es_const, "get( 'Version' )" ) );

ob_end_flush();
es_summary();
