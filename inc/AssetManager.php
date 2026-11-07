<?php
/**
 * AssetManager — enqueues the static theme's CSS/JS/fonts with
 * filemtime() versioning. Per-page conditional loading extends here
 * based on STATIC_TEMPLATE_MAP.md; the base bundle is global because
 * header/footer (and their JS hooks) exist on all 128 static pages.
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

if (!defined('ABSPATH')) {
    exit;
}

final class AssetManager
{
    public static function hooks(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_base']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_page'], 20);
        add_filter('script_loader_tag', [self::class, 'defer_main'], 10, 2);
    }

    /** Force defer on the main bundle regardless of WP strategy support. */
    public static function defer_main(string $tag, string $handle): string
    {
        if ($handle === 'erfansanat-main' && strpos($tag, 'defer') === false) {
            $tag = str_replace('<script ', '<script defer ', $tag);
        }
        return $tag;
    }

    /**
     * Global bundle: compiled Tailwind CSS + vanilla JS (defer).
     * Paths are relative inside the theme and identical to the static
     * mirror, so url(../fonts/...) inside the CSS keeps working.
     */
    public static function enqueue_base(): void
    {
        $css = ERFANSANAT_THEME_URI . 'assets/css/styles.css';
        $js  = ERFANSANAT_THEME_URI . 'assets/js/main.js';

        wp_enqueue_style(
            'erfansanat-styles',
            $css,
            [],
            self::version('assets/css/styles.css')
        );
        wp_enqueue_style(
            'erfansanat-print',
            ERFANSANAT_THEME_URI . 'assets/css/print.css',
            [],
            self::version('assets/css/print.css'),
            'print'
        );
        wp_enqueue_script(
            'erfansanat-main',
            $js,
            [],
            self::version('assets/js/main.js'),
            ['in_footer' => true, 'strategy' => 'defer']
        );
    }

    /**
     * Per-page conditional assets. Extend per STATIC_TEMPLATE_MAP.md
     * as templates are built in later phases.
     */
    public static function enqueue_page(): void
    {
        // Placeholder-free hook point: WooCommerce pages get no extra
        // bundle for now — the single base bundle covers the static design.
    }

    private static function version(string $relative): string
    {
        $path = ERFANSANAT_THEME_DIR . $relative;
        $mtime = is_file($path) ? (string) filemtime($path) : ERFANSANAT_THEME_VERSION;
        return ERFANSANAT_THEME_VERSION . '.' . $mtime;
    }
}
