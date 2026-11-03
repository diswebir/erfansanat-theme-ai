<?php
/**
 * Nav menu locations.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Setup;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the four menu locations from DATA_BLUEPRINT.
 */
final class Menus implements ModuleInterface {

	/**
	 * Location key => Persian label.
	 *
	 * @return array<string,string>
	 */
	public static function locations(): array {
		return array(
			'main-mega-menu'  => __( 'مگامنوی اصلی (دسکتاپ)', 'erfansanat' ),
			'mobile-menu'     => __( 'منوی موبایل', 'erfansanat' ),
			'tolidat-sherkat' => __( 'منوی تولیدات شرکت', 'erfansanat' ),
			'landing'         => __( 'منوی صفحات لندینگ', 'erfansanat' ),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function register(): void {
		add_action( 'after_setup_theme', array( $this, 'register_menus' ), 6 );
	}

	/**
	 * Register the locations.
	 */
	public function register_menus(): void {
		register_nav_menus( self::locations() );
	}
}
