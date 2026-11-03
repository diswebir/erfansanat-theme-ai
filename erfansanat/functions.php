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

// Global helpers (es_option) — P02-T002.
require_once ES_INC_DIR . 'Helpers.php';

\ErfanSanat\Theme\Core\Theme::boot();
\ErfanSanat\Theme\Core\DeBloat::boot();
\ErfanSanat\Theme\Core\AssetManager::boot();
\ErfanSanat\Theme\Core\PostTypes::boot();

// Custom admin dashboard — P02-T003.
\ErfanSanat\Theme\Admin\ThemeOptions::boot();

// Dashboard option tabs — P02-T010+.
\ErfanSanat\Theme\Options\Tabs\HeaderTab::boot();
\ErfanSanat\Theme\Options\Tabs\MegaMenuTab::boot();
\ErfanSanat\Theme\Options\Tabs\FooterTab::boot();
\ErfanSanat\Theme\Options\Tabs\BrandTab::boot();
\ErfanSanat\Theme\Options\Tabs\ArchiveTab::boot();
\ErfanSanat\Theme\Admin\SectionMetabox::boot();
\ErfanSanat\Theme\Sections\Renderer::boot();
