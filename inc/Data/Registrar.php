<?php
/**
 * Registrar — native registration of CPTs/taxonomies with EXACT rewrite
 * rules matching the live site (SEO: zero 404 on old links), plus admin
 * list-table columns/filters.
 *
 * @package ErfanSanat\Theme\Data
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Data;

if (!defined('ABSPATH')) {
    exit;
}

final class Registrar
{
    public static function hooks(): void
    {
        add_action('init', [self::class, 'register_cpts'], 5);
        add_action('init', [self::class, 'register_taxonomies'], 5);
        add_filter('woocommerce_taxonomy_rewrite_product_cat', [self::class, 'product_cat_slug']);
        add_filter('woocommerce_taxonomy_rewrite_product_tag', [self::class, 'product_tag_slug']);
        add_filter('manage_edit-projects_columns', [self::class, 'columns']);
        add_action('manage_projects_posts_custom_column', [self::class, 'column_content'], 10, 2);
        add_action('restrict_manage_posts', [self::class, 'dropdown_filters']);
        add_filter('parse_query', [self::class, 'apply_admin_filters']);
    }

    /**
     * CPT projects — replaces JetEngine registration.
     */
    public static function register_cpts(): void
    {
        register_post_type('projects', [
            'labels'       => [
                'name'          => __('پروژه‌ها', 'erfansanat'),
                'singular_name' => __('پروژه', 'erfansanat'),
            ],
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => ['slug' => 'projects', 'with_front' => false],
            'hierarchical' => false,
            'menu_icon'    => 'dashicons-screenoptions',
            'supports'     => ['title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions'],
            'show_in_rest' => true,
            'taxonomies'   => ['project_type', 'city'],
        ]);
    }

    /**
     * Taxonomies project_type & city — replaces JetEngine taxonomies.
     */
    public static function register_taxonomies(): void
    {
        $args = [
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
        ];
        register_taxonomy('project_type', ['projects'], $args + [
            'labels'  => ['name' => __('نوع پروژه', 'erfansanat')],
            'rewrite' => ['slug' => 'project_type', 'with_front' => false, 'hierarchical' => false],
        ]);
        register_taxonomy('city', ['projects'], $args + [
            'labels'  => ['name' => __('شهر محل پروژه', 'erfansanat')],
            'rewrite' => ['slug' => 'city', 'with_front' => false, 'hierarchical' => false],
        ]);
    }

    /**
     * SEO-critical: product_cat keeps its custom slug `product_cat`
     * (NOT the WooCommerce default `product-category`), with_front=true.
     */
    public static function product_cat_slug(): string
    {
        return 'product_cat';
    }

    public static function product_tag_slug(): string
    {
        return 'product-tag';
    }

    /**
     * product_cat keeps with_front=true + hierarchical=true (live-site SEO).
     */
    public static function product_cat_args(array $args): array
    {
        $args['rewrite'] = ['slug' => 'product_cat', 'with_front' => true, 'hierarchical' => true];
        $args['hierarchical'] = true;
        return $args;
    }

    public static function columns(array $cols): array
    {
        $out = [];
        foreach ($cols as $k => $v) {
            if ($k === 'date') {
                $out['same_key_hrefl'] = __('کلید Hreflang', 'erfansanat');
                $out['views'] = __('بازدید', 'erfansanat');
            }
            $out[$k] = $v;
        }
        return $out;
    }

    public static function column_content(string $col, int $post_id): void
    {
        if ($col === 'same_key_hrefl') {
            echo esc_html((string) get_post_meta($post_id, 'same_key_hrefl', true));
        }
        if ($col === 'views') {
            echo esc_html((string) get_post_meta($post_id, 'number2-viewc', true));
        }
    }

    /**
     * Dropdown filters for city & project_type on the projects list table.
     */
    public static function dropdown_filters(string $post_type): void
    {
        if ($post_type !== 'projects') {
            return;
        }
        foreach (['project_type', 'city'] as $tax) {
            $terms = get_terms(['taxonomy' => $tax, 'hide_empty' => false]);
            if (is_wp_error($terms) || !$terms) {
                continue;
            }
            $current = isset($_GET[$tax]) ? sanitize_text_field(wp_unslash($_GET[$tax])) : '';
            echo '<select name="' . esc_attr($tax) . '">';
            echo '<option value="">' . esc_html__('همه', 'erfansanat') . '</option>';
            foreach ($terms as $term) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($term->slug),
                    selected($current, $term->slug, false),
                    esc_html($term->name)
                );
            }
            echo '</select>';
        }
    }

    public static function apply_admin_filters(\WP_Query $q): void
    {
        if (!is_admin() || !$q->is_main_query() || $q->get('post_type') !== 'projects') {
            return;
        }
        $tax_query = [];
        foreach (['project_type', 'city'] as $tax) {
            if (!empty($_GET[$tax])) {
                $tax_query[] = [
                    'taxonomy' => $tax,
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field(wp_unslash($_GET[$tax])),
                ];
            }
        }
        if ($tax_query) {
            $q->set('tax_query', $tax_query);
        }
    }
}
