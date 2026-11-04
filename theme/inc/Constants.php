<?php
/**
 * Theme constants — defined once, never redefined.
 *
 * @package ErfanSanat\Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ES_THEME_VERSION' ) ) {
	/**
	 * Theme version, always in sync with the `Version:` header of style.css.
	 */
	define( 'ES_THEME_VERSION', '1.0.0' );
}

if ( ! defined( 'ES_THEME_SLUG' ) ) {
	define( 'ES_THEME_SLUG', 'erfansanat' );
}

if ( ! defined( 'ES_THEME_DIR' ) ) {
	define( 'ES_THEME_DIR', get_template_directory() );
}

if ( ! defined( 'ES_THEME_URI' ) ) {
	define( 'ES_THEME_URI', get_template_directory_uri() );
}

if ( ! defined( 'ES_THEME_ASSETS' ) ) {
	define( 'ES_THEME_ASSETS', ES_THEME_URI . '/assets' );
}

if ( ! defined( 'ES_REWRITE_VERSION' ) ) {
	/**
	 * Bump this (not the theme version) to trigger a one-off rewrite flush.
	 */
	define( 'ES_REWRITE_VERSION', '2' );
}
