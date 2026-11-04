<?php
/** Task 4.1 — metadata, hreflang, and base JSON-LD. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';

if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_phase4_head_meta'] = array( 51 => array( 'hrefmeta_fa' => 'https://example.test/fa/page/', 'hrefmeta_en' => 'https://example.test/en/page/', 'hrefmeta_ar' => 'https://example.test/ar/page/' ) );
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function get_bloginfo( $key = '' ) { return 'شرکت عرفان صنعت'; }
function get_the_ID() { return 51; }
function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['es_phase4_head_meta'][ $id ][ $key ] ?? ''; }
function wp_get_document_title() { return 'صفحه آزمایشی'; }
function get_permalink() { return 'https://example.test/fa/page/'; }
function get_the_post_thumbnail_url( $id, $size ) { return ''; }
function get_theme_file_uri( $path ) { return 'https://example.test/wp-content/themes/erfansanat' . $path; }
function is_singular() { return true; }
function has_excerpt() { return false; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); }
function wp_kses( $html, $allowed ) { return $html; }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/SEO/JsonLd.php';
require_once ES_REPO_ROOT . '/theme/inc/Frontend/Head.php';

$social = \ErfanSanat\Theme\Frontend\Head::social_tags();
$hreflang = \ErfanSanat\Theme\Frontend\Head::hreflang_tags();
$GLOBALS['es_phase4_head_meta'][51]['hrefmeta_ar'] = 'javascript:alert(1)';
$unsafe_hreflang = \ErfanSanat\Theme\Frontend\Head::hreflang_tags();
$schema = json_decode( \ErfanSanat\Theme\SEO\JsonLd::json(), true );
check( 'OG و Twitter در نبود افزونه SEO رندر می‌شوند', fn() => \ErfanSanat\Theme\Frontend\Head::social_tags_enabled( false ) && str_contains( $social, 'property="og:title"' ) && str_contains( $social, 'name="twitter:card"' ) );
check( 'با فعال‌بودن SEOPress، خروجی social theme غیرفعال است', fn() => ! \ErfanSanat\Theme\Frontend\Head::social_tags_enabled( true ) );
check( 'سه hreflang دقیقاً یک‌بار و با URL متا خروجی می‌شوند', fn() => substr_count( $hreflang, 'rel="alternate"' ) === 3 && substr_count( $hreflang, 'hreflang="fa"' ) === 1 && str_contains( $hreflang, 'https://example.test/en/page/' ) && ! str_contains( $unsafe_hreflang, 'javascript:' ) );
check( 'JSON-LD معتبر و شامل Organization و WebSite است', fn() => is_array( $schema ) && array_column( $schema, '@type' ) === array( 'Organization', 'BreadcrumbList', 'WebSite' ) && str_contains( \ErfanSanat\Theme\SEO\JsonLd::script(), 'id="ese-jsonld-schema"' ) );
summary();
