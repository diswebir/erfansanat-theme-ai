<?php
/** Task 5.7 — تاریخچه تعاملی. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'timeline', 'template-parts/home/section-timeline.php', 5, 'تاریخچه تعاملی' );
$markup = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/home/static/timeline.html' );
preg_match_all( '/data-home2-history-btn="(\d+)"/', $markup, $history );
$default_data = (array) json_decode( (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/timeline.json' ), true );
$total = ''; foreach ( $default_data as $key => $item ) { if ( 'data-history-total' === ( $item['attribute'] ?? '' ) ) { $total = (string) $item['value']; break; } }
check( 'تعداد گره‌های تاریخچه با data-history-total و selectorهای main.js برابر است', fn() => count( $history[1] ) === (int) $total && count( $history[1] ) === 6 && str_contains( $markup, 'data-history-scroll-track="true"' ) && str_contains( $markup, 'data-home2-history-prev="true"' ) && str_contains( $markup, 'data-home2-history-next="true"' ) );
$timeline_schema = \ErfanSanat\Theme\Sections\Registry::get( 'home' )['timeline'];
$history_rows = (array) ( $timeline_schema['default_data']['history'] ?? array() );
check( 'مراحل تاریخچه در Repeater با شش milestone و محتوای قابل ویرایش تعریف شده‌اند', function () use ( $timeline_schema, $history_rows ) { foreach ( $timeline_schema['fields'] as $field ) { if ( 'history' === ( $field['id'] ?? '' ) ) { return 'repeater' === $field['type'] && 6 === count( $history_rows ) && in_array( 'description', array_column( $field['fields'], 'id' ), true ) && in_array( 'image', array_column( $field['fields'], 'id' ), true ); } } return false; } );
$json = (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/timeline.json' );
$args = array( 'section' => 'timeline', 'data' => array( 'content_json' => $json ) ); ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-timeline.php'; $without_repeater = (string) ob_get_clean();
$args['data']['history'] = $history_rows; ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-timeline.php'; $default_repeater = (string) ob_get_clean();
$timeline_similarity = \ErfanSanat\Harness\DomDiff::similarity( $without_repeater, $default_repeater ); check( 'ردیف‌های پیش‌فرض repeater ساختار ثابت و شش milestone مرجع را حفظ می‌کنند', fn() => $timeline_similarity >= 99.0 && str_contains( $default_repeater, 'data-history-total="6"' ) );
$history_rows[0]['year'] = '۱۳۸۱'; $history_rows[0]['title'] = 'Milestone changed'; $history_rows[0]['description'] = 'Timeline repeater description changed';
$args = array( 'section' => 'timeline', 'data' => array( 'content_json' => $json, 'history' => $history_rows ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-timeline.php'; $edited_history = (string) ob_get_clean();
check( 'Repeater change synchronizes toolbar year, panel copy and historical step count', fn() => str_contains( $edited_history, '۱۳۸۱' ) && str_contains( $edited_history, 'Milestone changed' ) && str_contains( $edited_history, 'Timeline repeater description changed' ) && str_contains( $edited_history, 'data-history-total="6"' ) );
$args['data']['history'] = array_slice( $history_rows, 0, 3 );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-timeline.php'; $short_history = (string) ob_get_clean();
preg_match_all( '/data-home2-history-btn="(\\d+)"/', $short_history, $short_buttons ); preg_match_all( '/data-home2-history-panel="(\\d+)"/', $short_history, $short_panels );
check( 'Repeater row count controls matching history buttons, panels and total', fn() => 3 === count( $short_buttons[1] ) && 3 === count( $short_panels[1] ) && str_contains( $short_history, 'data-history-total="3"' ) && ! str_contains( $short_history, 'data-home2-history-panel="3"' ) );
check( 'jsdom timeline next/previous buttons and wheel event change the active milestone', function () {
	$report_path = ES_REPO_ROOT . '/tools/harness/jscheck-report.json';
	if ( ! is_file( $report_path ) ) { return null; }
	$report = json_decode( (string) file_get_contents( $report_path ), true );
	if ( ! is_array( $report ) ) { return null; }
	$generated = strtotime( (string) ( $report['generatedAt'] ?? '' ) );
	if ( false === $generated || time() - $generated > 3600 || hash_file( 'sha256', ES_REPO_ROOT . '/theme/assets/js/main.js' ) !== ( $report['mainJsSha256'] ?? '' ) ) { return null; }
	$interactions = $report['interactionChecks'] ?? array();
	return empty( $report['errors'] ) && ! empty( $interactions['historyNext'] ) && ! empty( $interactions['historyPrevious'] ) && ! empty( $interactions['historyWheel'] );
});
summary();
