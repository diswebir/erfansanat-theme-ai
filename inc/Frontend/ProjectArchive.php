<?php
/**
 * فیلترهای GET آرشیو پروژه‌ها — اعمال امن روی pre_get_posts
 *
 * پارامترها: type (اسلاگ project_type)، location (اسلاگ city)، sort (default|popular|rating)
 * شناسه فایل: inc/Frontend/ProjectArchive.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

final class ProjectArchive {

    public static function init(): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        add_action('pre_get_posts', [self::class, 'apply'], 20);
    }

    /** دریافت امن پارامتر GET */
    private static function g(string $key): string {
        if (!isset($_GET[$key]) || !is_string($_GET[$key])) {
            return '';
        }
        return sanitize_text_field((string) wp_unslash((string) $_GET[$key]));
    }

    /** args کوئری بر اساس GET — عمومی برای تست */
    public static function query_args(): array {
        $args = [];
        $tax_query = [];
        $type = self::g('type');
        if ($type !== '' && $type !== 'all') {
            $tax_query[] = ['taxonomy' => 'project_type', 'field' => 'slug', 'terms' => $type];
        }
        $loc = self::g('location');
        if ($loc !== '' && $loc !== 'all') {
            $tax_query[] = ['taxonomy' => 'city', 'field' => 'slug', 'terms' => $loc];
        }
        if ($tax_query !== []) {
            $args['tax_query'] = $tax_query;
        }
        $sort = self::g('sort');
        if ($sort === 'popular') {
            $args['meta_key'] = 'number2-viewc';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        } elseif ($sort === 'rating') {
            $args['meta_key'] = 'es_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        }
        return $args;
    }

    /** اعمال روی کوئری اصلی آرشیو پروژه‌ها */
    public static function apply(object $query): void {
        if (!method_exists($query, 'is_main_query') || !$query->is_main_query()) {
            return;
        }
        if (!(function_exists('is_post_type_archive') && is_post_type_archive('projects'))
            && !(function_exists('is_tax') && is_tax(['project_type', 'city']))) {
            return;
        }
        $per = (int) es_option('archives.projects_per_page', '6');
        $query->set('posts_per_page', max(1, min(100, $per)));
        foreach (self::query_args() as $es_k => $es_v) {
            $query->set($es_k, $es_v);
        }
    }
}
