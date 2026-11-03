<?php
/**
 * Erfan Sanat theme bootstrap.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/Constants.php';
require_once ES_INC_DIR . 'Autoloader.php';

\ErfanSanat\Theme\Autoloader::register();

\ErfanSanat\Theme\Core\Theme::boot();
\ErfanSanat\Theme\Core\DeBloat::boot();
\ErfanSanat\Theme\Core\AssetManager::boot();
