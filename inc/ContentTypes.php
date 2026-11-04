<?php
/**
 * Native content types and taxonomy compatibility for the site.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme;

defined('ABSPATH') || exit;

/**
 * Registers project/service content and preserves the legacy public URL structure.
 */
final class ContentTypes
{
    /** Attach registration and admin hooks before WordPress runs init. */
    public static function register(): void
    {
        add_action('init', [self::class, 'register_post_types'], 0);
        add_action('init', [self::class, 'register_taxonomies'], 5);
        add_action('init', [self::class, 'register_product_brand_taxonomy'], 12);
        add_action('init', [self::class, 'add_legacy_service_rewrite_rules'], 20);

        add_filter('woocommerce_register_post_type_product', [self::class, 'filter_product_post_type_args'], 20);
        add_filter('woocommerce_taxonomy_args_product_cat', [self::class, 'filter_product_cat_args'], 20);
        add_filter('woocommerce_taxonomy_args_product_tag', [self::class, 'filter_product_tag_args'], 20);
        add_filter('woocommerce_taxonomy_args_product_brand', [self::class, 'filter_product_brand_args'], 20);
        add_filter('post_type_link', [self::class, 'filter_service_permalink'], 10, 2);
        add_filter('post_type_archive_link', [self::class, 'filter_service_archive_link'], 10, 2);

        add_action('restrict_manage_posts', [self::class, 'render_project_taxonomy_filters']);
        add_filter('manage_projects_posts_columns', [self::class, 'project_columns']);
        add_action('manage_projects_posts_custom_column', [self::class, 'render_project_column'], 10, 2);
        add_action('after_switch_theme', [self::class, 'flush_rewrite_rules']);
    }

