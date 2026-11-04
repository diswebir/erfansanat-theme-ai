<?php
/**
 * Erfan Sanat Official Theme Bootstrap & Global Template Helpers.
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('ERFANSANAT_THEME_VERSION', '1.0.0');
define('ERFANSANAT_THEME_DIR', get_template_directory());
define('ERFANSANAT_THEME_URI', get_template_directory_uri());

require_once ERFANSANAT_THEME_DIR . '/inc/Autoloader.php';

\ErfanSanat\Theme\Autoloader::register(ERFANSANAT_THEME_DIR . '/inc');

if (!function_exists('es_asset_uri')) {
    /**
     * Return full public URI to a theme asset inside assets/.
     */
    function es_asset_uri(string $relativePath): string
    {
        return \ErfanSanat\Theme\Helpers\AssetHelper::uri($relativePath);
    }
}

if (!function_exists('es_image_uri')) {
    /**
     * Return full public URI to a theme media file inside assets/images/.
     */
    function es_image_uri(string $imageRelativePath): string
    {
        return \ErfanSanat\Theme\Helpers\AssetHelper::imageUri($imageRelativePath);
    }
}

if (!function_exists('es_option')) {
    /** Read a theme dashboard option (cached, with defaults fallback). */
    function es_option(string $key, mixed $default = null): mixed
    {
        return \ErfanSanat\Theme\Helpers\Options::get($key, $default);
    }
}

if (!function_exists('es_get_sections')) {
    /** @return array<int,array<string,mixed>> */
    function es_get_sections(int $post_id): array { return \ErfanSanat\Theme\Admin\SectionManager\SectionRenderer::getSections($post_id); }
}
if (!function_exists('es_render_sections')) {
    function es_render_sections(int $post_id): string { return \ErfanSanat\Theme\Admin\SectionManager\SectionRenderer::render($post_id); }
}

\ErfanSanat\Theme\Core\Plugin::instance()->boot();
