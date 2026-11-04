<?php
/**
 * Test — Task 5.21: گیت فاز ۵: تست رندر و کوئری صفحه اصلی
 * Phase 5: صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-05-homepage-and-sections/tests/test-task-21.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 5.21: گیت فاز ۵: تست رندر و کوئری صفحه اصلی ==\n";
echo "   phase 5 | size S | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// انطباق ۱۵ سکشن
check('انطباق ۱۵ سکشن', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// خطای JS
check('خطای JS روی خروجی کامل PHP-WASM در jsdom', function () {
	$report_path = ES_REPO_ROOT . '/tools/harness/jscheck-report.json';
	$queries_path = ES_REPO_ROOT . '/tools/harness/home-query-report.json';
	if ( ! is_file( $report_path ) || ! is_file( $queries_path ) ) { return null; }
	$report = json_decode( (string) file_get_contents( $report_path ), true );
	$queries = json_decode( (string) file_get_contents( $queries_path ), true );
	if ( ! is_array( $report ) || ! is_array( $queries ) ) { return null; }
	$generated = strtotime( (string) ( $report['generatedAt'] ?? '' ) );
	$main_js_hash = hash_file( 'sha256', ES_REPO_ROOT . '/theme/assets/js/main.js' );
	if ( false === $generated || time() - $generated > 3600 || $main_js_hash !== ( $report['mainJsSha256'] ?? '' ) || ( $queries['html_sha256'] ?? '' ) !== ( $report['htmlSha256'] ?? '' ) ) { return null; }
	return empty( $report['errors'] ) && (int) ( $report['homeSections'] ?? 0 ) >= 15 && 0 === (int) ( $report['phpMockAssertions']['fail'] ?? -1 ) && 0 === (int) ( $report['phpMockAssertions']['pending'] ?? -1 ) && 0 === (int) ( $queries['warnings'] ?? -1 );
});
// کوئری
check('کوئری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
