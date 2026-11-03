<?php
/**
 * Erfan Sanat Esfahan theme bootstrap.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

define( 'ERFANSANAT_THEME_VERSION', '0.1.0' );
define( 'ERFANSANAT_THEME_DIR', get_template_directory() );
define( 'ERFANSANAT_THEME_URI', get_template_directory_uri() );

require_once ERFANSANAT_THEME_DIR . '/inc/Autoloader.php';
require_once ERFANSANAT_THEME_DIR . '/inc/helpers.php';

ErfanSanat\Theme\Autoloader::register();

/**
 * Boot the theme after WordPress has loaded the text domain API.
 */
function erfansanat_boot_theme(): void {
	ErfanSanat\Theme\Theme::instance()->register();
}
add_action( 'after_setup_theme', 'erfansanat_boot_theme', 0 );
