<?php
/** Task 5.2 — schema-driven home1 hero preserving the static DOM/JS contract. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_p5_terms'] = array(
	(object) array( 'name' => 'دسته زنده یک', 'description' => 'توضیح زنده یک', 'count' => 12, 'term_id' => 10 ),
	(object) array( 'name' => 'دسته زنده دو', 'description' => 'توضیح زنده دو', 'count' => 8, 'term_id' => 11 ),
);
function get_terms( $args ) { return $GLOBALS['es_p5_terms']; }
function get_term_link( $term ) { return 'https://example.test/product-category/' . (int) $term->term_id . '/'; }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function number_format_i18n( $value, $decimals = 0 ) { return (string) $value; }
function get_template_directory_uri() { return 'https://example.test/theme'; }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Query/HomeQueries.php';
require_once ES_REPO_ROOT . '/theme/inc/Sections/HomeSectionMarkup.php';
require_once ES_REPO_ROOT . '/tools/harness/dom-diff.php';
$schema = (array) json_decode( (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/hero.json' ), true );
$args = array( 'section' => 'hero', 'data' => array( 'content_json' => (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/hero.json' ) ) );
$warnings = array();
set_error_handler( static function ( $severity, $message ) use ( &$warnings ) { $warnings[] = $message; return true; } );
ob_start(); require ES_REPO_ROOT . '/theme/template-parts/home/section-hero.php'; $rendered = (string) ob_get_clean();
$reference = (string) file_get_contents( ES_REPO_ROOT . '/reference/static-mirror/index.html' );
$start = strpos( $reference, '<section id="home"' );
$hero_end = false === $start ? false : strpos( $reference, '</section>', $start );
$reference_hero = false === $hero_end ? '' : substr( $reference, $start, $hero_end + strlen( '</section>' ) - $start );
$similarity = \ErfanSanat\Harness\DomDiff::similarity( $reference_hero, $rendered );

$source = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/home/section-hero.php' );
$scene_ids = array(); preg_match_all( '/data-home1-scene-img="(\\d+)"/', $rendered, $scene_ids );
$scene_buttons = array(); preg_match_all( '/data-home1-scene-btn="(\\d+)"/', $rendered, $scene_buttons );
check( 'ساختار tag/class هیرو با static reference حداقل ۹۹٪ منطبق است', fn() => $similarity >= 99.0 && str_contains( $rendered, 'data-home1-hero-frame="true"' ) && str_contains( $rendered, 'data-home1-categories-grid="true"' ) );
check( 'فایل section فقط renderer دارد و کپی/لینک‌ها از داده JSON در Schema می‌آیند', fn() => ! preg_match( '/[\x{0600}-\x{06FF}]/u', $source ) && isset( $schema['T002']['value'], $schema['A046']['value'] ) );
$custom_values = $schema; $custom_values['T002']['value'] = 'عنوان آزمایشی از داشبورد';
$args['data']['content_json'] = json_encode( $custom_values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
ob_start(); require ES_REPO_ROOT . '/theme/template-parts/home/section-hero.php'; $edited = (string) ob_get_clean();
check( 'ویرایش محتوای Schema در خروجی دیده می‌شود و دسته‌ها از product_cat واقعی می‌آیند', fn() => str_contains( $edited, 'عنوان آزمایشی از داشبورد' ) && str_contains( $edited, 'دسته زنده یک' ) && str_contains( $edited, 'href="https://example.test/product-category/10/"' ) );
$video_key = '';
foreach ( $schema as $key => $value ) { if ( 'src' === ( $value['attribute'] ?? '' ) && str_contains( (string) ( $value['value'] ?? '' ), 'hero-video.mp4' ) ) { $video_key = $key; break; } }
$without_video = $schema; $without_video[ $video_key ]['value'] = '';
$args['data']['content_json'] = json_encode( $without_video, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
ob_start(); require ES_REPO_ROOT . '/theme/template-parts/home/section-hero.php'; $empty_video = (string) ob_get_clean();
restore_error_handler();
check( 'صحنه‌ها ایندکس پیوسته دارند، video poster/preload حفظ است و در نبود video کنترل پخش حذف می‌شود', fn() => count( $scene_ids[1] ) === 5 && $scene_ids[1] === array( '0', '1', '2', '3', '4' ) && count( $scene_buttons[1] ) === 5 && str_contains( $rendered, 'poster="https://example.test/theme/assets/img/hero.jpg"' ) && str_contains( $rendered, 'preload="metadata"' ) && ! str_contains( $empty_video, '<video' ) && ! str_contains( $empty_video, 'data-home1-play-trigger="true"' ) && empty( $warnings ) );
summary();
