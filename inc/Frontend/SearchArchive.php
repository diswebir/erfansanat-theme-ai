<?php
/**
 * فیلترهای GET صفحه جستجو — اعمال امن روی pre_get_posts
 *
 * پارامتر: type (any|product|projects|post) — محدودکردن نوع نتایج تب‌ها.
 * فقط روی کوئری اصلی is_search اعمال می‌شود؛ جستجو با WP_Query استاندارد.
 * شناسه فایل: inc/Frontend/SearchArchive.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

final class SearchArchive {

    public static function init(): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        add_action('pre_get_posts', [self::class, 'apply'], 20);
    }

    /** انواع مجاز نتایج */
    public static function allowed_types(): array {
        return (array) apply_filters('es_search_allowed_types', ['any', 'product', 'projects', 'post']);
    }

    /** args کوئری بر اساس GET — عمومی برای تست */
    public static function query_args(): array {
        $args = [];
        $type = isset($_GET['type']) && is_string($_GET['type']) ? sanitize_key((string) wp_unslash((string) $_GET['type'])) : '';
        if (in_array($type, self::allowed_types(), true) && $type !== '' && $type !== 'any') {
            $args['post_type'] = $type;
        }
        return (array) apply_filters('es_search_query_args', $args);
    }

    /** تعداد نتایج در صفحه */
    public static function per_page(): int {
        return max(1, (int) apply_filters('es_search_per_page', 9));
    }

    /** اعمال روی کوئری اصلی جستجو */
    public static function apply(object $query): void {
        if (!empty($GLOBALS['__wp_is_admin'])) {
            return;
        }
        if (!$query->is_main_query() || !method_exists($query, 'is_search') || !$query->is_search()) {
            return;
        }
        $args = self::query_args();
        foreach ($args as $key => $value) {
            $query->set((string) $key, $value);
        }
        $query->set('posts_per_page', self::per_page());
    }
}
