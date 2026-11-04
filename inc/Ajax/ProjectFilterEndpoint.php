<?php
/** AJAX endpoint rendering filtered project cards and archive pagination. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Ajax;

use WP_Post;
use WP_Query;
use ErfanSanat\Theme\Contracts\ServiceInterface;
use ErfanSanat\Theme\Filters\ProjectFilterQuery;

final class ProjectFilterEndpoint implements ServiceInterface
{
    private const ROUTE = 'project_filter';

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
        $args = ProjectFilterQuery::buildArgs($input);
        $query = new WP_Query($args);
        $items = [];
        $html = '';
        $hadCardPost = array_key_exists('es_card_post', $GLOBALS);
        $previousCardPost = $GLOBALS['es_card_post'] ?? null;
        foreach ($query->posts as $post) {
            if (!$post instanceof WP_Post) continue;
            $items[] = (int) $post->ID;
            $GLOBALS['es_card_post'] = $post;
            ob_start();
            get_template_part('template-parts/cards/card-project');
            $html .= (string) ob_get_clean();
        }
        if ($hadCardPost) $GLOBALS['es_card_post'] = $previousCardPost;
        else unset($GLOBALS['es_card_post']);

        $pages = (int) $query->max_num_pages;
        $page = max(1, (int) $args['paged']);
        return [
            'data' => [
                'items' => $items,
                'page' => $page,
                'pages' => $pages,
                'pagination_html' => $this->renderPagination($pages, $page),
                'empty_html' => $items === [] ? '<div data-project-empty="true" class="mt-6 rounded-2xl bg-slate-50 p-8 text-center text-sm text-slate-500">پروژه‌ای با این مشخصات پیدا نشد.</div>' : '',
                'empty' => $items === [],
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
        $html .= '<button type="button" data-page-next="true" class="rounded-xl border px-3 py-2" aria-label="صفحه بعد">بعدی</button><span data-pagination-summary="true" class="sr-only"></span>';
        return $html;
    }
}
