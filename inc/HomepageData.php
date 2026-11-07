<?php
/**
 * Bounded, cached data access for dynamic homepage sections.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme;

defined('ABSPATH') || exit;

/**
 * Retrieves homepage cards and metrics from WordPress and WooCommerce sources.
 */
final class HomepageData
{
    private const CACHE_VERSION_OPTION = 'es_homepage_data_version';
    private const CACHE_TTL = 300;
    private const MAX_ITEMS = 24;

    /** Register cache invalidation hooks for content used on the homepage. */
    public static function register(): void
    {
        add_action('save_post', [self::class, 'maybe_invalidate_for_post'], 10, 3);
        add_action('deleted_post', [self::class, 'invalidate_cache']);
        add_action('created_term', [self::class, 'invalidate_for_term'], 10, 3);
        add_action('edited_term', [self::class, 'invalidate_for_term'], 10, 3);
        add_action('delete_term', [self::class, 'invalidate_for_term'], 10, 3);
        add_action('set_object_terms', [self::class, 'invalidate_for_object_terms'], 10, 4);
    }

    /** Return published project IDs, optionally constrained by an internal WP_Query argument set. */
    public static function get_projects(int $limit = 3, array $queryArgs = []): array
    {
        return self::query_post_ids('projects', $limit, $queryArgs);
    }

    /** Return article IDs ordered newest first. */
    public static function get_articles(int $limit = 4, array $queryArgs = []): array
    {
        return self::query_post_ids('post', $limit, $queryArgs);
    }

    /**
     * Return featured WooCommerce products, falling back to recent published products.
     * The WP_Query branch keeps the homepage usable when WooCommerce is temporarily disabled.
     */
    public static function get_products(int $limit = 6, array $queryArgs = []): array
    {
        $limit = self::normalize_limit($limit);
        $cacheKey = self::cache_key('products', [$limit, $queryArgs]);

        return self::remember($cacheKey, static function () use ($limit, $queryArgs): array {
            if (function_exists('wc_get_products')) {
                $wcArgs = [
                    'status' => 'publish',
                    'limit' => $limit,
                    'return' => 'ids',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'visibility' => 'visible',
                ];
                if (isset($queryArgs['include']) && is_array($queryArgs['include'])) {
                    $wcArgs['include'] = array_values(array_filter(array_map('absint', $queryArgs['include'])));
                }
                if (isset($queryArgs['category']) && is_array($queryArgs['category'])) {
                    $wcArgs['category'] = array_values(array_filter(array_map('sanitize_title', $queryArgs['category'])));
                }
                if (isset($queryArgs['featured_only'])) {
                    $featuredOnly = (bool) $queryArgs['featured_only'];
                    unset($queryArgs['featured_only']);
                } else {
                    $featuredOnly = false;
                }

                if (!isset($queryArgs['include']) && !$featuredOnly) {
                    $featured = self::normalize_product_ids(wc_get_products(array_merge($wcArgs, ['featured' => true])));
                    if ($featured) {
                        return array_slice($featured, 0, $limit);
                    }
                }

                return array_slice(self::normalize_product_ids(wc_get_products($wcArgs)), 0, $limit);
            }

            $args = $queryArgs;
            if (isset($args['include']) && is_array($args['include'])) {
                $args['post__in'] = array_values(array_filter(array_map('absint', $args['include'])));
                unset($args['include']);
            }
            if (isset($args['category']) && is_array($args['category']) && taxonomy_exists('product_cat')) {
                $categorySlugs = array_values(array_filter(array_map('sanitize_title', $args['category'])));
                if ($categorySlugs) {
                    $args['tax_query'][] = [
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => $categorySlugs,
                    ];
                }
            }
            unset($args['featured_only'], $args['category']);
            return self::query_post_ids('product', $limit, $args);
        });
    }

