<?php
/** AJAX endpoint combining the product archive's search and filter builders. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Ajax;

use WP_Post;
use WP_Query;
use ErfanSanat\Theme\Contracts\ServiceInterface;
use ErfanSanat\Theme\Filters\ProductAttributeFilters;
use ErfanSanat\Theme\Filters\ProductFilterQuery;
use ErfanSanat\Theme\Filters\ProductSaleFilter;

final class ProductFilterEndpoint implements ServiceInterface
{
    private const ROUTE = 'product_filter';

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

    /** @param array<string,mixed> $input @return array{data:array<string,mixed>,html:string,count:int} */
    public function search(array $input): array
    {
        $filters = isset($input['filters']) && is_array($input['filters']) ? array_merge($input, $input['filters']) : $input;
        $args = ProductFilterQuery::buildArgs($filters);
        $args = ProductSaleFilter::apply($args, $filters['true_sale'] ?? 'all');
        $attributes = isset($filters['attributes']) && is_array($filters['attributes']) ? $filters['attributes'] : [];
        $args = ProductAttributeFilters::apply($args, $attributes);
        $args['paged'] = max(1, absint($filters['page'] ?? 1));

        $query = new WP_Query($args);
        $ids = [];
        $html = '';
        $hadCardPost = array_key_exists('es_card_post', $GLOBALS);
        $previousCardPost = $GLOBALS['es_card_post'] ?? null;
        foreach ($query->posts as $post) {
            if (!$post instanceof WP_Post) continue;
            $ids[] = (int) $post->ID;
            $GLOBALS['es_card_post'] = $post;
            ob_start();
            get_template_part('template-parts/cards/card-product');
            $html .= (string) ob_get_clean();
        }
        if ($hadCardPost) $GLOBALS['es_card_post'] = $previousCardPost;
        else unset($GLOBALS['es_card_post']);

        $pagination = '';
        if ((int) $query->max_num_pages > 1) {
            $pagination = (string) paginate_links([
                'base' => '#page-%#%',
                'format' => '',
                'current' => max(1, (int) $args['paged']),
                'total' => (int) $query->max_num_pages,
                'type' => 'list',
                'prev_text' => 'قبلی',
                'next_text' => 'بعدی',
            ]);
        }

        $emptyHtml = '<div data-product-empty="true" class="mt-6 rounded-2xl bg-slate-50 p-8 text-center text-sm text-slate-500">محصولی مطابق فیلترهای انتخابی پیدا نشد.</div>';
        return [
            'data' => [
                'items' => $ids,
                'pages' => (int) $query->max_num_pages,
                'page' => max(1, (int) $args['paged']),
                'pagination_html' => $pagination,
                'empty' => $ids === [],
                'empty_html' => $ids === [] ? $emptyHtml : '',
            ],
            'html' => $html,
            'count' => (int) $query->found_posts,
        ];
    }
}
