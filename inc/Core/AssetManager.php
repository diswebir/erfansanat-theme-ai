<?php
/**
 * Frontend Asset Manager — Enqueues Static CSS, Vanilla JS, Local Vazirmatn Fonts & Runtime Config.
 *
 * @package ErfanSanat\Theme\Core
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Core;

use ErfanSanat\Theme\Contracts\ServiceInterface;
use ErfanSanat\Theme\Helpers\AssetHelper;

final class AssetManager implements ServiceInterface
{
    public const STYLE_HANDLE  = 'erfansanat-styles';
    public const SCRIPT_HANDLE = 'erfansanat-main';

    public function register(): void
    {
        add_action('wp_head', [$this, 'printResourcePreloads'], 2);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets'], 20);
    }

    /**
     * Output standard HTML5 preload hints for primary Persian font and front-page LCP hero media.
     */
    public function printResourcePreloads(): void
    {
        if (is_admin()) {
            return;
        }

        $arabicFontUri = AssetHelper::uri('fonts/vazirmatn-arabic.woff2');
        printf(
            "<link rel=\"preload\" href=\"%s\" as=\"font\" type=\"font/woff2\" crossorigin />\n",
            esc_url($arabicFontUri)
        );

        if (is_front_page()) {
            $heroImage = 'light-tunnel.webp';
            if (is_file(AssetHelper::path('images/' . $heroImage))) {
                printf(
                    "<link rel=\"preload\" href=\"%s\" as=\"image\" type=\"image/webp\" fetchpriority=\"high\" />\n",
                    esc_url(AssetHelper::imageUri($heroImage))
                );
            }
        }
    }

    /**
     * Enqueue pre-compiled Tailwind stylesheet and interactive Vanilla JS with filemtime cache-busting.
     */
    public function enqueueFrontendAssets(): void
    {
        if (is_admin()) {
            return;
        }

        wp_enqueue_style(
            self::STYLE_HANDLE,
            AssetHelper::uri('css/styles.css'),
            [],
            AssetHelper::version('css/styles.css'),
            'all'
        );

        wp_enqueue_script(
            self::SCRIPT_HANDLE,
            AssetHelper::uri('js/main.js'),
            [],
            AssetHelper::version('js/main.js'),
            [
                'in_footer' => true,
                'strategy'  => 'defer',
            ]
        );

        $runtimeConfig = [
            'ajaxUrl'           => admin_url('admin-ajax.php'),
            'nonce'             => wp_create_nonce('erfansanat_public_nonce'),
            'homeUrl'           => home_url('/'),
            'shopUrl'           => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/'),
            'cartUrl'           => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'),
            'checkoutUrl'       => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/'),
            'projectsUrl'       => home_url('/projects/'),
            'assetsUrl'         => rtrim(ERFANSANAT_THEME_URI, '/') . '/assets',
            'imagesUrl'         => rtrim(ERFANSANAT_THEME_URI, '/') . '/assets/images',
            'isRtl'             => is_rtl(),
            'pageContext'       => $this->detectPageContext(),
            'filterUrlMutation' => false,
        ];

        $inlineJs = 'window.esThemeData = ' . wp_json_encode($runtimeConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';';
        wp_add_inline_script(self::SCRIPT_HANDLE, $inlineJs, 'before');
    }

    /**
     * Detect active template context aligned with STATIC_TEMPLATE_MAP.md.
     */
    public function detectPageContext(): string
    {
        if (is_front_page()) {
            return 'front-page';
        }

        if (function_exists('is_product') && is_product()) {
            return 'single-product';
        }

        if ((function_exists('is_shop') && is_shop()) || (function_exists('is_product_taxonomy') && is_product_taxonomy())) {
            return 'shop-archive';
        }

        if (function_exists('is_cart') && is_cart()) {
            return 'cart';
        }

        if (function_exists('is_checkout') && is_checkout()) {
            return 'checkout';
        }

        if (is_singular('projects')) {
            return 'single-project';
        }

        if (is_post_type_archive('projects') || is_tax('project_type') || is_tax('city')) {
            return 'projects-archive';
        }

        if (is_singular('post')) {
            return 'single-post';
        }

        if (is_home() || is_category() || is_tag() || is_archive()) {
            return 'blog-archive';
        }

        if (is_search()) {
            return 'search';
        }

        if (is_404()) {
            return '404';
        }

        return 'page';
    }
}
