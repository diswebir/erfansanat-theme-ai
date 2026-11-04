<?php
/** Task 5.19 — bounded cached homepage query helpers. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
if ( ! defined( 'ES_THEME_VERSION' ) ) { define( 'ES_THEME_VERSION', 'test' ); }
if ( ! defined( 'HOUR_IN_SECONDS' ) ) { define( 'HOUR_IN_SECONDS', 3600 ); }
$GLOBALS['es_p5_transients'] = array();
$GLOBALS['es_p5_query_args'] = array();
$GLOBALS['es_p5_terms_calls'] = 0;
$GLOBALS['es_p5_deleted'] = array();
$GLOBALS['es_p5_actions'] = array();
function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_p5_actions'][] = $hook; }
function get_transient( $key ) { return $GLOBALS['es_p5_transients'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl ) { $GLOBALS['es_p5_transients'][ $key ] = $value; return true; }
function delete_transient( $key ) { $GLOBALS['es_p5_deleted'][] = $key; unset( $GLOBALS['es_p5_transients'][ $key ] ); return true; }
function get_terms( $args ) {
	++$GLOBALS['es_p5_terms_calls'];
	if ( empty( $GLOBALS['es_p5_render_fixture'] ) ) { return array( (object) array( 'term_id' => 7, 'name' => 'Lighting', 'count' => 9 ) ); }
	$terms = array(); $limit = max( 1, (int) ( $args['number'] ?? 1 ) );
	for ( $i = 1; $i <= min( 6, $limit ); ++$i ) { $terms[] = (object) array( 'term_id' => 700 + $i, 'name' => 'Fixture category ' . $i, 'slug' => 'fixture-category-' . $i, 'description' => 'Category fixture description', 'count' => 8 + $i ); }
	return $terms;
}
class WP_Query {
	public array $posts = array();
	public function __construct( array $args ) {
		$GLOBALS['es_p5_query_args'][] = $args;
		$limit = (int) $args['posts_per_page'];
		if ( 'projects' === ( $args['post_type'] ?? '' ) && $limit <= 6 && isset( $args['tax_query'][0]['terms'] ) ) {
			$limit = min( $limit, 'tunnel' === $args['tax_query'][0]['terms'] ? 3 : 2 );
		}
		for ( $i = 1; $i <= $limit; ++$i ) { $this->posts[] = (object) array( 'ID' => $i, 'post_type' => $args['post_type'], 'post_title' => 'Fixture ' . $args['post_type'] . ' ' . $i, 'post_excerpt' => 'Fixture excerpt ' . $i ); }
	}
}
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Query/HomeQueries.php';
use ErfanSanat\Theme\Query\HomeQueries;

$projects = HomeQueries::projects( 99 );
$args = $GLOBALS['es_p5_query_args'][0] ?? array();
check( 'کوئری‌های کارت‌ها محدودند و post-meta/term-cache اضافی غیرفعال است', fn() => count( $projects ) === 12 && 12 === ( $args['posts_per_page'] ?? 0 ) && true === ( $args['no_found_rows'] ?? false ) && false === ( $args['update_post_meta_cache'] ?? true ) && false === ( $args['update_post_term_cache'] ?? true ) );
$before = count( $GLOBALS['es_p5_query_args'] );
$again = HomeQueries::projects( 99 );
$categories = HomeQueries::product_categories( 7 );
$categories_again = HomeQueries::product_categories( 7 );
check( 'نتایج WP_Query و product_cat به‌صورت cache-hit تکرار query نمی‌سازند', fn() => $before === count( $GLOBALS['es_p5_query_args'] ) && 1 === $GLOBALS['es_p5_terms_calls'] && $again === $projects && $categories_again === $categories );
HomeQueries::articles( 4 );
HomeQueries::products( 4 );
$types = array_column( array_slice( $GLOBALS['es_p5_query_args'], $before ), 'post_type' );
check( 'helperهای مقالات و محصولات درخواست نوع محتوای صحیح دارند', fn() => in_array( 'post', $types, true ) && in_array( 'product', $types, true ) && count( $categories ) === 1 );
$typed_projects = HomeQueries::projects_by_type( 'tunnel', 99 ); $typed_args = end( $GLOBALS['es_p5_query_args'] ); $typed_count = count( $GLOBALS['es_p5_query_args'] ); HomeQueries::projects_by_type( 'tunnel', 99 ); $invalid_projects = HomeQueries::projects_by_type( 'not-a-tab', 6 );
check( 'پروژه‌های هر تب با taxonomy محدود، query حداکثر ۱۲ ردیفی و cache اختصاصی خوانده می‌شوند', fn() => 12 === count( $typed_projects ) && 12 === ( $typed_args['posts_per_page'] ?? 0 ) && 'projects' === ( $typed_args['post_type'] ?? '' ) && 'tunnel' === ( $typed_args['tax_query'][0]['terms'] ?? '' ) && $typed_count === count( $GLOBALS['es_p5_query_args'] ) - 0 && array() === $invalid_projects );
$module = new HomeQueries();
$module->register();
HomeQueries::on_save_post( 1, (object) array( 'post_type' => 'projects' ), true );
check( 'save/delete/term hooks ثبت‌اند و invalidation همه variantهای cache را پاک می‌کند', fn() => in_array( 'save_post', $GLOBALS['es_p5_actions'], true ) && in_array( 'deleted_post', $GLOBALS['es_p5_actions'], true ) && in_array( 'edited_term', $GLOBALS['es_p5_actions'], true ) && in_array( 'set_object_terms', $GLOBALS['es_p5_actions'], true ) && empty( $GLOBALS['es_p5_transients'] ) && count( $GLOBALS['es_p5_deleted'] ) >= 48 );
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key, $single = false ) { if ( '_es_sections' === $key ) { return $GLOBALS['es_p5_render_state'] ?? false; } return 'finish-pro-time' === $key ? '۱۴۰۴/۰۱/۰۱' : ''; } }
if ( ! function_exists( 'get_the_title' ) ) { function get_the_title( $post ) { return is_object( $post ) ? (string) ( $post->post_title ?? '' ) : 'Fixture ' . (int) $post; } }
if ( ! function_exists( 'get_permalink' ) ) { function get_permalink( $post ) { $id = is_object( $post ) ? (int) ( $post->ID ?? 0 ) : (int) $post; return 'https://example.test/post/' . $id . '/'; } }
if ( ! function_exists( 'get_the_post_thumbnail_url' ) ) { function get_the_post_thumbnail_url( $id, $size = 'large' ) { return 'https://example.test/image/' . (int) $id . '.jpg'; } }
if ( ! function_exists( 'get_the_terms' ) ) { function get_the_terms( $id, $taxonomy ) { return array( (object) array( 'name' => 'Term ' . $taxonomy, 'slug' => 'fixture' ) ); } }
if ( ! function_exists( 'get_the_excerpt' ) ) { function get_the_excerpt( $post ) { return is_object( $post ) ? (string) ( $post->post_excerpt ?? '' ) : 'Fixture excerpt'; } }
if ( ! function_exists( 'get_the_category' ) ) { function get_the_category( $id ) { return array( (object) array( 'name' => 'Updates' ) ); } }
if ( ! function_exists( 'get_the_date' ) ) { function get_the_date( $format = '', $post = null ) { return '۱۴۰۵/۰۸/۰۴'; } }
if ( ! function_exists( 'get_term_link' ) ) { function get_term_link( $term ) { return 'https://example.test/category/'; } }
if ( ! function_exists( 'get_template_directory_uri' ) ) { function get_template_directory_uri() { return 'https://example.test/theme'; } }
if ( ! function_exists( 'locate_template' ) ) { function locate_template( $templates, $load = false, $load_once = true, $args = array() ) { $template = is_array( $templates ) ? reset( $templates ) : $templates; $path = ES_REPO_ROOT . '/theme/' . $template; return is_file( $path ) ? $path : ''; } }
if ( ! function_exists( 'get_template_part' ) ) { function get_template_part( $slug, $name = null, $args = array() ) { $path = ES_REPO_ROOT . '/theme/' . $slug . '.php'; if ( is_file( $path ) ) { $args = is_array( $args ) ? $args : array(); include $path; } } }
require_once ES_REPO_ROOT . '/theme/inc/Sections/Registry.php';
require_once ES_REPO_ROOT . '/theme/inc/Sections/Store.php';
require_once ES_REPO_ROOT . '/theme/inc/Sections/Renderer.php';
require_once ES_REPO_ROOT . '/theme/inc/Sections/HomeSectionMarkup.php';
$GLOBALS['es_p5_render_fixture'] = true;
$GLOBALS['es_p5_render_state'] = \ErfanSanat\Theme\Sections\Store::defaults( 'home' );
$GLOBALS['es_p5_render_state']['data']['catalog']['catalog_file'] = 'https://example.test/catalog.pdf';
$GLOBALS['es_p5_query_args'] = array(); $GLOBALS['es_p5_terms_calls'] = 0; HomeQueries::invalidate();
$warnings = array(); $rendered_templates = array();
set_error_handler( static function ( $severity, $message ) use ( &$warnings ) { $warnings[] = $message; return true; } );
ob_start(); \ErfanSanat\Theme\Sections\Renderer::render( 'home', 42 ); $homepage_once = (string) ob_get_clean();
$query_count_once = count( $GLOBALS['es_p5_query_args'] ); $terms_count_once = $GLOBALS['es_p5_terms_calls'];
ob_start(); \ErfanSanat\Theme\Sections\Renderer::render( 'home', 42 ); $homepage_twice = (string) ob_get_clean();
restore_error_handler();
check( 'رندر یکپارچه تمام ۱۵ سکشن بدون warning انجام می‌شود و مسیرهای query-aware خروجی می‌سازند', fn() => substr_count( $homepage_once, '<section' ) >= 15 && str_contains( $homepage_once, 'data-home2-port-panel="all"' ) && str_contains( $homepage_once, 'Fixture 1' ) && str_contains( $homepage_once, 'data-home2-history-panel="0"' ) && empty( $warnings ) );
check( 'رندر کامل در سقف ۳۰ query می‌ماند و رندر دوم بدون query جدید از cache می‌آید', fn() => $query_count_once > 0 && ( $query_count_once + $terms_count_once ) <= 30 && $query_count_once === count( $GLOBALS['es_p5_query_args'] ) && $terms_count_once === $GLOBALS['es_p5_terms_calls'] && '' !== $homepage_twice );
$query_report = array(
	'home_sections' => substr_count( $homepage_once, '<section' ),
	'bounded_request_calls' => $query_count_once + $terms_count_once,
	'cache_hit' => $query_count_once === count( $GLOBALS['es_p5_query_args'] ) && $terms_count_once === $GLOBALS['es_p5_terms_calls'],
	'warnings' => count( $warnings ),
	'html_sha256' => hash( 'sha256', $homepage_once ),
);
file_put_contents( ES_REPO_ROOT . '/tools/harness/home-query-report.json', json_encode( $query_report, JSON_UNESCAPED_SLASHES ) );
$jscheck_export_request = ES_REPO_ROOT . '/tools/harness/jscheck-export.request';
if ( is_file( $jscheck_export_request ) ) {
	echo "\n<<<ES_HTML_BASE64>>>" . base64_encode( $homepage_once ) . "<<<END_ES_HTML>>>";
	@unlink( $jscheck_export_request );
}
summary();
