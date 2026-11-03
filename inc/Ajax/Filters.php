<?php
/**
 * Filters — native replacement for JetSearch + JetSmartFilters.
 *
 * - Live AJAX product search (text, product_cat, price range, true_sale,
 *   pa_* attributes, sorting) returning rendered cards as JSON.
 * - Project filters (project_type, city) with per-term counts.
 *
 * Security: nonce on every endpoint, all input sanitized, all queries via
 * WP_Query (no raw SQL → SQLi-safe), output escaped via wp_send_json.
 *
 * @package ErfanSanat\Theme\Ajax
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

final class Filters
{
    public const NONCE = 'erfansanat_filters_nonce';

    public static function hooks(): void
    {
        add_action('wp_ajax_erfansanat_search_products', [self::class, 'search_products']);
        add_action('wp_ajax_nopriv_erfansanat_search_products', [self::class, 'search_products']);
        add_action('wp_ajax_erfansanat_filter_products', [self::class, 'filter_products']);
        add_action('wp_ajax_nopriv_erfansanat_filter_products', [self::class, 'filter_products']);
        add_action('wp_ajax_erfansanat_filter_projects', [self::class, 'filter_projects']);
        add_action('wp_ajax_nopriv_erfansanat_filter_projects', [self::class, 'filter_projects']);
        add_action('wp_enqueue_scripts', [self::class, 'assets']);
    }

    private static function verify(): void
    {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), self::NONCE)) {
            wp_send_json_error(['message' => 'bad nonce'], 403);
        }
    }

    public static function assets(): void
    {
        wp_enqueue_script('erfansanat-filters', ERFANSANAT_THEME_URI . 'assets/js/filters.js', ['erfansanat-main'], ERFANSANAT_THEME_VERSION, true);
        wp_localize_script('erfansanat-filters', 'ErfanSanatAjax', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce(self::NONCE),
        ]);
    }

    /**
     * Live product search: ?s= term → matching items as cards.
     */
    public static function search_products(): void
    {
        self::verify();
        $s = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
        $q = new \WP_Query([
            'post_type'      => ['product', 'projects', 'post'],
            's'              => $s,
            'posts_per_page' => 12,
            'no_found_rows'  => true,
        ]);
        wp_send_json_success(['html' => self::render_cards($q), 'count' => $q->found_posts]);
    }

    /**
     * Smart product filters.
     */
    public static function filter_products(): void
    {
        self::verify();
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => isset($_GET['per_page']) ? absint($_GET['per_page']) : 12,
            'no_found_rows'  => false,
        ];
        if (!empty($_GET['s'])) {
            $args['s'] = sanitize_text_field(wp_unslash($_GET['s']));
        }
        $taxQuery = [];
        if (!empty($_GET['product_cat'])) {
            $taxQuery[] = ['taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => sanitize_text_field(wp_unslash($_GET['product_cat']))];
        }
        foreach ($_GET as $k => $v) {
            if (strpos((string) $k, 'pa_') === 0 && $v) {
                $taxQuery[] = ['taxonomy' => sanitize_key((string) $k), 'field' => 'slug', 'terms' => sanitize_text_field(wp_unslash($v))];
            }
        }
        if ($taxQuery) {
            $args['tax_query'] = $taxQuery;
        }
        $metaQuery = [];
        $min = isset($_GET['min_price']) ? absint($_GET['min_price']) : 0;
        $max = isset($_GET['max_price']) ? absint($_GET['max_price']) : 0;
        if ($min || $max) {
            $range = ['key' => '_price', 'type' => 'NUMERIC'];
            if ($min) { $range['value'] = [$min, $max ?: PHP_INT_MAX]; $range['compare'] = 'BETWEEN'; }
            else { $range['value'] = $max; $range['compare'] = '<='; }
            $metaQuery[] = $range;
        }
        if (!empty($_GET['true_sale']) && in_array($_GET['true_sale'], ['true', 'false'], true)) {
            $metaQuery[] = ['key' => 'true_sale', 'value' => sanitize_text_field(wp_unslash($_GET['true_sale'])), 'compare' => '='];
        }
        if ($metaQuery) {
            $args['meta_query'] = $metaQuery;
        }
        $orderby = isset($_GET['orderby']) ? sanitize_key(wp_unslash($_GET['orderby'])) : 'date';
        if ($orderby === 'price') {
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
        } else {
            $args['orderby'] = in_array($orderby, ['date', 'title', 'meta_value'], true) ? $orderby : 'date';
        }
        $args['order'] = isset($_GET['order']) && strtoupper(sanitize_key(wp_unslash($_GET['order']))) === 'ASC' ? 'ASC' : 'DESC';

        $q = new \WP_Query($args);
        wp_send_json_success([
            'html'       => self::render_cards($q, 'product'),
            'count'      => $q->found_posts,
            'pagination' => paginate_links(['total' => $q->max_num_pages, 'format' => '?paged=%#%']),
        ]);
    }

    /**
     * Project filters by type/city with counts.
     */
    public static function filter_projects(): void
    {
        self::verify();
        $args = ['post_type' => 'projects', 'posts_per_page' => isset($_GET['per_page']) ? absint($_GET['per_page']) : 12, 'no_found_rows' => false];
        $taxQuery = [];
        if (!empty($_GET['project_type'])) {
            $taxQuery[] = ['taxonomy' => 'project_type', 'field' => 'slug', 'terms' => sanitize_text_field(wp_unslash($_GET['project_type']))];
        }
        if (!empty($_GET['city'])) {
            $taxQuery[] = ['taxonomy' => 'city', 'field' => 'slug', 'terms' => sanitize_text_field(wp_unslash($_GET['city']))];
        }
        if ($taxQuery) {
            $args['tax_query'] = $taxQuery;
        }
        if (!empty($_GET['s'])) {
            $args['s'] = sanitize_text_field(wp_unslash($_GET['s']));
        }
        $q = new \WP_Query($args);

        $counts = [];
        foreach (['project_type', 'city'] as $tax) {
            $terms = get_terms(['taxonomy' => $tax, 'hide_empty' => true]);
            if (!is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $counts[$tax][$term->slug] = $term->count;
                }
            }
        }
        wp_send_json_success([
            'html'   => self::render_cards($q, 'project'),
            'count'  => $q->found_posts,
            'counts' => $counts,
        ]);
    }

    private static function render_cards(\WP_Query $q, ?string $forceType = null): string
    {
        ob_start();
        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();
                $type = $forceType ?: get_post_type();
                $card = $type === 'product' ? 'product' : ($type === 'projects' ? 'project' : 'post');
                get_template_part('template-parts/cards/card', $card);
            }
            wp_reset_postdata();
        } else {
            echo '<p class="text-sm font-bold text-slate-500">' . esc_html__('نتیجه‌ای یافت نشد.', 'erfansanat') . '</p>';
        }
        return (string) ob_get_clean();
    }
}
