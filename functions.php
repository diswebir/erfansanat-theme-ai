<?php
/**
 * erfansanat-theme-ai — bootstrap
 *
 * Non-block theme for erfansanat.com. All heavy lifting lives in inc/
 * under the ErfanSanat\Theme namespace (PSR-4-ish autoloader).
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

define('ERFANSANAT_THEME_VERSION', '1.0.0');
define('ERFANSANAT_THEME_DIR', trailingslashit(get_template_directory()));
define('ERFANSANAT_THEME_URI', trailingslashit(get_template_directory_uri()));

require_once ERFANSANAT_THEME_DIR . 'inc/Autoloader.php';
require_once ERFANSANAT_THEME_DIR . 'inc/helpers.php';

ErfanSanat\Theme\Theme::instance();
