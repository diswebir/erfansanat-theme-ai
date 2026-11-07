<?php
/**
 * Queries — optimized WP_Query helpers for homepage/archive sections.
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

if (!defined('ABSPATH')) {
    exit;
}

final class Queries
{
    public static function featured_products(int $n = 8): \WP_Query
    {
        return new \WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => $n,
            'post_status'    => 'publish',
            'meta_key'       => 'true_sale',
            'orderby'        => 'meta_value date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ]);
    }

    public static function latest_projects(int $n = 6): \WP_Query
    {
        return new \WP_Query([
            'post_type'      => 'projects',
            'posts_per_page' => $n,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ]);
    }

    public static function latest_posts(int $n = 4): \WP_Query
    {
        return new \WP_Query([
            'post_type'      => 'post',
            'posts_per_page' => $n,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ]);
    }

    public static function product_categories(int $n = 12): array
    {
        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'number'     => $n,
        ]);
        return is_wp_error($terms) ? [] : $terms;
    }
}
