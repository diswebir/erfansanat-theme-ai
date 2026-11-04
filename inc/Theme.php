<?php
/**
 * Main theme orchestrator.
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

if (!defined('ABSPATH')) {
    exit;
}

final class Theme
{
    private static ?Theme $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->boot();
        }
        return self::$instance;
    }

    private function boot(): void
    {
        DeBloat::hooks();
        AssetManager::hooks();
        if (is_admin()) {
            Admin\ThemeOptions::hooks();
            Admin\SectionManager::hooks();
            Admin\ProductMetaboxes::hooks();
            Admin\LegacyMetaBoxes::hooks();
            Admin\Migrator::hooks();
            add_action('admin_enqueue_scripts', [$this, 'admin_font']);
            add_action('admin_head', [$this, 'admin_font_css'], 1);
        }
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('init', [$this, 'register_core'], 5);
        Data\Registrar::hooks();
        Seo::hooks();
    }

    /**
     * Vazirmatn Persian font for the admin panel (local woff2, no CDN).
     */
    public function admin_font(string $hook): void
    {
        wp_enqueue_style(
            'erfansanat-admin-font',
            ERFANSANAT_THEME_URI . 'assets/css/admin-font.css',
            [],
            ERFANSANAT_THEME_VERSION
        );
    }

    /** Inline @font-face so it applies even before the stylesheet loads. */
    public function admin_font_css(): void
    {
        $uri = esc_url(ERFANSANAT_THEME_URI . 'assets/fonts/');
        echo '<style id="erfansanat-admin-font-face">'
            . '@font-face{font-family:"Vazirmatn";src:url("' . $uri . 'vazirmatn-arabic.woff2") format("woff2");font-weight:100 900;font-style:normal;font-display:swap;}'
            . '#wpcontent,#wpwrap,.wp-admin,body.wp-admin{font-family:"Vazirmatn",Tahoma,Arial,sans-serif !important;}'
            . '</style>' . "\n";
    }

    /**
     * Theme supports — required pass conditions for Task 1.1 plus the
     * essentials for a WooCommerce RTL shop.
     */
    public function setup(): void
    {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('woocommerce');
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ]);
        add_theme_support('automatic-feed-links');
        add_theme_support('responsive-embeds');
        add_theme_support('custom-logo');
        add_theme_support('menus');
        add_theme_support('customize-selective-refresh-widgets');

        // RTL is native to this site (Persian).
        if (is_rtl()) {
            add_theme_support('rtl-css');
        }

        load_theme_textdomain('erfansanat', ERFANSANAT_THEME_DIR . 'languages');
    }

    /**
     * Early core registration hook — CPTs/taxonomies/menus are wired in
     * Phase 3/4 modules; this stays intentionally light for Task 1.1.
     */
    public function register_core(): void
    {
        register_nav_menus([
            'main-mega-menu'  => __('مگامنوی اصلی', 'erfansanat'),
            'mobile-menu'     => __('منوی موبایل', 'erfansanat'),
            'tolidat-sherkat' => __('تولیدات شرکت', 'erfansanat'),
            'landing'         => __('منوی لندینگ', 'erfansanat'),
        ]);
    }
}
