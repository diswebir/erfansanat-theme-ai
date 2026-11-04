<?php
/**
 * Loads the local Vazirmatn family in the WordPress administration UI and editors.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

defined('ABSPATH') || exit;

/** Applies the theme's local Persian/Arabic font across admin screens. */
final class Typography
{
    private const STYLE_HANDLE = 'erfansanat-admin-vazirmatn';
    private const STYLE_FILE = 'assets/css/admin-vazirmatn.css';
    private const FONT_FILES = [
        'assets/fonts/vazirmatn-arabic.woff2',
        'assets/fonts/vazirmatn-latin-ext.woff2',
        'assets/fonts/vazirmatn-latin.woff2',
    ];

    /** Register the stylesheet for all admin pages, including theme and editor screens. */
    public static function register(): void
    {
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_admin_styles'], 1);
    }

    /** Enqueue local font faces and admin typography with asset-based cache busting. */
    public static function enqueue_admin_styles(): void
    {
        $cssPath = get_theme_file_path(self::STYLE_FILE);
        $cssUrl = get_theme_file_uri(self::STYLE_FILE);
        if (!is_file($cssPath) || false === $cssUrl) {
            return;
        }

        $versions = [filemtime($cssPath) ?: 0];
        foreach (self::FONT_FILES as $fontFile) {
            $fontPath = get_theme_file_path($fontFile);
            if (is_file($fontPath)) {
                $versions[] = filemtime($fontPath) ?: 0;
            }
        }

        wp_enqueue_style(
            self::STYLE_HANDLE,
            $cssUrl,
            [],
            (string) max($versions)
        );
    }
}
