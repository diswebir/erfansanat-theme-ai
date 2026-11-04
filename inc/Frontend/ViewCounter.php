<?php
/**
 * شمارنده بازدید — کلیدهای تاریخی number2-viewc (fallback: number-viewc)
 * با cookie dedupe و رد کردن ادمین/پیش‌نمایش/فید/بات.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

final class ViewCounter {

    public const PRIMARY_KEY = 'number2-viewc';
    public const FALLBACK_KEY = 'number-viewc';
    public const COOKIE_PREFIX = 'es_vc_';

    public static function init(): void {
        add_action('wp', [self::class, 'maybe_track'], 20);
        add_filter('manage_product_posts_columns', [self::class, 'columns']);
        add_action('manage_product_posts_custom_column', [self::class, 'column_content'], 10, 2);
    }

    /** پست‌تایپ‌های شمرده‌شده */
    public static function post_types(): array {
        return (array) apply_filters('es_view_counter_post_types', ['product', 'projects', 'post']);
    }

    /**
     * شمارش در دیداری سایت
     */
    public static function maybe_track(): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        if (function_exists('is_preview') && is_preview()) {
            return;
        }
        if (function_exists('is_feed') && is_feed()) {
            return;
        }
        if (self::is_bot()) {
            return;
        }
        if (!function_exists('is_singular') || !is_singular(self::post_types())) {
            return;
        }

        $post_id = (int) get_queried_object_id();
        if ($post_id <= 0) {
            return;
        }

        $cookie_name = self::COOKIE_PREFIX . $post_id;
        if (isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] === '1') {
            return;
        }

        self::increment($post_id);

        if (!headers_sent()) {
            $ttl = (int) apply_filters('es_view_counter_cookie_ttl', DAY_IN_SECONDS);
            setcookie($cookie_name, '1', ['expires' => time() + $ttl, 'path' => '/', 'samesite' => 'Lax']);
        }
    }

    /** تشخیص بات */
    public static function is_bot(): bool {
        $ua = '';
        if (isset($_SERVER['HTTP_USER_AGENT']) && is_string($_SERVER['HTTP_USER_AGENT'])) {
            $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        }
        if ($ua === '') {
            return true; // بدون UA = درخواست غیرمرورگر
        }
        $needles = (array) apply_filters('es_view_counter_bots', [
            'bot', 'crawl', 'spider', 'slurp', 'curl', 'wget', 'python-requests', 'facebookexternalhit', 'headless',
        ]);
        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($ua, strtolower((string) $needle))) {
                return true;
            }
        }
        return false;
    }

    /** افزایش شمارنده (کلید اصلی، گم‌شدن fallback) */
    public static function increment(int $post_id): int {
        $count = self::get_views($post_id) + 1;
        update_post_meta($post_id, self::PRIMARY_KEY, (string) $count);
        return $count;
    }

    /** خواندن: number2-viewc وگرنه number-viewc */
    public static function get_views(int $post_id): int {
        $raw = get_post_meta($post_id, self::PRIMARY_KEY, true);
        if ($raw === '' || $raw === null || (is_array($raw) && $raw === [])) {
            $raw = get_post_meta($post_id, self::FALLBACK_KEY, true);
        }
        return absint(is_scalar($raw) ? (string) $raw : 0);
    }

    /** ستون بازدید در لیست محصولات */
    public static function columns(array $columns): array {
        $columns['es-views'] = __('بازدید', 'erfansanat');
        return $columns;
    }

    public static function column_content(string $column, int $post_id): void {
        if ($column !== 'es-views') {
            return;
        }
        echo esc_html((string) self::get_views($post_id));
    }
}