    /** Return visible product categories ordered by product count. */
    public static function get_product_categories(int $limit = 6): array
    {
        $limit = self::normalize_limit($limit);
        $ids = self::remember(self::cache_key('product_categories', [$limit]), static function () use ($limit): array {
            if (!taxonomy_exists('product_cat')) {
                return [];
            }
            $terms = get_terms([
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
                'number' => $limit,
                'orderby' => 'count',
                'order' => 'DESC',
            ]);
            if (is_wp_error($terms) || !is_array($terms)) {
                return [];
            }
            return array_values(array_map(static function (\WP_Term $term): int {
                return (int) $term->term_id;
            }, $terms));
        });

        return self::resolve_terms($ids, 'product_cat');
    }

    /** Return populated project type terms for the homepage's portfolio tabs. */
    public static function get_project_types(int $limit = 4): array
    {
        $limit = self::normalize_limit($limit);
        $ids = self::remember(self::cache_key('project_types', [$limit]), static function () use ($limit): array {
            if (!taxonomy_exists('project_type')) {
                return [];
            }
            $terms = get_terms([
                'taxonomy' => 'project_type',
                'hide_empty' => true,
                'number' => $limit,
                'orderby' => 'count',
                'order' => 'DESC',
            ]);
            if (is_wp_error($terms) || !is_array($terms)) {
                return [];
            }
            return array_values(array_map(static function (\WP_Term $term): int {
                return (int) $term->term_id;
            }, $terms));
        });

        return self::resolve_terms($ids, 'project_type');
    }

    /** Count the editable client entries configured on the active homepage. */
    public static function get_configured_client_count(int $pageId): int
    {
        if ($pageId < 1 || !class_exists(Admin\SectionManager::class)) {
            return 0;
        }
        foreach (Admin\SectionManager::get_renderable_sections($pageId) as $section) {
            if ('clients' === ($section['id'] ?? '') && isset($section['fields']['logos']) && is_array($section['fields']['logos'])) {
                return count($section['fields']['logos']);
            }
        }
        return 0;
    }

    /** Return an exact published-post count for a registered post type. */
    public static function get_published_count(string $postType): int
    {
        $postType = sanitize_key($postType);
        if ('' === $postType || !post_type_exists($postType)) {
            return 0;
        }
        return (int) self::remember(self::cache_key('count', [$postType]), static function () use ($postType): int {
            $counts = wp_count_posts($postType);
            return $counts && isset($counts->publish) ? max(0, (int) $counts->publish) : 0;
        });
    }

    /** Return a display URL for a post thumbnail, then its legacy media gallery. */
    public static function get_post_image_url(int $postId, string $size = 'large'): string
    {
        $url = get_the_post_thumbnail_url($postId, $size);
        if (is_string($url) && '' !== $url) {
            return $url;
        }

        $gallery = [];
        $postType = get_post_type($postId);
        if ('projects' === $postType && class_exists(Admin\ProjectMetaboxes::class)) {
            $gallery = Admin\ProjectMetaboxes::get_project_gallery_ids($postId);
        } elseif ('product' === $postType && class_exists(Admin\ProductMetaboxes::class)) {
            $gallery = Admin\ProductMetaboxes::get_product_gallery_ids($postId);
        }
        if ($gallery) {
            $url = wp_get_attachment_image_url(absint(reset($gallery)), $size);
            if (is_string($url) && '' !== $url) {
                return $url;
            }
        }

        return '';
    }

    /** Return the registered thumbnail for a product category, including the legacy key. */
    public static function get_category_image_url(\WP_Term $term): string
    {
        $thumbnailId = absint(get_term_meta($term->term_id, 'thumbnail', true));
        if (!$thumbnailId) {
            $thumbnailId = absint(get_term_meta($term->term_id, 'thumbnail_id', true));
        }
        if (!$thumbnailId) {
            return '';
        }
        $url = wp_get_attachment_image_url($thumbnailId, 'medium');
        return is_string($url) ? $url : '';
    }

    /** Return the first useful city or project-type name for a project. */
    public static function get_project_location(int $postId): string
    {
        foreach (['city', 'project_type'] as $taxonomy) {
            $terms = get_the_terms($postId, $taxonomy);
            if (is_array($terms) && isset($terms[0]) && $terms[0] instanceof \WP_Term) {
                return $terms[0]->name;
            }
        }
        return '';
    }