    /** Register native projects and services post types. */
    public static function register_post_types(): void
    {
        register_post_type(
            'projects',
            [
                'labels' => [
                    'name' => __('پروژه‌ها', 'erfansanat-theme'),
                    'singular_name' => __('پروژه', 'erfansanat-theme'),
                    'menu_name' => __('پروژه‌ها', 'erfansanat-theme'),
                    'add_new_item' => __('افزودن پروژه', 'erfansanat-theme'),
                    'edit_item' => __('ویرایش پروژه', 'erfansanat-theme'),
                    'view_item' => __('مشاهده پروژه', 'erfansanat-theme'),
                    'search_items' => __('جست‌وجوی پروژه‌ها', 'erfansanat-theme'),
                    'not_found' => __('پروژه‌ای پیدا نشد.', 'erfansanat-theme'),
                ],
                'public' => true,
                'has_archive' => true,
                'hierarchical' => false,
                'menu_icon' => 'dashicons-screenoptions',
                'supports' => ['title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions'],
                'show_in_rest' => true,
                'rewrite' => [
                    'slug' => 'projects',
                    'with_front' => false,
                ],
                'query_var' => true,
            ]
        );

        register_post_type(
            'services',
            [
                'labels' => [
                    'name' => __('خدمات', 'erfansanat-theme'),
                    'singular_name' => __('خدمت', 'erfansanat-theme'),
                    'menu_name' => __('خدمات نورپردازی', 'erfansanat-theme'),
                    'add_new_item' => __('افزودن خدمت', 'erfansanat-theme'),
                    'edit_item' => __('ویرایش خدمت', 'erfansanat-theme'),
                    'view_item' => __('مشاهده خدمت', 'erfansanat-theme'),
                    'search_items' => __('جست‌وجوی خدمات', 'erfansanat-theme'),
                    'not_found' => __('خدمتی پیدا نشد.', 'erfansanat-theme'),
                ],
                'public' => true,
                'has_archive' => 'services',
                'hierarchical' => false,
                'menu_icon' => 'dashicons-lightbulb',
                'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'],
                'show_in_rest' => true,
                'rewrite' => [
                    'slug' => 'services',
                    'with_front' => false,
                ],
                'query_var' => true,
            ]
        );
    }

    /** Register hierarchical project type and city taxonomies. */
    public static function register_taxonomies(): void
    {
        register_taxonomy(
            'project_type',
            ['projects'],
            [
                'labels' => [
                    'name' => __('انواع پروژه', 'erfansanat-theme'),
                    'singular_name' => __('نوع پروژه', 'erfansanat-theme'),
                    'search_items' => __('جست‌وجوی انواع پروژه', 'erfansanat-theme'),
                    'all_items' => __('همهٔ انواع پروژه', 'erfansanat-theme'),
                    'edit_item' => __('ویرایش نوع پروژه', 'erfansanat-theme'),
                    'add_new_item' => __('افزودن نوع پروژه', 'erfansanat-theme'),
                ],
                'public' => true,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'query_var' => 'project_type',
                'rewrite' => [
                    'slug' => 'project_type',
                    'with_front' => false,
                    'hierarchical' => false,
                ],
            ]
        );

        register_taxonomy(
            'city',
            ['projects'],
            [
                'labels' => [
                    'name' => __('شهرهای پروژه', 'erfansanat-theme'),
                    'singular_name' => __('شهر پروژه', 'erfansanat-theme'),
                    'search_items' => __('جست‌وجوی شهرها', 'erfansanat-theme'),
                    'all_items' => __('همهٔ شهرها', 'erfansanat-theme'),
                    'edit_item' => __('ویرایش شهر', 'erfansanat-theme'),
                    'add_new_item' => __('افزودن شهر', 'erfansanat-theme'),
                ],
                'public' => true,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'query_var' => 'city',
                'rewrite' => [
                    'slug' => 'city',
                    'with_front' => false,
                    'hierarchical' => false,
                ],
            ]
        );
    }

    /**
     * Override WooCommerce's product single/archive rewrite bases while leaving WC registration intact.
     *
     * @param array<string, mixed> $args WooCommerce product post type arguments.
     * @return array<string, mixed>
     */
    public static function filter_product_post_type_args(array $args): array
    {
        $rewrite = isset($args['rewrite']) && is_array($args['rewrite']) ? $args['rewrite'] : [];
        $args['rewrite'] = array_merge(
            $rewrite,
            [
                'slug' => 'product',
                'with_front' => false,
            ]
        );
        $args['has_archive'] = 'shop';

        return $args;
    }

    /**
     * Preserve product category taxonomy URLs from the existing site.
     *
     * @param array<string, mixed> $args WooCommerce taxonomy arguments.
     * @return array<string, mixed>
     */
    public static function filter_product_cat_args(array $args): array
    {
        $args['hierarchical'] = true;
        $args['show_in_rest'] = true;
        $rewrite = isset($args['rewrite']) && is_array($args['rewrite']) ? $args['rewrite'] : [];
        $args['rewrite'] = array_merge(
            $rewrite,
            [
                'slug' => 'product_cat',
                'with_front' => true,
                'hierarchical' => true,
            ]
        );

        return $args;
    }

    /**
     * Preserve product tag URLs from the existing site.
     *
     * @param array<string, mixed> $args WooCommerce taxonomy arguments.
     * @return array<string, mixed>
     */
    public static function filter_product_tag_args(array $args): array
    {
        $args['show_in_rest'] = true;
        $rewrite = isset($args['rewrite']) && is_array($args['rewrite']) ? $args['rewrite'] : [];
        $args['rewrite'] = array_merge(
            $rewrite,
            [
                'slug' => 'product-tag',
                'with_front' => false,
            ]
        );

        return $args;
    }

    /**
     * Keep the product_brand taxonomy hierarchical and available in the REST API.
     *
     * @param array<string, mixed> $args Taxonomy arguments.
     * @return array<string, mixed>
     */
    public static function filter_product_brand_args(array $args): array
    {
        $args['hierarchical'] = true;
        $args['show_in_rest'] = true;

        return $args;
    }

    /**
     * Register product_brand only when WooCommerce has registered its product post type and the taxonomy.
     */
    public static function register_product_brand_taxonomy(): void
    {
        if (!post_type_exists('product') || taxonomy_exists('product_brand')) {
            return;
        }

        register_taxonomy(
            'product_brand',
            ['product'],
            [
                'labels' => [
                    'name' => __('برندهای محصول', 'erfansanat-theme'),
                    'singular_name' => __('برند محصول', 'erfansanat-theme'),
                    'search_items' => __('جست‌وجوی برندها', 'erfansanat-theme'),
                    'all_items' => __('همهٔ برندها', 'erfansanat-theme'),
                    'edit_item' => __('ویرایش برند', 'erfansanat-theme'),
                    'add_new_item' => __('افزودن برند', 'erfansanat-theme'),
                ],
                'public' => true,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'rewrite' => [
                    'slug' => 'product-brand',
                    'with_front' => false,
                ],
            ]
        );
    }

    /**
     * Add city and project-type dropdown filters to the projects list table.
     *
     * @param string $postType Current post type screen.
     */
    public static function render_project_taxonomy_filters(string $postType): void
    {
        if ('projects' !== $postType) {
            return;
        }

        foreach (['project_type' => __('همهٔ انواع پروژه', 'erfansanat-theme'), 'city' => __('همهٔ شهرها', 'erfansanat-theme')] as $taxonomy => $label) {
            if (!taxonomy_exists($taxonomy)) {
                continue;
            }
            $selected = isset($_GET[$taxonomy]) && is_string($_GET[$taxonomy])
                ? sanitize_title(wp_unslash($_GET[$taxonomy]))
                : '';
            wp_dropdown_categories(
                [
                    'show_option_all' => $label,
                    'taxonomy' => $taxonomy,
                    'name' => $taxonomy,
                    'id' => 'filter-by-' . $taxonomy,
                    'selected' => $selected,
                    'value_field' => 'slug',
                    'hierarchical' => true,
                    'hide_empty' => false,
                    'show_count' => false,
                ]
            );
        }
    }

    /**
     * Add the legacy same_key_hrefl column to projects and place taxonomies beside the title.
     *
     * @param array<string, string> $columns Existing list-table columns.
     * @return array<string, string>
     */
    public static function project_columns(array $columns): array
    {
        $updated = [];
        foreach ($columns as $key => $label) {
            $updated[$key] = $label;
            if ('title' === $key) {
                $updated['project_type'] = __('نوع پروژه', 'erfansanat-theme');
                $updated['city'] = __('شهر', 'erfansanat-theme');
                $updated['same_key_hrefl'] = __('کلید ترجمه', 'erfansanat-theme');
            }
        }

        return $updated;
    }

    /**
     * Render terms or the legacy translation key without trusting stored markup.
     *
     * @param string $column Column identifier.
     * @param int    $postId Project ID.
     */
    public static function render_project_column(string $column, int $postId): void
    {
        if (in_array($column, ['project_type', 'city'], true)) {
            $terms = wp_get_post_terms($postId, $column, ['fields' => 'names']);
            if (!is_wp_error($terms) && $terms) {
                echo esc_html(implode(', ', $terms));
            }
            return;
        }

        if ('same_key_hrefl' === $column) {
            echo esc_html((string) get_post_meta($postId, 'same_key_hrefl', true));
        }
    }

    /** Register compatibility rules for the static-mirror .html service URLs. */
    public static function add_legacy_service_rewrite_rules(): void
    {
        add_rewrite_rule('^services\\.html/?$', 'index.php?post_type=services', 'top');
        add_rewrite_rule('^services/([^/]+)\\.html/?$', 'index.php?services=$matches[1]', 'top');
    }

    /**
     * Keep service detail URLs compatible with the static mirror's .html paths.
     *
     * @param string   $permalink Generated permalink.
     * @param \\WP_Post $post Current post.
     * @return string
     */
    public static function filter_service_permalink(string $permalink, \WP_Post $post): string
    {
        if ('services' !== $post->post_type) {
            return $permalink;
        }

        return home_url('/services/' . rawurlencode($post->post_name) . '.html');
    }

    /**
     * Keep the services listing URL compatible with the static mirror.
     *
     * @param string $link Generated archive link.
     * @param string $postType Post type name.
     * @return string
     */
    public static function filter_service_archive_link(string $link, string $postType): string
    {
        return 'services' === $postType ? home_url('/services.html') : $link;
    }

    /** Flush rewrite rules once when the theme is switched on, never on normal requests. */
    public static function flush_rewrite_rules(): void
    {
        flush_rewrite_rules(false);
    }
}
