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
        }
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('init', [$this, 'register_core'], 5);
        Data\Registrar::hooks();
        Seo::hooks();
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