    /** Return project year/label from the preserved legacy completion date. */
    public static function get_project_date_label(int $postId): string
    {
        $date = sanitize_text_field((string) get_post_meta($postId, 'finish-pro-time', true));
        if ('' === $date) {
            $date = get_the_date('Y', $postId);
        }
        return $date;
    }

    /** Advance the transient namespace when relevant content changes. */
    public static function invalidate_cache(): void
    {
        $version = (int) get_option(self::CACHE_VERSION_OPTION, 0) + 1;
        update_option(self::CACHE_VERSION_OPTION, $version, false);
    }

    /** Invalidate query results after a relevant post is saved. */
    public static function maybe_invalidate_for_post(int $postId, \WP_Post $post, bool $update): void
    {
        if (in_array($post->post_type, ['post', 'projects', 'product'], true)) {
            self::invalidate_cache();
        }
    }

    /** Invalidate term query caches when supported taxonomies change. */
    public static function invalidate_for_term(int $termId, int $termTaxonomyId = 0, string $taxonomy = ''): void
    {
        if (in_array($taxonomy, ['product_cat', 'project_type', 'city'], true)) {
            self::invalidate_cache();
        }
    }

    /** Invalidate term-based queries when object relationships change. */
    public static function invalidate_for_object_terms(int $objectId, $terms, $termTaxonomyIds, string $taxonomy): void
    {
        if (in_array($taxonomy, ['product_cat', 'project_type', 'city'], true)) {
            self::invalidate_cache();
        }
    }

    /** Run and transient-cache a constrained post-ID query. */
    private static function query_post_ids(string $postType, int $limit, array $queryArgs = []): array
    {
        $limit = self::normalize_limit($limit);
        $postType = sanitize_key($postType);
        $cacheKey = self::cache_key('posts', [$postType, $limit, $queryArgs]);

        return self::remember($cacheKey, static function () use ($postType, $limit, $queryArgs): array {
            $args = array_merge(
                $queryArgs,
                [
                    'post_type' => $postType,
                    'post_status' => 'publish',
                    'posts_per_page' => $limit,
                    'no_found_rows' => true,
                    'ignore_sticky_posts' => true,
                    'orderby' => $queryArgs['orderby'] ?? 'date',
                    'order' => $queryArgs['order'] ?? 'DESC',
                    'fields' => 'ids',
                    'suppress_filters' => false,
                ]
            );
            unset($args['include']);
            $query = new \WP_Query($args);
            return array_values(array_filter(array_map('absint', $query->posts)));
        });
    }

    /** Cache bounded lists of IDs; objects are resolved fresh so edits remain visible. */
    private static function remember(string $key, callable $callback)
    {
        $cached = get_transient($key);
        if (false !== $cached) {
            return $cached;
        }
        $value = $callback();
        set_transient($key, $value, self::CACHE_TTL);
        return $value;
    }

    /** Build a versioned transient key from normalized query inputs. */
    private static function cache_key(string $type, array $arguments): string
    {
        $version = (int) get_option(self::CACHE_VERSION_OPTION, 0);
        return 'es_home_' . sanitize_key($type) . '_' . $version . '_' . md5(serialize($arguments));
    }

    /** Clamp all externally-controlled result counts to a safe upper bound. */
    private static function normalize_limit(int $limit): int
    {
        return max(1, min(self::MAX_ITEMS, $limit));
    }

    /** Normalize WooCommerce's product-object or product-ID return shape. */
    private static function normalize_product_ids($products): array
    {
        if (!is_array($products)) {
            return [];
        }
        $ids = [];
        foreach ($products as $product) {
            $id = is_object($product) && method_exists($product, 'get_id')
                ? absint($product->get_id())
                : absint($product);
            if ($id > 0) {
                $ids[] = $id;
            }
        }
        return array_values(array_unique($ids));
    }

    /** Resolve cached term IDs, skipping terms deleted since the transient was written. */
    private static function resolve_terms(array $ids, string $taxonomy): array
    {
        $terms = [];
        foreach ($ids as $id) {
            $term = get_term(absint($id), $taxonomy);
            if ($term instanceof \WP_Term && !is_wp_error($term)) {
                $terms[] = $term;
            }
        }
        return $terms;
    }
}
