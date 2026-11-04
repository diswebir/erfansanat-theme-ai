<?php
/** AJAX endpoint for native blog category and search filtering. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Ajax;

use WP_Post;
use WP_Query;
use ErfanSanat\Theme\Contracts\ServiceInterface;
use ErfanSanat\Theme\Filters\FilterRegistry;

final class BlogFilterEndpoint implements ServiceInterface
{
    private const ROUTE = 'blog_filter';
    private const PAGE_SIZE = 6;

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
        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => self::PAGE_SIZE,
            'paged' => max(1, absint($input['page'] ?? 1)),
            'ignore_sticky_posts' => true,
            'no_found_rows' => false,
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        if (FilterRegistry::isEnabled('search')) {
            $rawSearch = $input['search'] ?? $input['query'] ?? '';
            if (is_scalar($rawSearch)) {
                $search = trim(sanitize_text_field((string) $rawSearch));
                if ($search !== '') $args['s'] = $search;
            }
        }
        $rawCategory = $input['category'] ?? '';
        if (is_scalar($rawCategory)) {
            $category = trim(sanitize_text_field((string) $rawCategory));
            if ($category !== '' && $category !== 'همه' && strtolower($category) !== 'all') {
                $term = get_term_by('slug', sanitize_title($category), 'category');
                if (!$term) $term = get_term_by('name', $category, 'category');
                if ($term instanceof \WP_Term) $args['category__in'] = [(int) $term->term_id];
                else $args['post__in'] = [0];
            }
        }

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
            get_template_part('template-parts/cards/card-post');
            $html .= (string) ob_get_clean();
        }
        if ($hadCardPost) $GLOBALS['es_card_post'] = $previousCardPost;
        else unset($GLOBALS['es_card_post']);

        $pages = (int) $query->max_num_pages;
        $page = max(1, (int) $args['paged']);
        return [
            'data' => [
                'items' => $ids,
                'page' => $page,
                'pages' => $pages,
                'pagination_html' => $this->renderPagination($pages, $page),
                'empty' => $ids === [],
                'empty_html' => $ids === [] ? '<div data-blog-empty="true" class="mt-6 rounded-2xl bg-slate-50 p-8 text-center text-sm text-slate-500">مقاله‌ای با این مشخصات پیدا نشد.</div>' : '',
            ],
            'html' => $html,
            'count' => (int) $query->found_posts,
        ];
    }

    private function renderPagination(int $pages, int $currentPage): string
    {
        if ($pages <= 1) return '';
        $html = '<button type="button" data-page-prev="true" class="rounded-xl border px-3 py-2" aria-label="صفحه قبل">قبلی</button>';
        for ($page = 1; $page <= $pages; $page++) {
            $html .= '<button type="button" data-page-btn="' . esc_attr((string) $page) . '" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"' . ($page === $currentPage ? ' aria-current="page"' : '') . '>' . esc_html(number_format_i18n($page)) . '</button>';
        }
        return $html . '<button type="button" data-page-next="true" class="rounded-xl border px-3 py-2" aria-label="صفحه بعد">بعدی</button><span data-pagination-summary="true" class="sr-only"></span>';
    }
}
