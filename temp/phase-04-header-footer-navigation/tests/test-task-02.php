<?php
/** Task 4.2 — shared header static-structure contract. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_phase4_header_options'] = array();
function es_option( $key, $default = null ) { return $GLOBALS['es_phase4_header_options'][ $key ] ?? $default; }
function home_url( $path = '/' ) { return 'https://example.test/' . ltrim( $path, '/' ); }
function esc_url( $url, $protocols = null ) { return htmlspecialchars( (string) $url, ENT_QUOTES, 'UTF-8' ); }
function get_post_type_archive_link( $type ) { return 'https://example.test/' . ( 'projects' === $type ? 'projects' : 'shop' ) . '/'; }
function get_term_by( $field, $value, $taxonomy ) { return (object) array( 'slug' => $value, 'taxonomy' => $taxonomy ); }
function get_term_link( $term, $taxonomy = '' ) { return 'https://example.test/' . $taxonomy . '/' . $term->slug . '/'; }
function get_page_by_path( $path, $output = 'OBJECT', $type = 'page' ) { return false; }
require_once ES_REPO_ROOT . '/theme/inc/Nav/LegacyMarkup.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/HeaderMarkup.php';

function phase4_tags_and_attributes( string $html ): array {
	preg_match_all( '/<([a-z][a-z0-9:-]*)\b([^>]*)>/i', $html, $matches, PREG_SET_ORDER );
	$out = array();
	foreach ( $matches as $match ) {
		$attrs = array();
		preg_match_all( '/\b([a-z_:][a-z0-9:._-]*)(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+))?/i', $match[2], $am );
		foreach ( $am[1] as $name ) { $attrs[] = strtolower( $name ); }
		sort( $attrs );
		$out[] = strtolower( $match[1] ) . '|' . implode( ',', $attrs );
	}
	return $out;
}
$ref = (string) file_get_contents( ES_REPO_ROOT . '/reference/static-mirror/index.html' );
$ref_start = strpos( $ref, '<header' );
$ref_end = strpos( $ref, '</aside>', strpos( $ref, 'data-mobile-sidebar', $ref_start ) );
$ref_fragment = substr( $ref, $ref_start, $ref_end + 8 - $ref_start );
$topbar_file = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/announcement.php' );
$topbar = preg_replace( '/<\?php.*?\?>/s', '', $topbar_file );
$theme_markup = $topbar . (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/static-header.html' ) . (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/mobile-menu.html' );
$theme_markup = \ErfanSanat\Theme\Nav\HeaderMarkup::render( $theme_markup );
$normalized_ref = preg_replace_callback( '/\bhref=("|\')([^"\']+)\1/i', fn( $m ) => 'href=' . $m[1] . \ErfanSanat\Theme\Nav\LegacyMarkup::resolve( $m[2] ) . $m[1], $ref_fragment );
$theme_sig = phase4_tags_and_attributes( $theme_markup );
$ref_sig = phase4_tags_and_attributes( $normalized_ref );
$similarity = count( $theme_sig ) === count( $ref_sig ) ? 100 : ( 100 * min( count( $theme_sig ), count( $ref_sig ) ) / max( count( $theme_sig ), count( $ref_sig ) ) );
$announcement = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/announcement.php' );
check( 'ساختار تگ و اتریبیوت هدر با مرجع منطبق است', fn() => $similarity >= 99 );
check( 'لینک HTML قدیمی در خروجی بازنویسی باقی نمی‌ماند', fn() => ! preg_match( '/\bhref=["\'][^"\']+\.html(?:[?#][^"\']*)?["\']/i', $theme_markup ) );
check( 'custom و data-initial-qty بازمانده حذف می‌شوند', fn() => ! str_contains( $theme_markup, 'custom=' ) && ! str_contains( $theme_markup, 'data-initial-qty' ) );
check( 'toggle داشبورد کل نوار بالایی را مشروط می‌کند', fn() => str_contains( $announcement, 'HeaderTab::announcement_enabled()' ) );
check( 'نام دسترس‌پذیر منوی اصلی حفظ شده است', fn() => str_contains( $theme_markup, 'aria-label="منوی اصلی"' ) );
$GLOBALS['es_phase4_header_options'] = array( 'header.announcement.text' => '<b>خبر</b>', 'header.announcement.url' => 'javascript:alert(1)' );
$unsafe_announcement = \ErfanSanat\Theme\Nav\HeaderMarkup::render( $topbar . (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/static-header.html' ) );
check( 'محتوا و URL نوار اعلان امن هستند', fn() => str_contains( $unsafe_announcement, '&lt;b&gt;خبر&lt;/b&gt;' ) && ! str_contains( $unsafe_announcement, 'javascript:' ) );
summary();
