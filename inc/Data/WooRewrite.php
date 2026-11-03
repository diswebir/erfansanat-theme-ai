<?php
/**
 * فیلترهای rewrite تکسونومی‌های ووکامرس — حفظ ساختار URL سئوشده سایت زنده
 *
 * جدول URL نهایی (بلوپرینت §۳):
 *   دسته محصول:  https://erfansanat.com/product_cat/{term}/   (with_front=TRUE — نه product-category!)
 *   برچسب:       https://erfansanat.com/product-tag/{term}/   (with_front=false)
 *   برند:        سلسله‌مراتبی
 *
 * flush در after_switch_theme (ThemeSetup::activate) انجام می‌شود.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Data;

final class WooRewrite {

    public static function init(): void {
        if (!function_exists('add_filter')) {
            return;
        }
        add_filter('woocommerce_taxonomy_args_product_cat', [self::class, 'product_cat_args']);
        add_filter('woocommerce_taxonomy_args_product_tag', [self::class, 'product_tag_args']);
        add_filter('woocommerce_taxonomy_args_product_brand', [self::class, 'product_brand_args']);
    }

    /** دسته محصول: slug=product_cat با with_front=true (نکته حیاتی سئو) */
    public static function product_cat_args(array $args): array {
        $args['rewrite'] = [
            'slug'         => 'product_cat',
            'with_front'   => true,
            'hierarchical' => true,
        ];
        return $args;
    }

    /** برچسب محصول: slug=product-tag با with_front=false */
    public static function product_tag_args(array $args): array {
        $args['rewrite'] = [
            'slug'         => 'product-tag',
            'with_front'   => false,
            'hierarchical' => false,
        ];
        return $args;
    }

    /** برند: سلسله‌مراتبی */
    public static function product_brand_args(array $args): array {
        $args['hierarchical'] = true;
        return $args;
    }
}
