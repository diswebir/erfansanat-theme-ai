<?php
/**
 * راه‌اندازی هسته قالب — Theme Supports، منوها، سایز تصاویر و هوک فعال‌سازی
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme;

final class ThemeSetup {

    public static function init(): void {
        add_action('after_setup_theme', [self::class, 'setup']);
        add_action('after_switch_theme', [self::class, 'activate']);
    }

    /**
     * قابلیت‌های هسته — دقیقاً مطابق نیاز میرور استاتیک و ووکامرس.
     * گالری پیش‌فرض ووکامرس (zoom/lightbox/slider) عمداً ثبت نمی‌شود؛
     * گالری اختصاصی میرور (data-gallery-*) جایگزین آن است (فاز ۶).
     */
    public static function setup(): void {
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
        add_theme_support('custom-logo', [
            'height'               => 96,
            'width'                => 96,
            'flex-height'          => true,
            'flex-width'           => true,
            'unlink-homepage-logo' => false,
        ]);
        add_theme_support('woocommerce');

        /**
         * موقعیت‌های منو — اسلاگ‌ها عیناً مطابق بلوپرینت دیتابیس زنده (temp/DATA_BLUEPRINT.md §۱۰)
         */
        register_nav_menus([
            'main-mega-menu'  => __('مگامنوی اصلی دسکتاپ', 'erfansanat'),
            'mobile-menu'     => __('منوی موبایل', 'erfansanat'),
            'tolidat-sherkat' => __('تولیدات شرکت', 'erfansanat'),
            'landing'         => __('منوی لندینگ (اسکرول)', 'erfansanat'),
        ]);

        /**
         * سایزهای تصویر کارت‌ها مطابق نسبت‌های گرید میرور (کارت محصول/پروژه/مقاله)
         */
        add_image_size('es-card-600', 600, 400, true);
        add_image_size('es-card-400', 400, 300, true);
    }

    /** فعال‌سازی قالب: بازسازی rewrite rules برای اسلاگ‌های فاز ۳ */
    public static function activate(): void {
        if (function_exists('flush_rewrite_rules')) {
            flush_rewrite_rules();
        }
    }
}
