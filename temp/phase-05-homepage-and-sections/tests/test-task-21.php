<?php
/** Task 5.21 — phase gate using the PHP-WASM request-budget mock, not SQL instrumentation. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 5.21: گیت فاز ۵: تست رندر و بودجه درخواست mock صفحه اصلی ==\n";
echo "   phase 5 | size S | files: 2 | pass conditions: 4\n";

$report_path = ES_REPO_ROOT . '/tools/harness/jscheck-report.json';
$queries_path = ES_REPO_ROOT . '/tools/harness/home-query-report.json';
$report = is_file( $report_path ) ? json_decode( (string) file_get_contents( $report_path ), true ) : null;
$queries = is_file( $queries_path ) ? json_decode( (string) file_get_contents( $queries_path ), true ) : null;
$report_fresh = is_array( $report ) && false !== ( $generated = strtotime( (string) ( $report['generatedAt'] ?? '' ) ) ) && time() - $generated < 3600 && hash_file( 'sha256', ES_REPO_ROOT . '/theme/assets/js/main.js' ) === ( $report['mainJsSha256'] ?? '' ) && hash_file( 'sha256', ES_REPO_ROOT . '/theme/assets/js/home-service-links.js' ) === ( $report['serviceLinksSha256'] ?? '' );

check( '۱۵ سکشن مرجع حفظ شده و سکشن مجازشده‌ی شانزدهم حضور دارد', function () use ( $report, $report_fresh ) {
	if ( ! $report_fresh ) { return null; }
	$similarities = $report['sectionSimilarities'] ?? null;
	if ( ! is_array( $similarities ) || 15 !== count( $similarities ) ) { return false; }
	foreach ( $similarities as $similarity ) { if ( ! is_numeric( $similarity ) || (float) $similarity < 99.0 ) { return false; } }
	return 16 === (int) ( $report['homeSections'] ?? 0 ) && empty( $report['errors'] );
});
check( 'jsdom بدون runtime error است و main.js و adapter ناوبری خدمات را اجرا می‌کند', fn() => $report_fresh && empty( $report['errors'] ) && ! empty( $report['interactionChecks']['services'] ) && ! empty( $report['interactionChecks']['serviceLinkNavigation'] ) && 7 === (int) ( $report['phpMockAssertions']['pass'] ?? -1 ) && 0 === (int) ( $report['phpMockAssertions']['fail'] ?? -1 ) && 0 === (int) ( $report['phpMockAssertions']['pending'] ?? -1 ) );
check( 'بودجه mock حداکثر ۳۰ درخواست سطح WP_Query/get_terms و rerender از cache است', fn() => is_array( $queries ) && ( $queries['measurement'] ?? '' ) === 'mock request-level WP_Query/get_terms calls; not $wpdb SQL' && (int) ( $queries['bounded_request_calls'] ?? PHP_INT_MAX ) <= 30 && ! empty( $queries['cache_hit'] ) && 0 === (int) ( $queries['warnings'] ?? -1 ) && ( $queries['html_sha256'] ?? '' ) === ( $report['htmlSha256'] ?? '' ) );
check( 'تمام ۲۰ فایل قبلی همین اجرای phase سبز بوده‌اند؛ runner این فایل را به‌عنوان final gate اجرا می‌کند', function () use ( $report, $report_fresh ) {
	$result_path = ES_REPO_ROOT . '/tools/harness/last-phase-result.json';
	if ( ! $report_fresh || ! is_file( $result_path ) ) { return null; }
	$result = json_decode( (string) file_get_contents( $result_path ), true );
	if ( ! is_array( $result ) || 20 !== (int) ( $result['files'] ?? 0 ) || 0 !== (int) ( $result['failed'] ?? -1 ) || 0 !== (int) ( $result['pending'] ?? -1 ) || (int) ( $result['passed'] ?? 0 ) < 1 ) { return false; }
	$run_time = strtotime( (string) ( $result['at'] ?? '' ) );
	$js_time = strtotime( (string) ( $report['generatedAt'] ?? '' ) );
	return false !== $run_time && false !== $js_time && $run_time >= $js_time;
});
summary();
