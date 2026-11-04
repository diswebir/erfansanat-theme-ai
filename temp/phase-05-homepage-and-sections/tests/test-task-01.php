<?php
/** Task 5.1 — front-page delegates to the ordered section renderer. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_p5_variant'] = 'home1';
$GLOBALS['es_p5_template_calls'] = array();
$GLOBALS['es_p5_disable_sections'] = false;
$GLOBALS['es_p5_posts_remaining'] = 0;
$warnings = array();
set_error_handler( static function ( $severity, $message ) use ( &$warnings ) { $warnings[] = $message; return true; } );
function es_option( $key, $default = null ) { return 'home_hero_variant' === $key ? $GLOBALS['es_p5_variant'] : $default; }
function get_option( $key, $default = false ) { return 'page_on_front' === $key ? 42 : $default; }
function absint( $value ) { return abs( (int) $value ); }
function get_post_meta( $post_id, $key, $single = false ) {
	if ( ! $GLOBALS['es_p5_disable_sections'] ) { return array(); }
	$keys = array_keys( \ErfanSanat\Theme\Sections\Registry::get( 'home' ) );
	return array( 'order' => $keys, 'enabled' => array_fill_keys( $keys, false ), 'data' => array() );
}
function locate_template( $templates, $load = false, $load_once = true, $args = array() ) {
	$template = is_array( $templates ) ? reset( $templates ) : $templates;
	if ( 'template-parts/home/hero-variants/hero-home2.php' === $template && 'home2' === $GLOBALS['es_p5_variant'] ) { return '/mock/' . $template; }
	if ( 'template-parts/home/hero-variants/hero-home3.php' === $template && 'home3' === $GLOBALS['es_p5_variant'] ) { return '/mock/' . $template; }
	return str_starts_with( (string) $template, 'template-parts/home/section-' ) ? '/mock/' . $template : '';
}
function get_template_part( $slug, $name = null, $args = array() ) {
	$GLOBALS['es_p5_template_calls'][] = array( 'slug' => $slug, 'args' => $args );
	echo '<section data-test-section="' . htmlspecialchars( (string) ( $args['section'] ?? '' ), ENT_QUOTES, 'UTF-8' ) . '"></section>';
}
function get_header() { echo '<!-- header -->'; }
function get_footer() { echo '<!-- footer -->'; }
function have_posts() { return $GLOBALS['es_p5_posts_remaining'] > 0; }
function the_post() { --$GLOBALS['es_p5_posts_remaining']; }
function the_content() { echo '<article>Fallback homepage content</article>'; }
require_once ES_REPO_ROOT . '/theme/inc/Sections/Registry.php';
require_once ES_REPO_ROOT . '/theme/inc/Sections/Store.php';
require_once ES_REPO_ROOT . '/theme/inc/Sections/Renderer.php';
function render_front_page(): string {
	$GLOBALS['es_p5_template_calls'] = array();
	ob_start();
	include ES_REPO_ROOT . '/theme/front-page.php';
	return (string) ob_get_clean();
}
$source = (string) file_get_contents( ES_REPO_ROOT . '/theme/front-page.php' );
$output = render_front_page();
$rendered_order = array_map( static fn( $call ) => $call['args']['section'] ?? '', $GLOBALS['es_p5_template_calls'] );
$expected_order = array_keys( \ErfanSanat\Theme\Sections\Registry::get( 'home' ) );
check( 'front-page فاقد مارک‌آپ سکشن است و فقط رندرر را فراخوانی می‌کند', fn() => ! str_contains( $source, '<section' ) && ! str_contains( $source, '<article' ) && ! str_contains( $source, '<div' ) && str_contains( $source, "Renderer::render( 'home' )" ) );
check( '۱۶ سکشن (با مجوز Task 5.17) به ترتیب دقیق Registry رندر می‌شوند', fn() => 16 === count( $rendered_order ) && $rendered_order === $expected_order && str_contains( $output, 'data-test-section="hero"' ) );
$GLOBALS['es_p5_variant'] = 'home2';
$variant_output = render_front_page();
$hero_call = $GLOBALS['es_p5_template_calls'][0] ?? array();
check( 'home_hero_variant=home2 به قالب هیرو مستقل منتقل می‌شود', fn() => str_contains( $variant_output, 'data-test-section="hero"' ) && 'template-parts/home/hero-variants/hero-home2' === ( $hero_call['slug'] ?? '' ) && 'home2' === ( $hero_call['args']['variant'] ?? '' ) );
$GLOBALS['es_p5_disable_sections'] = true;
$GLOBALS['es_p5_posts_remaining'] = 1;
$empty_output = render_front_page();
check( 'بدون سکشن فعال، محتوای واقعی برگه بدون warning نمایش داده می‌شود', fn() => str_contains( $empty_output, 'Fallback homepage content' ) && 0 === count( $GLOBALS['es_p5_template_calls'] ) && empty( $warnings ) );
restore_error_handler();
summary();
