<?php
/**
 * P01-T009 — انتقال JS و رسانه‌های پایه
 * Run: php temp/phase-01-core-and-assets/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T009', 'انتقال JS و رسانه‌های پایه' );

$js_src = ES_MIRROR . '/assets/js/main.js';
$js_dst = ES_THEME . '/assets/js/main.js';
es_check( 'target: assets/js/main.js', is_file( $js_dst ) );
es_check( 'AC-01: main.js بدون تغییر کپی شده', is_file( $js_dst ) && hash_file( 'sha256', $js_src ) === hash_file( 'sha256', $js_dst ) );

// Images/video copied byte-for-byte.
$src_images = ES_MIRROR . '/images';
$dst_images = ES_THEME . '/assets/images';
$all_match = is_dir( $dst_images );
$count = 0;
$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $src_images, FilesystemIterator::SKIP_DOTS ) );
foreach ( $iterator as $file ) {
	if ( ! $file->isFile() ) {
		continue;
	}
	$rel = substr( $file->getPathname(), strlen( $src_images ) + 1 );
	$dst = $dst_images . '/' . $rel;
	$count++;
	$all_match = $all_match && is_file( $dst ) && hash_file( 'sha256', $file->getPathname() ) === hash_file( 'sha256', $dst );
}
es_check( 'AC-02: همه تصاویر/ویدیو کپی شده‌اند', $all_match && $count > 0 );
es_check( 'AC-02: ویدیوی هیرو موجود است', is_file( $dst_images . '/hero-video.mp4' ) );
es_check( 'AC-03: لوگو fallback موجود است', is_file( $dst_images . '/ese-logo.svg' ) );

$js = is_file( $js_dst ) ? (string) file_get_contents( $js_dst ) : '';
es_check( 'SC-02: ماژول هیرو ویدیویی در main.js هست', false !== strpos( $js, 'initHeroVideo' ) );

// SC-01: HTTP 200 for JS/images/video.
$report_path = ES_ROOT . '/temp/asset-http-report.json';
$report_ok = false;
if ( is_file( $report_path ) ) {
	$report = json_decode( (string) file_get_contents( $report_path ), true );
	$report_ok = ! empty( $report['ok'] );
	foreach ( $report['results'] ?? array() as $row ) {
		if ( 'assets/js/main.js' === $row['path'] || 'assets/images/hero-video.mp4' === $row['path'] ) {
			$report_ok = $report_ok && 200 === (int) $row['status'];
		}
	}
}
es_check( 'SC-01: assetها HTTP 200 می‌دهند', $report_ok );

es_summary();
