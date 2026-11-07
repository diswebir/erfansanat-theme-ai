<?php
/**
 * Removes nonessential WordPress frontend assets and head noise.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme;

defined('ABSPATH') || exit;

/**
 * De-bloats only the public-facing request; wp-admin assets remain untouched.
 */
final class DeBloat
{
    /**
     * Register frontend-only cleanup.
     */
    public static function register(): void
    {
        if (!function_exists('is_admin') || is_admin()) {
            return;
        }

        add_action('wp_enqueue_scripts', [self::class, 'dequeue_default_assets'], 100);

        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
        remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
    }

    /**
     * Dequeue core block/global styles and emoji assets after core enqueues them.
     */
    public static function dequeue_default_assets(): void
    {
        foreach (
            [
                'wp-block-library',
                'wp-block-library-theme',
                'global-styles',
                'wp-global-styles-placeholder',
                'wp-block-styles-placeholder',
                'classic-theme-styles',
                'wp-emoji-styles',
            ] as $handle
        ) {
            wp_dequeue_style($handle);
        }

        foreach (['wp-emoji-release', 'wp-emoji'] as $handle) {
            wp_dequeue_script($handle);
        }
    }
}
