<?php
/** Task 3.1 — native projects post type registration. */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_actions'] = $GLOBALS['es_phase3_actions'] ?? array();
$GLOBALS['es_phase3_post_types'] = $GLOBALS['es_phase3_post_types'] ?? array();
if ( ! function_exists( 'add_action' ) ) { function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_actions'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
if ( ! function_exists( '__' ) ) { function __( $text, $domain = 'default' ) { return $text; } }
if ( ! function_exists( 'register_post_type' ) ) { function register_post_type( $post_type, $args = array() ) { $GLOBALS['es_phase3_post_types'][ $post_type ] = (object) array_merge( array( 'name' => $post_type ), $args ); return $GLOBALS['es_phase3_post_types'][ $post_type ]; } }
if ( ! function_exists( 'post_type_exists' ) ) { function post_type_exists( $post_type ) { return isset( $GLOBALS['es_phase3_post_types'][ $post_type ] ); } }
if ( ! function_exists( 'is_post_type_public' ) ) { function is_post_type_public( $post_type ) { return ! empty( $GLOBALS['es_phase3_post_types'][ $post_type ]->public ); } }
if ( ! function_exists( 'get_post_type_object' ) ) { function get_post_type_object( $post_type ) { return $GLOBALS['es_phase3_post_types'][ $post_type ] ?? null; } }
if ( ! function_exists( 'home_url' ) ) { function home_url( $path = '/' ) { return 'https://example.test' . '/' . ltrim( $path, '/' ); } }
if ( ! function_exists( 'get_post_type_archive_link' ) ) { function get_post_type_archive_link( $post_type ) { $object = get_post_type_object( $post_type ); if ( ! $object || empty( $object->has_archive ) ) { return false; } return home_url( '/' . ( $object->rewrite['slug'] ?? $post_type ) . '/' ); } }

$class_file = ES_REPO_ROOT . '/theme/inc/Content/PostTypes.php';
$available = is_file( $class_file ) && is_file( ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php' );
if ( $available ) {
	require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
	require_once $class_file;
}
$theme_source = (string) @file_get_contents( ES_REPO_ROOT . '/theme/inc/Theme.php' );

// Each focused check executes the registered init callback against WordPress-shaped mocks.
$register_projects = static function () use ( $available ) {
	if ( ! $available ) { return; }
	$module = new \ErfanSanat\Theme\Content\PostTypes();
	$module->register();
	foreach ( $GLOBALS['es_phase3_actions']['init'] ?? array() as $action ) {
		if ( 10 === $action[1] ) { call_user_func( $action[0] ); }
	}
};
$register_projects();

check( 'projects post type is registered, public and wired into Theme', fn() => $available && post_type_exists( 'projects' ) && is_post_type_public( 'projects' ) && str_contains( $theme_source, "new PostTypes()" ) );
check( 'projects archive URL resolves to /projects/', fn() => '/projects/' === (string) parse_url( (string) get_post_type_archive_link( 'projects' ), PHP_URL_PATH ) );
check( 'projects rewrite uses only the expected slug and with_front false', function () { $object = get_post_type_object( 'projects' ); return is_object( $object ) && array( 'slug' => 'projects', 'with_front' => false ) === $object->rewrite && false === $object->hierarchical; } );
check( 'projects supports the complete required feature set and REST', function () { $object = get_post_type_object( 'projects' ); $supports = $object->supports ?? array(); return is_array( $supports ) && array_diff( array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions' ), $supports ) === array() && ! empty( $object->show_in_rest ) && ! empty( $object->map_meta_cap ) && 'post' === $object->capability_type && 'dashicons-screenoptions' === $object->menu_icon && ! empty( $object->labels['name'] ) && ! empty( $object->labels['not_found'] ); } );
summary();
