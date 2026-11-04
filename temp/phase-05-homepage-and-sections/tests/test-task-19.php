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
function get_terms( $args ) { ++$GLOBALS['es_p5_terms_calls']; return array( (object) array( 'term_id' => 7, 'name' => 'Lighting', 'count' => 9 ) ); }
class WP_Query {
	public array $posts = array();
	public function __construct( array $args ) {
		$GLOBALS['es_p5_query_args'][] = $args;
		for ( $i = 1; $i <= (int) $args['posts_per_page']; ++$i ) { $this->posts[] = (object) array( 'ID' => $i, 'post_type' => $args['post_type'] ); }
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
$module = new HomeQueries();
$module->register();
HomeQueries::on_save_post( 1, (object) array( 'post_type' => 'projects' ), true );
check( 'save/delete/term hooks ثبت‌اند و invalidation همه variantهای cache را پاک می‌کند', fn() => in_array( 'save_post', $GLOBALS['es_p5_actions'], true ) && in_array( 'deleted_post', $GLOBALS['es_p5_actions'], true ) && in_array( 'edited_term', $GLOBALS['es_p5_actions'], true ) && in_array( 'set_object_terms', $GLOBALS['es_p5_actions'], true ) && empty( $GLOBALS['es_p5_transients'] ) && count( $GLOBALS['es_p5_deleted'] ) >= 48 );
summary();
