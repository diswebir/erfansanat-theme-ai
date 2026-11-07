<?php
/**
 * Enqueues the local static-mirror assets for public theme templates.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme;

defined('ABSPATH') || exit;

/**
 * Serves the shared stylesheet and interaction script from the theme directory.
 */
final class AssetManager
{
    private const STYLE_HANDLE = 'erfansanat-theme-styles';
    private const SCRIPT_HANDLE = 'erfansanat-theme-script';
    private const STYLE_FILE = 'assets/css/styles.css';
    private const SCRIPT_FILE = 'assets/js/main.js';

    /**
     * Attach the shared assets to the frontend enqueue lifecycle.
     */
    public static function register(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_frontend_assets'], 20);
    }

    /**
     * Enqueue assets used by all public pages represented in the static template map.
     */
    public static function enqueue_frontend_assets(): void
    {
        if (is_admin() || (function_exists('is_feed') && is_feed())) {
            return;
        }

        self::enqueue_style();
        self::enqueue_script();
    }

    /**
     * Enqueue the compiled RTL stylesheet with a file modification cache key.
     */
    private static function enqueue_style(): void
    {
        $path = get_theme_file_path(self::STYLE_FILE);
        $url = get_theme_file_uri(self::STYLE_FILE);
        if (!is_file($path) || false === $url) {
            return;
        }

        wp_enqueue_style(
            self::STYLE_HANDLE,
            $url,
            [],
            self::asset_version($path),
            'all'
        );
    }

    /**
     * Enqueue the vanilla-JavaScript interactions in the footer.
     */
    private static function enqueue_script(): void
    {
        $path = get_theme_file_path(self::SCRIPT_FILE);
        $url = get_theme_file_uri(self::SCRIPT_FILE);
        if (!is_file($path) || false === $url) {
            return;
        }

        wp_enqueue_script(
            self::SCRIPT_HANDLE,
            $url,
            [],
            self::asset_version($path),
            true
        );
        wp_localize_script(
            self::SCRIPT_HANDLE,
            'eseAjax',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ese_public_filters'),
            ]
        );
    }

    /**
     * Generate a cache version that changes whenever the asset changes.
     *
     * @param string $path Absolute asset path.
     * @return string Asset modification time or theme version fallback.
     */
    private static function asset_version(string $path): string
    {
        $modified = filemtime($path);
        if (false !== $modified) {
            return (string) $modified;
        }

        return (string) wp_get_theme()->get('Version');
    }
}
