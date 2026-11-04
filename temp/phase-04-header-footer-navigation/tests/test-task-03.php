<?php
/** Task 4.3 — shared footer, contact options, and document close. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_phase4_empty_options'] = false;
function es_option( $key, $default = null ) { return ! empty( $GLOBALS['es_phase4_empty_options'] ) ? '' : $default; }
function home_url( $path = '/' ) { return 'https://example.test/' . ltrim( $path, '/' ); }
function esc_url( $url, $protocols = null ) { return htmlspecialchars( (string) $url, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function sanitize_email( $value ) { return filter_var( (string) $value, FILTER_SANITIZE_EMAIL ); }
function get_post_type_archive_link( $type ) { return 'https://example.test/' . ( 'projects' === $type ? 'projects' : 'shop' ) . '/'; }
function get_term_by( $field, $value, $taxonomy ) { return (object) array( 'slug' => $value, 'taxonomy' => $taxonomy ); }
function get_term_link( $term, $taxonomy = '' ) { return 'https://example.test/' . $taxonomy . '/' . $term->slug . '/'; }
function get_page_by_path( $path, $output = 'OBJECT', $type = 'page' ) { return false; }
function get_option( $key, $default = false ) { return $default; }
require_once ES_REPO_ROOT . '/theme/inc/Nav/LegacyMarkup.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/FooterMarkup.php';
function phase4_footer_signature( string $html ): array {
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
$source = (string) file_get_contents( ES_REPO_ROOT . '/reference/static-mirror/index.html' );
$start = strpos( $source, '<footer' ); $end = strpos( $source, '</footer>', $start ) + 9;
$reference_footer = substr( $source, $start, $end - $start );
$static_footer = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/footer/static-footer.html' );
$rendered = \ErfanSanat\Theme\Nav\FooterMarkup::render( $static_footer );
$reference_footer = preg_replace_callback( '/\bhref=("|\')([^"\']+)\1/i', fn( $m ) => 'href=' . $m[1] . \ErfanSanat\Theme\Nav\LegacyMarkup::resolve( $m[2] ) . $m[1], $reference_footer );
$GLOBALS['es_phase4_empty_options'] = true;
$empty_footer = \ErfanSanat\Theme\Nav\FooterMarkup::render( $static_footer );
$footer_template = (string) file_get_contents( ES_REPO_ROOT . '/theme/footer.php' );
check( 'ساختار تگ‌ها و اتریبیوت‌های فوتر با مرجع برابر است', fn() => phase4_footer_signature( $rendered ) === phase4_footer_signature( $reference_footer ) );
check( 'لینک‌های فوتر از URLهای WordPress ساخته می‌شوند و html ندارند', fn() => ! preg_match( '/\bhref=["\'][^"\']+\.html(?:[?#][^"\']*)?["\']/i', $rendered ) && str_contains( $rendered, 'https://example.test/product_cat/point-light/' ) );
check( 'wp_footer پیش از بسته‌شدن body فراخوانی شده است', fn() => strpos( $footer_template, 'wp_footer();' ) < strpos( $footer_template, '</body>' ) );
check( 'وقتی تنظیمات تماس خالی‌اند، بلوک تماس حذف می‌شود', fn() => ! str_contains( $empty_footer, 'اطلاعات تماس کارخانه' ) && ! str_contains( $empty_footer, 'tel:031-91091011' ) && ! str_contains( $empty_footer, 'info@erfansanat.com' ) );
summary();
