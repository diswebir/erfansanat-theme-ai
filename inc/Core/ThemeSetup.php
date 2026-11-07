<?php
/**
 * Core WordPress & WooCommerce Theme Supports, Menus, and Base Body/HTML Attributes.
 *
 * @package ErfanSanat\Theme\Core
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Core;

use ErfanSanat\Theme\Contracts\ServiceInterface;

final class ThemeSetup implements ServiceInterface
{
    public function register(): void
    {
        add_action('after_setup_theme', [$this, 'setupTheme']);
        add_filter('body_class', [$this, 'filterBodyClasses']);
        add_filter('language_attributes', [$this, 'filterLanguageAttributes'], 10, 2);
    }

    /**
     * Register theme features, WooCommerce compatibility, and navigation menus.
     */
    public function setupTheme(): void
    {
        load_theme_textdomain('erfansanat', ERFANSANAT_THEME_DIR . '/languages');

        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('automatic-feed-links');

        add_theme_support(
            'html5',
            [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
                'navigation-widgets',
            ]
        );

        // Keep the classic visual editor aligned with the locally hosted admin typography.
        add_editor_style('assets/admin/css/admin-dashboard.css');

        add_theme_support(
            'custom-logo',
            [
                'height'      => 96,
                'width'       => 280,
                'flex-height' => true,
                'flex-width'  => true,
            ]
        );

        add_theme_support(
            'woocommerce',
            [
                'thumbnail_image_width' => 600,
                'single_image_width'    => 1000,
                'product_grid'          => [
                    'default_rows'    => 4,
                    'min_rows'        => 1,
                    'max_rows'        => 12,
                    'default_columns' => 3,
                    'min_columns'     => 1,
                    'max_columns'     => 4,
                ],
            ]
        );
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');

        register_nav_menus(
            [
                'main-mega-menu'  => 'مگامنوی اصلی دسکتاپ (Main Mega Menu)',
                'mobile-menu'     => 'فهرست موبایل چندسطحی (Mobile Off-Canvas Menu)',
                'tolidat-sherkat' => 'منوی تولیدات شرکت در فوتر/هدر (Company Products Menu)',
                'landing'         => 'منوی اسکرول صفحات لندینگ (Landing Scroll Menu)',
            ]
        );
    }

    /**
     * Preserve exact static template base body classes (`bg-[#f6f8f7] text-[#1e293b] antialiased`).
     *
     * @param array<int, string> $classes Existing WordPress body classes.
     * @return array<int, string>
     */
    public function filterBodyClasses(array $classes): array
    {
        $staticBaseClasses = [
            'bg-[#f6f8f7]',
            'text-[#1e293b]',
            'antialiased',
        ];

        foreach ($staticBaseClasses as $cls) {
            if (!in_array($cls, $classes, true)) {
                $classes[] = $cls;
            }
        }

        return $classes;
    }

    /**
     * Ensure RTL directionality and Persian language defaults match static HTML (`lang="fa" dir="rtl"`).
     */
    public function filterLanguageAttributes(string $output, string $doctype): string
    {
        if (is_admin()) {
            if (!str_contains($output, 'dir=')) {
                $output .= ' dir="rtl"';
            }

            return trim($output);
        }

        $language = apply_filters('erfansanat_theme_html_language', 'fa');
        if (!is_string($language) || !preg_match('/^[a-z]{2,3}(?:-[a-z0-9]{2,8})*$/i', $language)) {
            $language = 'fa';
        }

        $languageAttribute = 'lang="' . esc_attr($language) . '"';
        if (preg_match('~\blang="[^"]*"~', $output)) {
            $output = preg_replace('~\blang="[^"]*"~', $languageAttribute, $output, 1) ?? $output;
        } else {
            $output = $languageAttribute . ' ' . $output;
        }

        if (preg_match('~\bdir="[^"]*"~', $output)) {
            $output = preg_replace('~\bdir="[^"]*"~', 'dir="rtl"', $output, 1) ?? $output;
        } else {
            $output .= ' dir="rtl"';
        }

        return trim($output);
    }
}
