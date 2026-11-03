<?php
/**
 * حذف بloat وردپرس از فرانت‌اند — بخش ۱: دارایی‌های بلوک‌های گوتنبرگ
 *
 * اصل: پیشخوان و ادیتور دست‌نخورده می‌مانند؛ همه حذف‌ها فقط در زمینه فرانت (is_admin false).
 * فیلترهای es_debloat_* برای استثنا در سازگاری افزونه‌ها.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

final class DeBloat {

    public static function init(): void {
        if (is_admin()) {
            return;
        }

        self::remove_block_assets();
    }

    /** حذف رجیستر/چاپ استایل‌های بلوک‌ها از فرانت (قالب کلاسیک غیربلوکی) */
    private static function remove_block_assets(): void {
        remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
        remove_action('wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles');
        remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
        add_action('wp_enqueue_scripts', [self::class, 'dequeue_block_assets'], 100);
    }

    /**
     * خروجی از صف استایل‌های بلوکی که با هر روشی (افزونه/هسته) انکیو شده باشند.
     */
    public static function dequeue_block_assets(): void {
        $blocked_handles = [
            'wp-block-library',
            'wp-block-library-theme',
            'wc-blocks-style',
            'global-styles',
            'classic-theme-styles',
        ];
        foreach ($blocked_handles as $handle) {
            wp_dequeue_style($handle);
        }
    }
}
