<?php
/** Enqueue the small homepage service-link adapter after the protected main bundle. */

namespace ErfanSanat\Theme\Frontend;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Routes configured service rows without modifying the homepage JS contract. */
final class HomeServiceLinks implements ModuleInterface {

	public function register(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue' ), 31 );
	}

	public static function enqueue(): void {
		if ( ( function_exists( 'is_admin' ) && is_admin() ) || ( function_exists( 'is_front_page' ) && ! is_front_page() ) ) { return; }
		if ( ! function_exists( 'wp_enqueue_script' ) ) { return; }
		$base = function_exists( 'get_template_directory_uri' ) ? get_template_directory_uri() : '';
		$version = defined( 'ES_THEME_VERSION' ) ? ES_THEME_VERSION : '1.0.0';
		wp_enqueue_script( 'es-home-service-links', $base . '/assets/js/home-service-links.js', array( 'es-main' ), $version, true );
	}
}
