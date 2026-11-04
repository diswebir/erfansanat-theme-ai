<?php
/** Task 3.2 — project_type taxonomy registration and public archive contract. */
declare(strict_types=1);

require_once __DIR__ . '/../../../../temp/tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_actions'] = $GLOBALS['es_phase3_actions'] ?? array();
if ( ! function_exists( 'add_action' ) ) { function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_actions'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
$GLOBALS['es_phase3_taxonomies'] = $GLOBALS['es_phase3_taxonomies'] ?? array();
if ( ! function_exists( 'register_taxonomy' ) ) { function register_taxonomy( $taxonomy, $object_type, $args = array() ) { $GLOBALS['es_phase3_taxonomies'][ $taxonomy ] = (object) array_merge( array( 'name' => $taxonomy, 'object_type' => (array) $object_type ), $args ); return $GLOBALS['es_phase3_taxonomies'][ $taxonomy ]; } }
if ( ! function_exists( 'taxonomy_exists' ) ) { function taxonomy_exists( $taxonomy ) { return isset( $GLOBALS['es_phase3_taxonomies'][ $taxonomy ] ); } }
if ( ! function_exists( 'get_term_link' ) ) { function get_term_link( $term, $taxonomy = '' ) { $object = get_taxonomy( $taxonomy ); if ( $term instanceof WP_Error || ! $object || empty( $object->public ) ) { return false; } $slug = is_object( $term ) ? $term->slug : (string) $term; return home_url( '/' . $object->rewrite['slug'] . '/' . rawurlencode( $slug ) . '/' ); } }
if ( ! function_exists( 'get_taxonomy' ) ) { function get_taxonomy( $taxonomy ) { return $GLOBALS['es_phase3_taxonomies'][ $taxonomy ] ?? null; } }
if ( ! function_exists( 'home_url' ) ) { function home_url( $path = '/' ) { return 'https://example.test' . '/' . ltrim( $path, '/' ); } }
if ( ! function_exists( 'is_wp_error' ) ) { function is_wp_error( $value ) { return $value instanceof WP_Error; } }
if ( ! class_exists( 'WP_Error' ) ) { class WP_Error { public function get_error_code() { return 'invalid_term'; } } }
if ( ! function_exists( '__' ) ) { function __( $text, $domain = 'default' ) { return $text; } }

$class_file = ES_REPO_ROOT . '/theme/inc/Content/Taxonomies.php';
$available = is_file( $class_file );
if ( $available ) { require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php'; require_once $class_file; }
if ( $available ) { ( new \ErfanSanat\Theme\Content\Taxonomies() )->register(); foreach ( $GLOBALS['es_phase3_actions']['init'] ?? array() as $action ) { if ( 10 === $action[1] ) { call_user_func( $action[0] ); } } }

check( 'project_type is registered for projects with public REST/admin support', fn() => $available && taxonomy_exists( 'project_type' ) && array( 'projects' ) === get_taxonomy( 'project_type' )->object_type && get_taxonomy( 'project_type' )->hierarchical && get_taxonomy( 'project_type' )->show_in_rest && get_taxonomy( 'project_type' )->show_admin_column );
check( 'project_type term links use /project_type/{slug}/', fn() => '/project_type/bridge-lighting/' === (string) parse_url( (string) get_term_link( 'bridge-lighting', 'project_type' ), PHP_URL_PATH ) );
check( 'project_type rewrite is non-hierarchical and excludes the front base', function () { $taxonomy = get_taxonomy( 'project_type' ); return is_object( $taxonomy ) && array( 'slug' => 'project_type', 'with_front' => false, 'hierarchical' => false ) === $taxonomy->rewrite; } );
check( 'project_type archive routing is public and labels are localized', function () { $taxonomy = get_taxonomy( 'project_type' ); return is_object( $taxonomy ) && ! empty( $taxonomy->public ) && ! empty( $taxonomy->publicly_queryable ) && ! empty( $taxonomy->query_var ) && ! empty( $taxonomy->rewrite['slug'] ) && ! empty( $taxonomy->labels['name'] ) && ! empty( $taxonomy->labels['not_found'] ); } );
summary();
