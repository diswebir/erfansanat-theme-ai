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
        self::remove_emoji();
        self::clean_wp_head();
        self::extra_hardening();
    }

    /** حذف کامل ایموجی‌های وردپرس (اسکریپت/استایل/فیلترهای فید و ایمیل) */
    private static function remove_emoji(): void {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_action('wp_mail', 'wp_staticize_emoji_for_email');
    }

    /** پاکسازی wp_head از متا/لینک‌های زائد برای حداکثر سرعت */
    private static function clean_wp_head(): void {
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'parent_post_rel_link', 10);
        remove_action('wp_head', 'start_post_rel_link', 10);
        remove_action('wp_head', 'feed_links_extra', 3);
        add_filter('the_generator', '__return_empty_string');
    }

    /** سخت‌سازی‌های تکمیلی — هر کدام با فیلتر es_debloat_* قابل استثنا */
    private static function extra_hardening(): void {
        // XML-RPC برای این قالب کاربردی ندارد؛ در صورت نیاز افزونه‌ای با فیلتر زیر فعال بماند
        if (!apply_filters('es_debloat_keep_xmlrpc', false)) {
            add_filter('xmlrpc_enabled', '__return_false');
        }

        // dashicons فقط برای بازدیدکنندگان واردنشده حذف می‌شود
        add_action('wp_enqueue_scripts', [self::class, 'dequeue_dashicons'], 100);
    }

    /**
     * حذف dashicons از فرانت برای بازدیدکنندگان واردنشده.
     * (کاربران واردشده به آن برای نوار ادمین نیاز دارند.)
     */
    public static function dequeue_dashicons(): void {
        if (!is_user_logged_in() && apply_filters('es_debloat_remove_dashicons', true)) {
            wp_dequeue_style('dashicons');
        }
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
