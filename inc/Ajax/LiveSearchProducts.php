<?php
/** Live WooCommerce product search for the global search modal. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Ajax;

use WP_Query;
use WP_Post;
use ErfanSanat\Theme\Contracts\ServiceInterface;

final class LiveSearchProducts implements ServiceInterface
{
    private const ROUTE = 'product_search';
    private const RESULT_LIMIT = 6;
    private const MIN_QUERY_LENGTH = 2;

    public function register(): void
    {
        add_filter('erfan_sanat_ajax_routes', [$this, 'registerRoute']);
    }

    /** @param mixed $routes @return array<string,callable> */
    public function registerRoute(mixed $routes): array
    {
        $routes = is_array($routes) ? $routes : [];
        $routes[self::ROUTE] = [$this, 'search'];
        return $routes;
    }

    /** @param array<string,mixed> $input @return array{data:array{items:array<int,int>},html:string,count:int} */
    public function search(array $input): array
    {
        $rawQuery = $input['query'] ?? $input['q'] ?? $input['search'] ?? '';
        $term = is_scalar($rawQuery) ? sanitize_text_field((string) $rawQuery) : '';
        $term = trim($term);
        if (function_exists('mb_substr')) {
            $term = mb_substr($term, 0, 80);
            $length = mb_strlen($term);
        } else {
            $term = substr($term, 0, 80);
            $length = strlen($term);
        }
        if ($length < self::MIN_QUERY_LENGTH) {
            return ['data' => ['items' => []], 'html' => '', 'count' => 0];
        }

        global $wpdb;
        $like = '%' . $wpdb->esc_like($term) . '%';
        $searchFilter = static function (string $search, WP_Query $query) use ($wpdb, $like): string {
            if (!$query->get('_es_live_product_search')) {
                return $search;
            }
            return (string) $wpdb->prepare(
                " AND ({$wpdb->posts}.post_title LIKE %s OR EXISTS (SELECT 1 FROM {$wpdb->postmeta} AS es_product_sku WHERE es_product_sku.post_id = {$wpdb->posts}.ID AND es_product_sku.meta_key = '_sku' AND es_product_sku.meta_value LIKE %s))",
                $like,
                $like
            );
        };

        add_filter('posts_search', $searchFilter, 10, 2);
        try {
            $query = new WP_Query([
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page' => self::RESULT_LIMIT,
                'orderby' => 'title',
                'order' => 'ASC',
                's' => $term,
                'no_found_rows' => false,
                'ignore_sticky_posts' => true,
                'suppress_filters' => false,
                '_es_live_product_search' => true,
            ]);
        } finally {
            remove_filter('posts_search', $searchFilter, 10);
        }

        $items = [];
        $html = '';
        $hadCardPost = array_key_exists('es_card_post', $GLOBALS);
        $previousCardPost = $GLOBALS['es_card_post'] ?? null;
        foreach ($query->posts as $post) {
            if (!$post instanceof WP_Post) {
                continue;
            }
            $items[] = (int) $post->ID;
            $GLOBALS['es_card_post'] = $post;
            ob_start();
            get_template_part('template-parts/cards/card-product');
            $html .= (string) ob_get_clean();
        }
        if ($hadCardPost) {
            $GLOBALS['es_card_post'] = $previousCardPost;
        } else {
            unset($GLOBALS['es_card_post']);
        }

        return [
            'data' => ['items' => $items],
            'html' => $html,
            'count' => (int) $query->found_posts,
        ];
    }
}
