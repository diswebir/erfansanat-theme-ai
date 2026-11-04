<?php
/** Task 3.6 — project admin columns, filters, sorting and pagination. */
declare(strict_types=1);

require_once __DIR__ . '/../../../../temp/tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_actions'] = $GLOBALS['es_phase3_actions'] ?? array();
$GLOBALS['es_phase3_filters'] = $GLOBALS['es_phase3_filters'] ?? array();
$GLOBALS['es_phase3_admin'] = true;
$GLOBALS['es_phase3_meta_calls'] = 0;
$GLOBALS['es_phase3_mock_terms'] = array(
	'city' => array( (object) array( 'slug' => 'esfahan', 'name' => 'اصفهان' ) ),
	'project_type' => array( (object) array( 'slug' => 'bridge-lighting', 'name' => 'نورپردازی پل' ) ),
);
$GLOBALS['es_phase3_postmeta'] = array( 88 => array( 'finish-pro-time' => '1404/01/01', 'number2-viewc' => '250', 'number-viewc' => '230', 'same_key_hrefl' => 'prj-88' ) );
if ( ! function_exists( 'add_action' ) ) { function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_actions'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
if ( ! function_exists( 'add_filter' ) ) { function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_filters'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
if ( ! function_exists( 'is_admin' ) ) { function is_admin() { return ! empty( $GLOBALS['es_phase3_admin'] ); } }
if ( ! function_exists( 'get_terms' ) ) { function get_terms( $args = array() ) { return $GLOBALS['es_phase3_mock_terms'][ $args['taxonomy'] ?? '' ] ?? array(); } }
if ( ! function_exists( 'is_wp_error' ) ) { function is_wp_error( $value ) { return $value instanceof WP_Error; } }
if ( ! function_exists( 'get_the_terms' ) ) { function get_the_terms( $post_id, $taxonomy ) { return $GLOBALS['es_phase3_mock_terms'][ $taxonomy ] ?? array(); } }
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $post_id, $key = '', $single = false ) { $GLOBALS['es_phase3_meta_calls']++; if ( '' === $key ) { return $GLOBALS['es_phase3_postmeta'][ $post_id ] ?? array(); } return $GLOBALS['es_phase3_postmeta'][ $post_id ][ $key ] ?? ''; } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : ''; } }
if ( ! function_exists( 'sanitize_title' ) ) { function sanitize_title( $value ) { return strtolower( preg_replace( '/[^a-z0-9_-]/i', '', (string) $value ) ); } }
if ( ! function_exists( 'wp_unslash' ) ) { function wp_unslash( $value ) { return is_array( $value ) ? array_map( 'wp_unslash', $value ) : stripslashes( (string) $value ); } }
if ( ! function_exists( '__' ) ) { function __( $value, $domain = 'default' ) { return $value; } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $value, $domain = 'default' ) { return esc_html( $value ); } }

$class_file = ES_REPO_ROOT . '/theme/inc/Admin/ListTables.php';
$available = is_file( $class_file );
if ( $available ) { require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php'; require_once $class_file; }
if ( $available ) { ( new \ErfanSanat\Theme\Admin\ListTables() )->register(); }

final class EsPhase3MockQuery {
	public array $vars = array( 'post_type' => 'projects', 'paged' => 3, 'posts_per_page' => 20, 'tax_query' => array() );
	public function get( $key ) { return $this->vars[ $key ] ?? null; }
	public function set( $key, $value ) { $this->vars[ $key ] = $value; }
	public function is_main_query() { return true; }
}
$columns = $available ? \ErfanSanat\Theme\Admin\ListTables::columns_for_post_type( array( 'cb' => '<input>', 'title' => 'Title' ), 'projects' ) : array();
$_GET['city'] = 'esfahan';
$_GET['project_type'] = 'bridge-lighting';
$query = new EsPhase3MockQuery();
if ( $available ) { \ErfanSanat\Theme\Admin\ListTables::filter_project_query( $query ); }
$filters_html = $available ? \ErfanSanat\Theme\Admin\ListTables::render_project_filters( 'projects', 'top' ) : '';

check( 'projects admin columns include city, project type, completion, views and hreflang', fn() => $available && array_diff( array( 'city', 'project_type', 'finish-pro-time', 'number2-viewc', 'same_key_hrefl' ), array_keys( $columns ) ) === array() );
check( 'city and project-type dropdowns preserve selected options and append taxonomy filters', fn() => str_contains( $filters_html, 'name="city"' ) && str_contains( $filters_html, 'value="esfahan" selected="selected"' ) && str_contains( $filters_html, 'name="project_type"' ) && count( $query->vars['tax_query'] ) === 3 );
check( 'completion and views columns are sortable with numeric view ordering', function () use ( $available ) { if ( ! $available ) { return false; } $sortable = \ErfanSanat\Theme\Admin\ListTables::sortable_project_columns( array() ); $query = new EsPhase3MockQuery(); $query->vars['orderby'] = 'number2-viewc'; \ErfanSanat\Theme\Admin\ListTables::filter_project_query( $query ); return isset( $sortable['finish-pro-time'], $sortable['number2-viewc'] ) && 'number2-viewc' === $query->vars['meta_key'] && 'meta_value_num' === $query->vars['orderby']; } );
check( 'filtered project queries retain current page and page size', fn() => 3 === $query->vars['paged'] && 20 === $query->vars['posts_per_page'] );
summary();
