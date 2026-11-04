<?php
/** Task 2.11 — section registry schemas and post-meta state store. */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : ''; } }
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $post_id, $key, $single = false ) { return $GLOBALS['es_section_meta'][ $post_id ][ $key ] ?? ( $single ? '' : array() ); } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $post_id, $key, $value ) { $GLOBALS['es_section_meta'][ $post_id ][ $key ] = $value; return true; } }
if ( ! function_exists( 'apply_filters' ) ) { function apply_filters( $tag, $value ) { foreach ( $GLOBALS['es_section_filters'][ $tag ] ?? array() as $entry ) { $value = ( $entry['callback'] )( $value ); } return $value; } }
if ( ! function_exists( 'add_filter' ) ) { function add_filter( $tag, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_section_filters'][ $tag ][] = array( 'callback' => $callback, 'priority' => $priority ); return true; } }
if ( ! function_exists( 'remove_filter' ) ) { function remove_filter( $tag, $callback, $priority = 10 ) { foreach ( $GLOBALS['es_section_filters'][ $tag ] ?? array() as $key => $entry ) { if ( $entry['callback'] === $callback && $entry['priority'] === $priority ) { unset( $GLOBALS['es_section_filters'][ $tag ][ $key ] ); return true; } } return false; } }

$root = ES_REPO_ROOT . '/theme/';
$registry_file = $root . 'inc/Sections/Registry.php';
$store_file = $root . 'inc/Sections/Store.php';
$available = is_file( $registry_file ) && is_file( $store_file );
if ( $available ) {
	require_once $registry_file;
	require_once $store_file;
}

echo "== Task 2.11: section registry and store ==\n";
check( 'section registry and store modules exist', fn() => $available );
// The conditional is intentionally outside a namespace; keep aliases explicit.
if ( $available ) {
	$registry = '\\ErfanSanat\\Theme\\Sections\\Registry';
	$store = '\\ErfanSanat\\Theme\\Sections\\Store';
	$home = $registry::get( 'home' );
	$defaults = $store::defaults( 'home' );
	$post_id = 987654321;
	$missing_meta = $store::get( $post_id, 'home' );
	$expected = array( 'hero', 'projects', 'gallery', 'about', 'establishment', 'timeline', 'products', 'catalog', 'services', 'quality', 'support', 'clients', 'blog-preview', 'contact-cta', 'map-contact' );
	$fixed_pages = array( 'about', 'contact', 'faq', 'licenses', 'warranty', 'recruitment' );
	$fixed_pages_defined = true;
	foreach ( $fixed_pages as $page ) { $fixed_pages_defined = $fixed_pages_defined && count( $registry::get( $page ) ) > 0; }
	check( 'homepage defaults to fifteen ordered, enabled sections and fixed-page schemas exist', fn() => array_keys( $home ) === $expected && $defaults['order'] === $expected && $missing_meta['order'] === $expected && count( $defaults['enabled'] ) === 15 && count( array_filter( $defaults['enabled'] ) ) === 15 && $fixed_pages_defined );


	$round_trip = array(
		'order' => array_reverse( $expected ),
		'enabled' => array( 'hero' => false, 'projects' => true ),
		'data' => array( 'hero' => array( 'title' => 'Round-trip headline' ) ),
	);
	$store::save( $post_id, $round_trip, 'home' );
	$loaded = $store::get( $post_id, 'home' );
	check( 'section order and enabled state survive save and load', fn() => $loaded['order'] === array_reverse( $expected ) && false === $loaded['enabled']['hero'] && true === $loaded['enabled']['projects'] && 'Round-trip headline' === ( $loaded['data']['hero']['title'] ?? null ) );

	$filter = static function ( $schemas ) {
			unset( $schemas['home']['projects'] );
			$schemas['home']['late-added'] = array(
				'key' => 'late-added', 'label' => 'Late-added section', 'template' => 'template-parts/home/section-late-added.php',
				'fields' => array(), 'default_data' => array(),
			);
		return $schemas;
	};
	add_filter( 'es_sections_registry', $filter, 10, 2 );
	$drifted = $store::get( $post_id, 'home' );
	remove_filter( 'es_sections_registry', $filter, 10 );
	check( 'new registry section is appended to saved order', fn() => end( $drifted['order'] ) === 'late-added' && isset( $drifted['enabled']['late-added'] ) );
	check( 'removed registry section is ignored without errors', fn() => ! in_array( 'projects', $drifted['order'], true ) && ! isset( $drifted['enabled']['projects'] ) && count( $drifted['order'] ) === 15 );
} else {
	check( 'homepage has fifteen ordered, enabled default sections', fn() => false );
	check( 'section order and enabled state survive save and load', fn() => false );
	check( 'new registry section is appended to saved order', fn() => false );
	check( 'removed registry section is ignored without errors', fn() => false );
}
summary();
