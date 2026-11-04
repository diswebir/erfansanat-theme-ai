<?php
/**
 * Theme bootstrap for ErfanSanat Theme.
 *
 * @package ErfanSanat\Theme
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/inc/Autoloader.php';

\ErfanSanat\Theme\Autoloader::register(__DIR__ . '/inc');
require_once __DIR__ . '/inc/Helpers.php';
\ErfanSanat\Theme\Theme::register();
