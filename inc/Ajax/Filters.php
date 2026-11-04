<?php
/**
 * Public, nonce-protected AJAX search and archive filters.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Ajax;

defined('ABSPATH') || exit;

/**
 * Handles global product/project search and composable archive filtering.
 */
final class Filters
{
    private const NONCE_ACTION = 'ese_public_filters';
    private const PAGE_SIZE = 9;
    private const SEARCH_SIZE = 4;

    /** Register authenticated and public AJAX endpoints. */
    public static function register(): void
    {
        add_action('wp_ajax_ese_live_search', [self::class, 'dispatch']);
        add_action('wp_ajax_nopriv_ese_live_search', [self::class, 'dispatch']);
        add_action('wp_ajax_ese_filter_products', [self::class, 'dispatch']);
        add_action('wp_ajax_nopriv_ese_filter_products', [self::class, 'dispatch']);
        add_action('wp_ajax_ese_filter_projects', [self::class, 'dispatch']);
        add_action('wp_ajax_nopriv_ese_filter_projects', [self::class, 'dispatch']);
    }

    /** Dispatch a public AJAX request after nonce validation. */
    public static function dispatch(): void
    {
        if ('POST' !== strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'POST'))) {
            wp_send_json_error(['message' => __('فقط درخواست POST پذیرفته می‌شود.', 'erfansanat-theme')], 405);
        }
        if (!self::verify_nonce(isset($_POST['nonce']) ? wp_unslash($_POST['nonce']) : '')) {
            wp_send_json_error(['message' => __('درخواست نامعتبر یا منقضی شده است.', 'erfansanat-theme')], 403);
        }

        $rawAction = isset($_POST['action']) ? wp_unslash($_POST['action']) : '';
        $action = is_scalar($rawAction) ? sanitize_key((string) $rawAction) : '';
        $input = self::unslash_input($_POST);
        $started = microtime(true);

        if ('ese_live_search' === $action) {
            $query = self::clean_text($input['query'] ?? '', 100);
            $results = self::live_search($query);
            $results['duration_ms'] = round((microtime(true) - $started) * 1000, 2);
            wp_send_json_success($results);
        }

        if ('ese_filter_products' === $action) {
            $results = self::filter_products($input);
            $results['duration_ms'] = round((microtime(true) - $started) * 1000, 2);
            wp_send_json_success($results);
        }

        if ('ese_filter_projects' === $action) {
            $results = self::filter_projects($input);
            $results['duration_ms'] = round((microtime(true) - $started) * 1000, 2);
            wp_send_json_success($results);
        }

        wp_send_json_error(['message' => __('درخواست شناخته نشد.', 'erfansanat-theme')], 400);
    }

    /** Verify the shared front-end AJAX nonce. */
    public static function verify_nonce($nonce): bool
    {
        return is_string($nonce) && false !== wp_verify_nonce(sanitize_text_field($nonce), self::NONCE_ACTION);
    }

    /**
     * Build a bounded product query from the supported filter whitelist.
     *
     * @param array<string, mixed> $input Request filters.
     * @return array<string, mixed>
     */
    public static function build_product_query_args(array $input): array
    {
        $page = max(1, min(1000, absint($input['page'] ?? 1)));
        $perPage = max(1, min(24, absint($input['per_page'] ?? self::PAGE_SIZE)));
        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $perPage,
            'paged' => $page,
            'no_found_rows' => false,
            'ignore_sticky_posts' => true,
            'suppress_filters' => false,
        ];
        $search = self::clean_text($input['query'] ?? '', 100);
        if ('' !== $search) {
            $args['s'] = $search;
            $args['ese_product_search'] = $search;
        }

        $taxQuery = ['relation' => 'AND'];
        foreach (['product_cat' => 'category', 'product_tag' => 'tag'] as $taxonomy => $key) {
            $slug = sanitize_title(self::clean_text($input[$key] ?? '', 100));
            if ('' !== $slug && taxonomy_exists($taxonomy)) {
                $taxQuery[] = [
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => [$slug],
                ];
            }
        }

        $rawSaleMode = $input['sale'] ?? 'all';
        $saleMode = is_scalar($rawSaleMode) ? sanitize_key((string) $rawSaleMode) : '';
        if (!in_array($saleMode, ['all', 'online', 'inquiry'], true)) {
            $args['post__in'] = [0];
            $saleMode = 'all';
        }
        if (in_array($saleMode, ['online', 'inquiry'], true)) {
            $metaQuery = [
                'key' => 'true_sale',
                'value' => 'online' === $saleMode ? 'true' : 'false',
                'compare' => '=',
            ];
        } else {
            $metaQuery = null;
        }

        $minPrice = max(0, min(50000000, absint($input['min_price'] ?? 0)));
        $maxPrice = max(0, min(50000000, absint($input['max_price'] ?? 3000000)));
        if ($maxPrice < $minPrice) {
            $maxPrice = $minPrice;
        }
        if ($minPrice > 0 || $maxPrice < 3000000) {
            $priceQuery = [
                'key' => '_price',
                'value' => [$minPrice, $maxPrice],
                'type' => 'NUMERIC',
                'compare' => 'BETWEEN',
            ];
            $metaQuery = $metaQuery ? ['relation' => 'AND', $metaQuery, $priceQuery] : $priceQuery;
        }
        if ($metaQuery) {
            $args['meta_query'] = is_array($metaQuery) && isset($metaQuery['relation']) ? $metaQuery : [$metaQuery];
        }

        $ipValues = self::clean_list($input['ip'] ?? []);
        $voltageValues = self::clean_list($input['voltage'] ?? []);
        foreach (['pa_ip-rating' => $ipValues, 'pa_input-voltage' => $voltageValues] as $taxonomy => $values) {
            if (!$values) {
                continue;
            }
            if (taxonomy_exists($taxonomy)) {
                $slugs = self::attribute_term_slugs($taxonomy, $values);
                $taxQuery[] = [
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $slugs ?: ['__ese_no_matching_attribute__'],
                    'operator' => 'IN',
                ];
            } else {
                $alternatives = ['relation' => 'OR'];
                foreach ($values as $value) {
                    $alternatives[] = [
                        'key' => $taxonomy,
                        'value' => $value,
                        'compare' => 'LIKE',
                    ];
                }
                if (!isset($args['meta_query'])) {
                    $args['meta_query'] = ['relation' => 'AND'];
                }
                if (!isset($args['meta_query']['relation'])) {
                    $args['meta_query'] = ['relation' => 'AND', $args['meta_query']];
                }
                $args['meta_query'][] = $alternatives;
            }
        }
        if (count($taxQuery) > 1) {
            $args['tax_query'] = $taxQuery;
        }

        $rawSort = $input['orderby'] ?? 'menu_order';
        $sort = is_scalar($rawSort) ? sanitize_key((string) $rawSort) : '';
        $sorts = [
            'menu_order' => ['orderby' => ['menu_order' => 'ASC', 'date' => 'DESC']],
            'newest' => ['orderby' => 'date', 'order' => 'DESC'],
            'date' => ['orderby' => 'date', 'order' => 'DESC'],
            'title' => ['orderby' => 'title', 'order' => 'ASC'],
            'price' => ['meta_key' => '_price', 'orderby' => 'meta_value_num', 'order' => 'ASC'],
            'price-desc' => ['meta_key' => '_price', 'orderby' => 'meta_value_num', 'order' => 'DESC'],
            'popularity' => ['meta_key' => 'number2-viewc', 'orderby' => 'meta_value_num', 'order' => 'DESC'],
            'rating' => ['meta_key' => '_wc_average_rating', 'orderby' => 'meta_value_num', 'order' => 'DESC'],
        ];
        $args = array_merge($args, $sorts[$sort] ?? $sorts['menu_order']);
        return $args;
    }

    /**
     * Build a bounded project query from the supported filter whitelist.
     *
     * @param array<string, mixed> $input Request filters.
     * @return array<string, mixed>
     */
    public static function build_project_query_args(array $input): array
    {
        $args = [
            'post_type' => 'projects',
            'post_status' => 'publish',
            'posts_per_page' => max(1, min(24, absint($input['per_page'] ?? self::PAGE_SIZE))),
            'paged' => max(1, min(1000, absint($input['page'] ?? 1))),
            'no_found_rows' => false,
            'ignore_sticky_posts' => true,
            'suppress_filters' => false,
        ];
        $search = self::clean_text($input['query'] ?? '', 100);
        if ('' !== $search) {
            $args['s'] = $search;
        }
        $taxQuery = ['relation' => 'AND'];
        foreach (['project_type' => 'type', 'city' => 'city'] as $taxonomy => $key) {
            $value = self::clean_text($input[$key] ?? '', 100);
            if ('' === $value || 'all' === strtolower($value) || 'همه' === $value || !taxonomy_exists($taxonomy)) {
                continue;
            }
            $term = get_term_by('slug', sanitize_title($value), $taxonomy);
            if (!$term instanceof \WP_Term) {
                $term = get_term_by('name', $value, $taxonomy);
            }
            if ($term instanceof \WP_Term) {
                $taxQuery[] = ['taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => [(int) $term->term_id]];
            } else {
                $taxQuery[] = ['taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => ['__ese_no_matching_term__']];
            }
        }
        if (count($taxQuery) > 1) {
            $args['tax_query'] = $taxQuery;
        }
        $rawSort = $input['orderby'] ?? 'newest';
        $sort = is_scalar($rawSort) ? sanitize_key((string) $rawSort) : '';
        if ('popular' === $sort) {
            $args['meta_key'] = 'number2-viewc';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        } elseif ('rating' === $sort) {
            $args['meta_key'] = 'project-rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        } else {
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
        }
        return $args;
    }

    /** Query and render one product-filter page. */
    public static function filter_products(array $input): array
    {
        $started = microtime(true);
        $query = self::query_products(self::build_product_query_args($input));
        $html = self::render_product_items($query->posts);
        $total = max(0, (int) $query->found_posts);
        $perPage = max(1, (int) $query->query_vars['posts_per_page']);
        $page = max(1, (int) $query->query_vars['paged']);
        wp_reset_postdata();
        return [
            'html' => $html,
            'total' => $total,
            'pages' => max(1, (int) ceil($total / $perPage)),
            'page' => $page,
            'duration_ms' => round((microtime(true) - $started) * 1000, 2),
        ];
    }

    /** Query and render one project-filter page. */
    public static function filter_projects(array $input): array
    {
        $started = microtime(true);
        $query = new \WP_Query(self::build_project_query_args($input));
        $html = self::render_project_items($query->posts);
        $total = max(0, (int) $query->found_posts);
        $perPage = max(1, (int) $query->query_vars['posts_per_page']);
        $page = max(1, (int) $query->query_vars['paged']);
        wp_reset_postdata();
        return [
            'html' => $html,
            'total' => $total,
            'pages' => max(1, (int) ceil($total / $perPage)),
            'page' => $page,
            'duration_ms' => round((microtime(true) - $started) * 1000, 2),
        ];
    }

    /** Query and render the live-search dropdown results. */
    public static function live_search(string $search): array
    {
        $search = self::clean_text($search, 100);
        if ('' === $search) {
            return ['products_html' => '', 'projects_html' => '', 'product_count' => 0, 'project_count' => 0];
        }
        $products = self::query_products([
            'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => self::SEARCH_SIZE,
            's' => $search, 'ese_product_search' => $search, 'no_found_rows' => true, 'ignore_sticky_posts' => true,
        ]);
        $projects = new \WP_Query([
            'post_type' => 'projects', 'post_status' => 'publish', 'posts_per_page' => self::SEARCH_SIZE,
            's' => $search, 'no_found_rows' => true, 'ignore_sticky_posts' => true,
        ]);
        $productHtml = self::render_search_items($products->posts, 'product');
        $projectHtml = self::render_search_items($projects->posts, 'projects');
        $productCount = (int) $products->post_count;
        $projectCount = (int) $projects->post_count;
        wp_reset_postdata();
        return [
            'products_html' => $productHtml,
            'projects_html' => $projectHtml,
            'product_count' => $productCount,
            'project_count' => $projectCount,
        ];
    }

    /**
     * Run product text search across public content and common product identifiers.
     * The custom SQL fragment is assembled only from fixed columns and prepared LIKE values.
     *
     * @param array<string, mixed> $args Product query arguments.
     */
    private static function query_products(array $args): \WP_Query
    {
        $search = isset($args['ese_product_search']) ? self::clean_text($args['ese_product_search'], 100) : '';
        if ('' === $search) {
            return new \WP_Query($args);
        }
        global $wpdb;
        $searchFilter = static function ($where, $query) use ($wpdb) {
            if (!$query instanceof \WP_Query) {
                return $where;
            }
            $term = self::clean_text($query->get('ese_product_search'), 100);
            if ('' === $term) {
                return $where;
            }
            $like = '%' . $wpdb->esc_like($term) . '%';
            $sql = " AND ({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR {$wpdb->posts}.post_content LIKE %s OR EXISTS (SELECT 1 FROM {$wpdb->postmeta} AS ese_filter_meta WHERE ese_filter_meta.post_id = {$wpdb->posts}.ID AND ese_filter_meta.meta_key IN ('_sku','zireonvan','english_titl') AND ese_filter_meta.meta_value LIKE %s))";
            return $wpdb->prepare($sql, $like, $like, $like, $like);
        };
        add_filter('posts_search', $searchFilter, 10, 2);
        $query = new \WP_Query($args);
        remove_filter('posts_search', $searchFilter, 10);
        return $query;
    }

    /** Build accessible product cards for AJAX responses. */
    private static function render_product_items(array $posts): string
    {
        ob_start();
        $index = 0;
        foreach ($posts as $productPost) {
            if (!$productPost instanceof \WP_Post) {
                continue;
            }
            $productId = (int) $productPost->ID;
            $price = function_exists('wc_get_product') ? wc_get_product($productId) : null;
            $priceValue = is_object($price) && method_exists($price, 'get_price') ? (float) $price->get_price() : (float) get_post_meta($productId, '_price', true);
            $sale = strtolower(trim((string) get_post_meta($productId, 'true_sale', true)));
            if ('true' === $sale) {
                $online = true;
            } elseif ('false' === $sale) {
                $online = false;
            } else {
                $online = is_object($price) && method_exists($price, 'is_purchasable') && $price->is_purchasable();
            }
            $ip = self::attribute_value($productId, 'pa_ip-rating');
            $voltage = self::attribute_value($productId, 'pa_input-voltage');
            $categoryTerms = get_the_terms($productId, 'product_cat');
            $categoryNames = is_array($categoryTerms) ? array_map(static function ($term): string { return $term instanceof \WP_Term ? $term->name : ''; }, $categoryTerms) : [];
            $categoryNames = array_values(array_filter($categoryNames, 'strlen'));
            $searchText = implode(' ', array_filter([
                get_the_title($productId), get_the_excerpt($productId), get_post_meta($productId, 'zireonvan', true),
                get_post_meta($productId, 'english_titl', true), get_post_meta($productId, '_sku', true),
                implode(' ', $categoryNames), $ip, $voltage,
            ], 'is_scalar'));
            $views = get_post_meta($productId, 'number2-viewc', true);
            if ('' === $views) {
                $views = get_post_meta($productId, 'number-viewc', true);
            }
            $rating = get_post_meta($productId, '_wc_average_rating', true);
            ?>
            <article class="min-w-0" data-product-item="true" data-product-category="<?php echo esc_attr((string) reset($categoryNames)); ?>" data-product-categories="<?php echo esc_attr(implode('|', $categoryNames)); ?>" data-product-purchasable="<?php echo $online ? 'true' : 'false'; ?>" data-product-price="<?php echo esc_attr((string) max(0, (int) round($priceValue))); ?>" data-product-ip="<?php echo esc_attr($ip); ?>" data-product-voltage="<?php echo esc_attr($voltage); ?>" data-product-views="<?php echo esc_attr(is_numeric($views) ? (string) max(0, (int) $views) : '0'); ?>" data-product-rating="<?php echo esc_attr(is_numeric($rating) ? (string) $rating : '0'); ?>" data-product-search-text="<?php echo esc_attr(sanitize_text_field((string) $searchText)); ?>">
                <?php get_template_part('template-parts/cards/card-product', null, ['post_id' => $productId, 'index' => $index, 'variant' => 'archive']); ?>
            </article>
            <?php
            $index++;
        }
        return (string) ob_get_clean();
    }

    /** Build accessible project cards for AJAX responses. */
    private static function render_project_items(array $posts): string
    {
        ob_start();
        foreach ($posts as $projectPost) {
            if (!$projectPost instanceof \WP_Post) {
                continue;
            }
            $projectId = (int) $projectPost->ID;
            $GLOBALS['post'] = $projectPost;
            setup_postdata($projectPost);
            $typeTerms = get_the_terms($projectId, 'project_type');
            $cityTerms = get_the_terms($projectId, 'city');
            $typeNames = self::term_names($typeTerms);
            $cityNames = self::term_names($cityTerms);
            $views = class_exists(\ErfanSanat\Theme\Admin\ContentMeta::class) ? \ErfanSanat\Theme\Admin\ContentMeta::get_view_count($projectId) : 0;
            $rating = get_post_meta($projectId, 'project-rating', true);
            $searchText = trim(wp_strip_all_tags(get_the_title($projectId) . ' ' . get_the_excerpt($projectId) . ' ' . implode(' ', $typeNames) . ' ' . implode(' ', $cityNames)));
            ?>
            <article <?php post_class('project-archive-item'); ?> data-project-item="true" data-project-type="<?php echo esc_attr((string) reset($typeNames)); ?>" data-project-types="<?php echo esc_attr(implode('|', $typeNames)); ?>" data-project-location="<?php echo esc_attr((string) reset($cityNames)); ?>" data-project-views="<?php echo esc_attr((string) max(0, (int) $views)); ?>" data-project-rating="<?php echo esc_attr(is_numeric($rating) ? (string) $rating : '0'); ?>" data-project-date="<?php echo esc_attr(get_post_time('c', true, $projectId)); ?>" data-project-search-text="<?php echo esc_attr($searchText); ?>">
                <?php get_template_part('template-parts/cards/card-project', null, ['post_id' => $projectId, 'variant' => 'archive', 'index' => 0]); ?>
            </article>
            <?php
        }
        return (string) ob_get_clean();
    }

    /** Build compact global-search result links. */
    private static function render_search_items(array $posts, string $postType): string
    {
        ob_start();
        foreach ($posts as $resultPost) {
            if (!$resultPost instanceof \WP_Post) {
                continue;
            }
            $resultId = (int) $resultPost->ID;
            $image = class_exists(\ErfanSanat\Theme\HomepageData::class) ? \ErfanSanat\Theme\HomepageData::get_post_image_url($resultId) : get_the_post_thumbnail_url($resultId, 'thumbnail');
            $terms = get_the_terms($resultId, 'product' === $postType ? 'product_cat' : 'project_type');
            if ('projects' === $postType && (!$terms || is_wp_error($terms))) {
                $terms = get_the_terms($resultId, 'city');
            }
            $termName = is_array($terms) && isset($terms[0]) && $terms[0] instanceof \WP_Term ? $terms[0]->name : '';
            $title = get_the_title($resultId);
            $description = get_the_excerpt($resultId);
            $url = get_permalink($resultId);
            ?>
            <a href="<?php echo esc_url($url); ?>" data-search-item="<?php echo esc_attr(sanitize_text_field($title . ' ' . $termName . ' ' . $description)); ?>" class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/70 p-2.5 transition-colors hover:border-emerald-300 hover:bg-emerald-50/50">
                <div class="flex min-w-0 items-center gap-3"><?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="h-10 w-10 shrink-0 rounded-lg object-cover" loading="lazy" /><?php endif; ?><div class="min-w-0"><div class="truncate text-xs font-extrabold text-slate-900"><?php echo esc_html($title); ?></div><?php if ($termName) : ?><div class="text-[10.5px] font-semibold text-slate-500"><?php echo esc_html($termName); ?></div><?php endif; ?></div></div>
                <?php if ('product' === $postType) : $price = function_exists('wc_get_product') ? wc_get_product($resultId) : null; ?><?php if (is_object($price) && method_exists($price, 'get_price_html')) : ?><span class="shrink-0 text-[11px] font-black text-emerald-700"><?php echo wp_kses_post($price->get_price_html()); ?></span><?php endif; ?><?php endif; ?>
            </a>
            <?php
        }
        $html = (string) ob_get_clean();
        if ('' === trim($html)) {
            return '<p class="rounded-xl border border-dashed border-slate-200 p-4 text-xs text-slate-500">' . esc_html__('نتیجه‌ای یافت نشد.', 'erfansanat-theme') . '</p>';
        }
        return $html;
    }

    /** Resolve an attribute's display value from Woo taxonomy terms or legacy metadata. */
    private static function attribute_value(int $postId, string $taxonomy): string
    {
        $terms = taxonomy_exists($taxonomy) ? get_the_terms($postId, $taxonomy) : false;
        if (is_array($terms)) {
            return implode(' ', self::term_names($terms));
        }
        $value = get_post_meta($postId, $taxonomy, true);
        if ('' === $value) {
            $value = get_post_meta($postId, 'test_' . $taxonomy, true);
        }
        return is_scalar($value) ? sanitize_text_field((string) $value) : '';
    }

    /** Extract safe term names. */
    private static function term_names($terms): array
    {
        if (!is_array($terms)) {
            return [];
        }
        $names = [];
        foreach ($terms as $term) {
            if ($term instanceof \WP_Term) {
                $names[] = $term->name;
            }
        }
        return $names;
    }

    /** Convert submitted attribute display values to existing term slugs. */
    private static function attribute_term_slugs(string $taxonomy, array $values): array
    {
        $slugs = [];
        foreach ($values as $value) {
            $term = get_term_by('name', $value, $taxonomy);
            if (!$term instanceof \WP_Term) {
                $term = get_term_by('slug', sanitize_title($value), $taxonomy);
            }
            if ($term instanceof \WP_Term) {
                $slugs[] = $term->slug;
            }
        }
        return array_values(array_unique($slugs));
    }

    /** Sanitize scalar user text with an explicit length bound. */
    private static function clean_text($value, int $maxLength): string
    {
        if (!is_scalar($value)) {
            return '';
        }
        $value = sanitize_text_field((string) $value);
        return function_exists('mb_substr') ? mb_substr($value, 0, $maxLength) : substr($value, 0, $maxLength);
    }

    /** Sanitize a bounded list of scalar filters. */
    private static function clean_list($value): array
    {
        if (!is_array($value)) {
            $value = is_scalar($value) ? explode('|', (string) $value) : [];
        }
        $clean = [];
        foreach (array_slice($value, 0, 20) as $item) {
            $item = self::clean_text($item, 60);
            if ('' !== $item) {
                $clean[] = $item;
            }
        }
        return array_values(array_unique($clean));
    }

    /** Remove slashes from a copy of the incoming request array. */
    private static function unslash_input(array $input): array
    {
        return function_exists('wp_unslash') ? wp_unslash($input) : $input;
    }
}
