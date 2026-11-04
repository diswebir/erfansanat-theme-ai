<?php
/** Task 3.4 — WooCommerce permalink and taxonomy rewrite compatibility. */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_actions'] = $GLOBALS['es_phase3_actions'] ?? array();
$GLOBALS['es_phase3_filters'] = $GLOBALS['es_phase3_filters'] ?? array();
$GLOBALS['es_phase3_taxonomies'] = $GLOBALS['es_phase3_taxonomies'] ?? array();
$GLOBALS['es_phase3_flush_calls'] = 0;
if ( ! function_exists( 'add_action' ) ) { function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_actions'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
if ( ! function_exists( 'add_filter' ) ) { function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_filters'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
if ( ! function_exists( 'taxonomy_exists' ) ) { function taxonomy_exists( $taxonomy ) { return isset( $GLOBALS['es_phase3_taxonomies'][ $taxonomy ] ); } }
if ( ! function_exists( 'post_type_exists' ) ) { function post_type_exists( $post_type ) { return in_array( $post_type, array( 'product', 'projects' ), true ); } }
if ( ! function_exists( 'register_taxonomy' ) ) { function register_taxonomy( $taxonomy, $object_type, $args = array() ) { $GLOBALS['es_phase3_taxonomies'][ $taxonomy ] = array( 'object_type' => (array) $object_type, 'args' => $args ); return true; } }
if ( ! function_exists( 'flush_rewrite_rules' ) ) { function flush_rewrite_rules( $hard = true ) { $GLOBALS['es_phase3_flush_calls']++; } }
if ( ! function_exists( 'home_url' ) ) { function home_url( $path = '/' ) { return 'https://example.test' . '/' . ltrim( $path, '/' ); } }
if ( ! function_exists( '__' ) ) { function __( $text, $domain = 'default' ) { return $text; } }

$class_file = ES_REPO_ROOT . '/theme/inc/Content/Rewrites.php';
$available = is_file( $class_file );
if ( $available ) { require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php'; require_once $class_file; }
$theme_source = (string) @file_get_contents( ES_REPO_ROOT . '/theme/inc/Theme.php' );
$category_args = $available ? \ErfanSanat\Theme\Content\Rewrites::filter_taxonomy_args( array(), 'product_cat', array( 'product' ) ) : array();
$tag_args = $available ? \ErfanSanat\Theme\Content\Rewrites::filter_taxonomy_args( array(), 'product_tag', array( 'product' ) ) : array();
$product_args = $available ? \ErfanSanat\Theme\Content\Rewrites::filter_product_args( array(), 'product' ) : array();
$GLOBALS['es_phase3_test_tax_args'] = array( 'product_cat' => $category_args, 'product_tag' => $tag_args );
$GLOBALS['es_phase3_test_product_args'] = $product_args;

if ( $available ) {
	$GLOBALS['es_phase3_post_types']['product'] = (object) array( 'name' => 'product', 'public' => true ); // WooCommerce provides this in production.
	$module = new \ErfanSanat\Theme\Content\Rewrites();
	$module->register();
	foreach ( $GLOBALS['es_phase3_actions']['init'] ?? array() as $action ) { if ( 20 === $action[1] ) { call_user_func( $action[0] ); } }
}
$term_link = static function ( $slug, $taxonomy ) { $args = $GLOBALS['es_phase3_test_tax_args'][ $taxonomy ] ?? array(); return home_url( '/' . ( $args['rewrite']['slug'] ?? $taxonomy ) . '/' . $slug . '/' ); };
$product_link = static function ( $slug ) { $rewrite = $GLOBALS['es_phase3_test_product_args']['rewrite'] ?? array(); return home_url( '/' . trim( $rewrite['slug'] ?? '', '/' ) . '/' . $slug . '/' ); };

check( 'product_cat keeps the exact category archive URL', fn() => '/product_cat/strings-and-lighting-decorations/' === (string) parse_url( $term_link( 'strings-and-lighting-decorations', 'product_cat' ), PHP_URL_PATH ) && ( $category_args['rewrite'] ?? array() ) === array( 'slug' => 'product_cat', 'with_front' => true, 'hierarchical' => true ) );
check( 'product_tag uses /product-tag/{slug}/', fn() => '/product-tag/featured/' === (string) parse_url( $term_link( 'featured', 'product_tag' ), PHP_URL_PATH ) && ( $tag_args['rewrite'] ?? array() ) === array( 'slug' => 'product-tag', 'with_front' => false ) );
check( 'product singles use /product/{slug}/', fn() => '/product/beam-light/' === (string) parse_url( $product_link( 'beam-light' ), PHP_URL_PATH ) && ( $product_args['rewrite'] ?? array() ) === array( 'slug' => 'product', 'with_front' => false ) );
check( 'brand registration is conditional, rewrites are hooked, and normal registration never flushes', fn() => $available && str_contains( $theme_source, "new Rewrites()" ) && taxonomy_exists( 'product_brand' ) && ! empty( $GLOBALS['es_phase3_filters']['register_taxonomy_args'] ) && ! empty( $GLOBALS['es_phase3_filters']['register_post_type_args'] ) && 0 === $GLOBALS['es_phase3_flush_calls'] );
summary();
