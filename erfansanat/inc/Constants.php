<?php
/**
 * Theme constants.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ES_THEME_SLUG' ) ) {
	define( 'ES_THEME_SLUG', 'erfansanat' );
}

if ( ! defined( 'ES_THEME_DIR' ) ) {
	define( 'ES_THEME_DIR', trailingslashit( get_template_directory() ) );
}

if ( ! defined( 'ES_THEME_URI' ) ) {
	define( 'ES_THEME_URI', trailingslashit( get_template_directory_uri() ) );
}

if ( ! defined( 'ES_VERSION' ) ) {
	$es_theme = wp_get_theme( ES_THEME_SLUG );
	define( 'ES_VERSION', $es_theme->exists() ? (string) $es_theme->get( 'Version' ) : '0.1.0' );
	unset( $es_theme );
}

if ( ! defined( 'ES_ASSETS_DIR' ) ) {
	define( 'ES_ASSETS_DIR', ES_THEME_DIR . 'assets/' );
}

if ( ! defined( 'ES_ASSETS_URI' ) ) {
	define( 'ES_ASSETS_URI', ES_THEME_URI . 'assets/' );
}

if ( ! defined( 'ES_INC_DIR' ) ) {
	define( 'ES_INC_DIR', ES_THEME_DIR . 'inc/' );
}
