<?php
/** WooCommerce theme integration and presentation setup. */
declare(strict_types=1);

namespace ErfanSanat\Theme\WooCommerce;

use ErfanSanat\Theme\Contracts\ServiceInterface;

final class WooSetup implements ServiceInterface
{
    public function register(): void
    {
        add_action('after_setup_theme', [self::class, 'setup'], 20);
        add_action('wp', [self::class, 'removeConflictingHooks'], 20);
    }

    public static function setup(): void
    {
        add_theme_support('woocommerce', [
            'thumbnail_image_width' => 480,
            'single_image_width' => 900,
            'product_grid' => ['default_rows' => 3, 'min_rows' => 1, 'max_rows' => 8, 'default_columns' => 3, 'min_columns' => 1, 'max_columns' => 4],
        ]);
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }

    public static function removeConflictingHooks(): void
    {
        if (!class_exists('WooCommerce')) {
            return;
        }
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
    }
}
