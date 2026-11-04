<?php
/** Task 2.13 — safe frontend renderer for registered sections. */
declare(strict_types=1);

require_once __DIR__ . '/../../../../temp/tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : ''; } }
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $post_id, $key, $single = false ) { return $GLOBALS['es_section_meta'][ $post_id ][ $key ] ?? ( $single ? '' : array() ); } }
if ( ! function_exists( 'locate_template' ) ) { function locate_template( $templates, $load = false, $load_once = true, $args = array() ) { foreach ( (array) $templates as $template ) { if ( in_array( $template, $GLOBALS['es_renderer_templates'] ?? array(), true ) ) { return '/theme/' . $template; } } return ''; } }
if ( ! function_exists( 'get_template_part' ) ) { function get_template_part( $slug, $name = null, $args = array() ) { $GLOBALS['es_renderer_calls'][] = array( $slug, $args ); if ( in_array( $slug, $GLOBALS['es_renderer_throw'] ?? array(), true ) ) { echo 'partial output'; throw new RuntimeException( 'mock template exception' ); } echo '<section data-rendered="' . htmlspecialchars( $slug, ENT_QUOTES, 'UTF-8' ) . '"></section>'; } }
if ( ! function_exists( 'do_action' ) ) { function do_action( $tag, ...$args ) { foreach ( $GLOBALS['es_renderer_hooks'][ $tag ] ?? array() as $callback ) { $callback( ...$args ); } } }

$root = ES_REPO_ROOT . '/theme/';
$registry_file = $root . 'inc/Sections/Registry.php';
$store_file = $root . 'inc/Sections/Store.php';
$renderer_file = $root . 'inc/Sections/Renderer.php';
$available = is_file( $registry_file ) && is_file( $store_file ) && is_file( $renderer_file );
if ( $available ) {
	require_once $registry_file;
	require_once $store_file;
	require_once $renderer_file;
}

echo "== Task 2.13: frontend section renderer ==\n";
check( 'section renderer module exists', fn() => $available );
if ( $available ) {
	$renderer = '\\ErfanSanat\\Theme\\Sections\\Renderer';
	$post_id = 987650013;
	$all_keys = array_keys( \ErfanSanat\Theme\Sections\Registry::get( 'home' ) );
	$state = array( 'order' => $all_keys, 'enabled' => array_fill_keys( $all_keys, false ), 'data' => array(), 'available' => array() );
	$state['enabled']['hero'] = false;
	$GLOBALS['es_section_meta'][ $post_id ]['_es_sections'] = $state;
	$GLOBALS['es_renderer_templates'] = array( 'template-parts/home/section-hero.php' );
	$GLOBALS['es_renderer_calls'] = array();
	ob_start();
	$renderer::render( 'home', $post_id );
	$disabled_output = ob_get_clean();
	check( 'disabled sections produce zero markup', fn() => '' === $disabled_output && array() === $GLOBALS['es_renderer_calls'] );

	$state['order'] = array( 'hero', 'projects', 'about' );
	$state['enabled'] = array( 'hero' => true, 'projects' => true, 'about' => true );
	$state['data']['projects'] = array( 'title' => 'Projects from Store' );
	$GLOBALS['es_section_meta'][ $post_id ]['_es_sections'] = $state;
	$GLOBALS['es_renderer_templates'] = array( 'template-parts/home/section-projects-intro.php', 'template-parts/home/section-about.php' );
	$GLOBALS['es_renderer_throw'] = array( 'template-parts/home/section-projects-intro' );
	$GLOBALS['es_renderer_calls'] = array();
	$warnings = array();
	set_error_handler( static function ( $severity, $message ) use ( &$warnings ) { $warnings[] = $message; return true; } );
	ob_start();
	$renderer::render( 'home', $post_id );
	$missing_output = ob_get_clean();
	restore_error_handler();
	$missing_calls = $GLOBALS['es_renderer_calls'];
	check( 'missing or failed template is silent and later sections continue rendering', fn() => empty( $warnings ) && count( $missing_calls ) === 2 && ! str_contains( $missing_output, 'partial output' ) && ! str_contains( $missing_output, 'section-projects-intro' ) && str_contains( $missing_output, 'section-about' ) );

	$state['order'] = array( 'about', 'projects', 'hero' );
	$GLOBALS['es_section_meta'][ $post_id ]['_es_sections'] = $state;
	$GLOBALS['es_renderer_templates'] = array( 'template-parts/home/section-projects-intro.php', 'template-parts/home/section-about.php', 'template-parts/home/section-hero.php' );
	$GLOBALS['es_renderer_throw'] = array();
	$GLOBALS['es_renderer_calls'] = array();
	ob_start();
	$renderer::render( 'home', $post_id );
	$order_output = ob_get_clean();
	$slugs = array_column( $GLOBALS['es_renderer_calls'], 0 );
	$passed_data = $GLOBALS['es_renderer_calls'][1][1]['data']['title'] ?? '';
	check( 'output follows stored order and template args carry section data', fn() => $slugs === array( 'template-parts/home/section-about', 'template-parts/home/section-projects-intro', 'template-parts/home/section-hero' ) && false !== strpos( $order_output, 'section-about' ) && $passed_data === 'Projects from Store' );
} else {
	check( 'disabled sections produce zero markup', fn() => false );
	check( 'missing template is silent and later sections continue rendering', fn() => false );
	check( 'output follows stored order and template args carry section data', fn() => false );
}
summary();
