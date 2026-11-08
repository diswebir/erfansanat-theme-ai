<?php
/**
 * فیلترهای GET آرشیو نوشته‌ها (وبلاگ) — اعمال امن روی pre_get_posts
 *
 * پارامترها: cat (شناسه دسته)، s (جستجو) — فیلتر سمت کلاینت با data-blog-* انجام می‌شود؛
 * این کلاس همان فیلترها را سمت سرور هم پشتیبانی می‌کند و هوک فاز ۸ را حفظ می‌کند.
 * فقط روی صفحهٔ نوشته‌ها (home.php) و آرشیو دسته (category.php) اعمال می‌شود.
 * شناسه فایل: inc/Frontend/BlogArchive.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

final class BlogArchive {

    public static function init(): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        add_action('pre_get_posts', [self::class, 'apply'], 20);
    }

    /** args کوئری بر اساس GET — عمومی برای تست */
    public static function query_args(): array {
        $args = [];
        $cat = isset($_GET['cat']) ? absint((string) $_GET['cat']) : 0;
        if ($cat > 0) {
            $args['cat'] = $cat;
        }
        $s = isset($_GET['s']) && is_string($_GET['s']) ? sanitize_text_field((string) wp_unslash((string) $_GET['s'])) : '';
        if ($s !== '') {
            $args['s'] = $s;
        }
        return (array) apply_filters('es_blog_archive_query_args', $args);
    }

    /**
     * اعمال روی کوئری اصلی — فقط آرشیو نوشته‌ها (home/category)، نه صفحه‌ها و نه ادمین.
     * همهٔ آیتم‌ها در DOM رندر می‌شوند (صفحه‌بندی/فیلتر سمت کلاینت طبق میرور؛ data-items-per-page).
     */
    public static function apply(object $query): void {
        if (!empty($GLOBALS['__wp_is_admin'])) {
            return;
        }
        if (!$query->is_main_query() || !method_exists($query, 'is_home')) {
            return;
        }
        $is_posts_archive = $query->is_home() || (method_exists($query, 'is_category') && $query->is_category());
        if (!$is_posts_archive) {
            return;
        }
        $args = self::query_args();
        foreach ($args as $key => $value) {
            $query->set((string) $key, $value);
        }
        $query->set('posts_per_page', 100); // همهٔ مقالات برای صفحه‌بندی/فیلتر سمت کلاینت
        $query->set('ignore_sticky_posts', true); // ترتیب میرور: جدیدترین؛ sticky فقط نشان «ویژه» می‌گیرد
    }
}
