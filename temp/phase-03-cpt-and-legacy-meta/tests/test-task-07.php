<?php
/** Task 3.7 — shared admin columns and product-category thumbnail. */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_postmeta'] = array(
	101 => array( 'same_key_hrefl' => '', 'number2-viewc' => '', 'number-viewc' => '77' ),
	102 => array( 'same_key_hrefl' => 'sku-lang', 'number2-viewc' => '10', 'gs1_id' => 'GTIN-123', 'national_code' => '' ),
);
$GLOBALS['es_phase3_termmeta'] = array( 5 => array( 'thumbnail' => 0 ) );
if ( ! function_exists( 'add_action' ) ) { function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_actions'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
if ( ! function_exists( 'add_filter' ) ) { function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_filters'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $post_id, $key, $single = false ) { return $GLOBALS['es_phase3_postmeta'][ $post_id ][ $key ] ?? ''; } }
if ( ! function_exists( 'get_term_meta' ) ) { function get_term_meta( $term_id, $key, $single = false ) { return $GLOBALS['es_phase3_termmeta'][ $term_id ][ $key ] ?? ''; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( '__' ) ) { function __( $value, $domain = 'default' ) { return $value; } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }

$class_file = ES_REPO_ROOT . '/theme/inc/Admin/ListTables.php';
$available = is_file( $class_file ) && str_contains( (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Theme.php' ), 'new ListTables()' );
if ( $available ) { require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php'; require_once $class_file; }
$columns = array();
foreach ( array( 'post', 'page', 'product', 'projects' ) as $post_type ) { $columns[ $post_type ] = $available ? \ErfanSanat\Theme\Admin\ListTables::columns_for_post_type( array( 'title' => 'Title' ), $post_type ) : array(); }
$empty_view = $available ? \ErfanSanat\Theme\Admin\ListTables::render_post_column( 'post', 'number2-viewc', 101 ) : '';
$empty_hreflang = $available ? \ErfanSanat\Theme\Admin\ListTables::render_post_column( 'post', 'same_key_hrefl', 101 ) : '';
$product_code = $available ? \ErfanSanat\Theme\Admin\ListTables::render_post_column( 'product', 'gs1_id', 102 ) : '';
$empty_national = $available ? \ErfanSanat\Theme\Admin\ListTables::render_post_column( 'product', 'national_code', 102 ) : '';
$term_columns = $available ? \ErfanSanat\Theme\Admin\ListTables::product_category_columns( array( 'name' => 'Name' ) ) : array();
ob_start(); if ( $available ) { \ErfanSanat\Theme\Admin\ListTables::render_product_category_column( '', 'primary-image', 5 ); } $empty_image = (string) ob_get_clean();
$sortable = $available ? \ErfanSanat\Theme\Admin\ListTables::sortable_columns_for_post_type( array(), 'post' ) : array();

check( 'hreflang, views and product ID columns are registered on the appropriate lists', fn() => $available && isset( $columns['post']['same_key_hrefl'], $columns['post']['number2-viewc'], $columns['page']['same_key_hrefl'], $columns['product']['same_key_hrefl'], $columns['product']['number2-viewc'], $columns['product']['gs1_id'], $columns['product']['national_code'], $columns['projects']['same_key_hrefl'] ) );
check( 'empty legacy metadata renders a dash and view count falls back to number-viewc', fn() => '&mdash;' === $empty_hreflang && '77' === $empty_view );
check( 'product identifiers render escaped values and sortable views remain enabled', fn() => 'GTIN-123' === $product_code && '&mdash;' === $empty_national && isset( $sortable['number2-viewc'] ) );
check( 'product category image column uses thumbnail term meta and handles empty images', fn() => isset( $term_columns['primary-image'] ) && '&mdash;' === $empty_image );
summary();
