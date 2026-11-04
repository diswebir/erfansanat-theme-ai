<?php
/** Task 4.5 — city and project-type dynamic tabs. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_phase4_terms_queries'] = 0;
$GLOBALS['es_phase4_transients'] = array();
$GLOBALS['es_phase4_term_fixture'] = array(
	'project_type' => array( (object) array( 'term_id' => 31, 'taxonomy' => 'project_type', 'name' => 'تونل نوری' ), (object) array( 'term_id' => 32, 'taxonomy' => 'project_type', 'name' => 'المان‌های شهری' ) ),
	'city' => array( (object) array( 'term_id' => 41, 'taxonomy' => 'city', 'name' => 'اصفهان' ), (object) array( 'term_id' => 42, 'taxonomy' => 'city', 'name' => 'تهران' ) ),
);
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value, $protocols = null ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function get_transient( $key ) { return $GLOBALS['es_phase4_transients'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl = 0 ) { $GLOBALS['es_phase4_transients'][ $key ] = $value; return true; }
function get_terms( $args ) { ++$GLOBALS['es_phase4_terms_queries']; $taxonomies = (array) ( $args['taxonomy'] ?? array() ); $out = array(); foreach ( $taxonomies as $taxonomy ) { $out = array_merge( $out, $GLOBALS['es_phase4_term_fixture'][ $taxonomy ] ?? array() ); } return $out; }
function get_term_link( $term, $taxonomy = '' ) { return 'https://example.test/' . $taxonomy . '/' . $term->term_id . '/'; }
require_once ES_REPO_ROOT . '/theme/inc/Nav/Icons.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/NavTerms.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/ProjectsMegaWalker.php';
$html = \ErfanSanat\Theme\Nav\ProjectsMegaWalker::render();
$initial_queries = $GLOBALS['es_phase4_terms_queries'];
$GLOBALS['es_phase4_terms_queries'] = 0;
$GLOBALS['es_phase4_term_fixture']['project_type'] = array();
$GLOBALS['es_phase4_transients'] = array();
\ErfanSanat\Theme\Nav\NavTerms::reset();
$city_only = \ErfanSanat\Theme\Nav\ProjectsMegaWalker::render();
$city_queries = $GLOBALS['es_phase4_terms_queries'];
$GLOBALS['es_phase4_terms_queries'] = 0;
$GLOBALS['es_phase4_term_fixture'] = array( 'project_type' => array(), 'city' => array() );
$GLOBALS['es_phase4_transients'] = array();
\ErfanSanat\Theme\Nav\NavTerms::reset();
$empty = \ErfanSanat\Theme\Nav\ProjectsMegaWalker::render();
$js = (string) file_get_contents( ES_REPO_ROOT . '/theme/assets/js/main.js' );
check( 'تمام شهرها و نوع‌پروژه‌ها در تب و پنل حاضرند', fn() => substr_count( $html, 'data-mega-proj-tab=' ) === 2 && substr_count( $html, 'data-mega-proj-panel=' ) === 2 && str_contains( $html, 'اصفهان' ) && str_contains( $html, 'تونل نوری' ) );
check( 'hrefها مستقیماً از get_term_link ساخته شده‌اند', fn() => str_contains( $html, 'https://example.test/project_type/31/' ) && str_contains( $html, 'https://example.test/city/41/' ) );
check( 'تبِ بدون ترم حذف می‌شود و در نبود هر دو منو خالی است', fn() => substr_count( $city_only, 'data-mega-proj-tab=' ) === 1 && ! str_contains( $city_only, 'data-mega-proj-tab="type"' ) && '' === $empty );
check( 'هوک‌های tab پروژه با main.js هماهنگ‌اند و ترم‌ها با یک query کش می‌شوند', fn() => str_contains( $js, '[data-mega-proj-tab]' ) && str_contains( $js, '[data-mega-proj-panel]' ) && $initial_queries === 1 && $city_queries === 1 && $GLOBALS['es_phase4_terms_queries'] === 1 );
summary();
