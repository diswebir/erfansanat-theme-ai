<?php
/** Task 4.12 — static DOM parity, phase aggregate, and warning-free render gate. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$warnings = array();
set_error_handler( function ( $severity, $message ) use ( &$warnings ) { $warnings[] = $message; return true; } );
require_once ES_REPO_ROOT . '/tools/harness/dom-diff.php';
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Woo/HeaderCart.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/LegacyMarkup.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/HeaderMarkup.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/FooterMarkup.php';
$topbar = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/announcement.php' );
$topbar = (string) preg_replace( '/<\?php.*?\?>/s', '', $topbar );
$static_header = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/static-header.html' );
$mobile = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/mobile-menu.html' );
$rendered_header = \ErfanSanat\Theme\Nav\HeaderMarkup::render( trim( $topbar ) . $static_header . '<div data-mobile-menu="true" aria-hidden="true" class="hidden">' . $mobile . '</div>' );
ob_start(); require ES_REPO_ROOT . '/theme/template-parts/header/cart-modal.php'; $rendered_header .= (string) ob_get_clean();
$reference = (string) file_get_contents( ES_REPO_ROOT . '/reference/static-mirror/index.html' );
$header_start = strpos( $reference, '<header data-navbar-header="true"' );
$aside_end = false === $header_start ? false : strpos( $reference, '</aside>', strpos( $reference, 'data-mobile-sidebar="true"', $header_start ) );
$reference_header = false === $aside_end ? '' : substr( $reference, $header_start, $aside_end + 8 - $header_start );
$cart_start = strpos( $reference, '<div data-cart-modal="true"' );
if ( false !== $cart_start ) {
	preg_match_all( '/<div\b[^>]*>|<\/div\s*>/i', substr( $reference, $cart_start ), $div_tags, PREG_OFFSET_CAPTURE );
	$depth = 0;
	foreach ( $div_tags[0] as $tag ) {
		$depth += str_starts_with( $tag[0], '</' ) ? -1 : 1;
		if ( 0 === $depth ) { $reference_header .= substr( $reference, $cart_start, $cart_start + $tag[1] + strlen( $tag[0] ) - $cart_start ); break; }
	}
}
$static_footer = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/footer/static-footer.html' );
$rendered_footer = \ErfanSanat\Theme\Nav\FooterMarkup::render( $static_footer );
$footer_start = strpos( $reference, '<footer' );
$footer_end = false === $footer_start ? false : strpos( $reference, '</footer>', $footer_start );
$reference_footer = false === $footer_end ? '' : substr( $reference, $footer_start, $footer_end + 9 - $footer_start );
$header_similarity = \ErfanSanat\Harness\DomDiff::similarity( $reference_header, $rendered_header );
$footer_similarity = \ErfanSanat\Harness\DomDiff::similarity( $reference_footer, $rendered_footer );
$phase_result_path = ES_REPO_ROOT . '/tools/harness/last-phase-result.json';
$phase_result = is_file( $phase_result_path ) ? json_decode( (string) file_get_contents( $phase_result_path ), true ) : array();
restore_error_handler();
check( 'انطباق ساختاری header و footer حداقل ۹۹٪ است', fn() => $header_similarity >= 99.0 && $footer_similarity >= 99.0 );
check( 'تمام ۱۱ تست پیشین فاز با PASS و بدون FAIL/PENDING اجرا شدند', fn() => is_array( $phase_result ) && 11 === ( $phase_result['files'] ?? 0 ) && 45 === ( $phase_result['passed'] ?? 0 ) && 0 === ( $phase_result['failed'] ?? -1 ) && 0 === ( $phase_result['pending'] ?? -1 ) );
check( 'رندر header/footer بدون PHP warning انجام شد', fn() => empty( $warnings ) && '' !== $rendered_header && '' !== $rendered_footer );
summary();
