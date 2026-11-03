<?php
/**
 * P01-T012 — یکپارچگی asset و بسته‌بندی
 * Run: php temp/phase-01-core-and-assets/tests/test-task-012.php
 * Requires: bash temp/tools/package-theme.sh && bash temp/tools/check-assets-http.sh
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T012', 'یکپارچگی asset و بسته‌بندی' );

es_check( 'target: temp/tools/package-theme.sh', es_file( 'temp/tools/package-theme.sh', ES_ROOT ) );

$zip_path = ES_ROOT . '/temp/dist/erfansanat.zip';
es_check( 'SC-01: زیپ بسته‌بندی موجود است', is_file( $zip_path ) );

$zip_ok = false;
$entries = array();
if ( is_file( $zip_path ) ) {
	$zip = new ZipArchive();
	if ( true === $zip->open( $zip_path ) ) {
		$zip_ok = true;
		for ( $i = 0; $i < $zip->numFiles; $i++ ) {
			$name = $zip->getNameIndex( $i );
			$entries[] = $name;
			if ( 0 !== strpos( $name, 'erfansanat/' ) ) {
				$zip_ok = false;
			}
			if ( false !== strpos( $name, 'static-mirror' ) || false !== strpos( $name, 'node_modules' ) || 0 === strpos( $name, 'temp/' ) ) {
				$zip_ok = false;
			}
		}
		$zip->close();
	}
}
es_check( 'AC-01: زیپ فقط erfansanat/ است', $zip_ok );
es_check( 'AC-01: style.css در زیپ هست', in_array( 'erfansanat/style.css', $entries, true ) );
es_check( 'AC-01: index.php در زیپ هست', in_array( 'erfansanat/index.php', $entries, true ) );

// AC-02: CSS url(...) assets resolve inside the theme.
$css = (string) file_get_contents( ES_THEME . '/assets/css/styles.css' );
preg_match_all( '/url\(\s*[\'"]?(\.\.\/[^\'")]+)[\'"]?\s*\)/', $css, $matches );
$resolve_ok = true;
foreach ( $matches[1] as $rel ) {
	if ( 0 === strpos( $rel, 'data:' ) ) {
		continue;
	}
	$path = realpath( ES_THEME . '/assets/css/' . $rel );
	$resolve_ok = $resolve_ok && $path && is_file( $path );
}
es_check( 'AC-02: url(...)های CSS resolve می‌شوند', $resolve_ok );

// AC-03/SC-02: HTTP report has zero broken assets.
$report_path = ES_ROOT . '/temp/asset-http-report.json';
$report_ok = false;
if ( is_file( $report_path ) ) {
	$report = json_decode( (string) file_get_contents( $report_path ), true );
	$report_ok = ! empty( $report['ok'] ) && ( $report['count'] ?? 0 ) > 0;
}
es_check( 'AC-03/SC-02: asset شکسته صفر است', $report_ok );

// SC-01: extract packaged theme into an isolated root and validate/activate it.
if ( es_load_wp() && is_file( $zip_path ) ) {
	$extract_root = ES_ROOT . '/temp/dist/package-test-' . getmypid();
	mkdir( $extract_root, 0777, true );
	$zip = new ZipArchive();
	$extracted = true === $zip->open( $zip_path ) && $zip->extractTo( $extract_root );
	if ( $extracted ) {
		$zip->close();
	}
	$packaged_dir = $extract_root . '/erfansanat';
	$renamed_dir  = $extract_root . '/erfansanat-packaged';
	if ( is_dir( $packaged_dir ) ) {
		rename( $packaged_dir, $renamed_dir );
	}

	$previous_stylesheet      = get_option( 'stylesheet' );
	$previous_template        = get_option( 'template' );
	$previous_stylesheet_root = get_option( 'stylesheet_root' );
	$previous_template_root   = get_option( 'template_root' );

	register_theme_directory( $extract_root );
	wp_clean_themes_cache();
	$packaged = wp_get_theme( 'erfansanat-packaged' );
	es_check( 'SC-01: پوسته بسته‌بندی‌شده معتبر است', $packaged->exists() && ! $packaged->errors() );

	switch_theme( 'erfansanat-packaged' );
	es_check( 'SC-01: فعال‌سازی پوسته بسته‌بندی‌شده', get_option( 'stylesheet' ) === 'erfansanat-packaged' );

	// Restore the original theme AND its registered roots; switch_theme alone can
	// leave stylesheet_root/template_root pointing at the temporary package root.
	switch_theme( $previous_stylesheet );
	update_option( 'stylesheet_root', $previous_stylesheet_root );
	update_option( 'template_root', $previous_template_root );
	update_option( 'template', $previous_template );
	delete_option( 'theme_mods_erfansanat-packaged' );
	wp_clean_themes_cache();

	// Remove the isolated extraction root so it can never shadow the real theme.
	$rrmdir = function ( $dir ) use ( &$rrmdir ) {
		if ( ! is_dir( $dir ) ) {
			return;
		}
		foreach ( scandir( $dir ) as $item ) {
			if ( '.' === $item || '..' === $item ) {
				continue;
			}
			$path = $dir . '/' . $item;
			is_dir( $path ) ? $rrmdir( $path ) : unlink( $path );
		}
		rmdir( $dir );
	};
	$rrmdir( $extract_root );
} else {
	es_check( 'SC-01: بارگذاری WP/زیپ برای فعال‌سازی', false );
}

es_summary();
