<?php
/** Live projects and blog posts search for the global search modal. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Ajax;

use WP_Post;
use WP_Query;
use ErfanSanat\Theme\Contracts\ServiceInterface;

final class LiveSearchProjectsPosts implements ServiceInterface
{
    private const ROUTE = 'content_search';
    private const RESULT_LIMIT_PER_GROUP = 4;
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

    /**
     * @param array<string,mixed> $input
     * @return array{data:array{groups:array<string,array{items:array<int,int>,html:string,count:int}>},html:string,count:int}
     */
    public function search(array $input): array
    {
        $rawQuery = $input['query'] ?? $input['q'] ?? $input['search'] ?? '';
        $term = is_scalar($rawQuery) ? trim(sanitize_text_field((string) $rawQuery)) : '';
        if (function_exists('mb_substr')) {
            $term = mb_substr($term, 0, 80);
            $length = mb_strlen($term);
        } else {
            $term = substr($term, 0, 80);
            $length = strlen($term);
        }
        if ($length < self::MIN_QUERY_LENGTH) {
            return ['data' => ['groups' => ['projects' => ['items' => [], 'html' => '', 'count' => 0], 'posts' => ['items' => [], 'html' => '', 'count' => 0]]], 'html' => '', 'count' => 0];
        }

        $groups = [
            'projects' => $this->searchGroup('projects', $term),
            'posts' => $this->searchGroup('post', $term),
        ];
        $html = '';
        $count = 0;
        foreach ($groups as $key => $group) {
            $count += $group['count'];
            if ($group['html'] === '') {
                continue;
            }
            $label = $key === 'projects' ? 'پروژه‌ها' : 'مقالات';
            $html .= '<section data-search-group="' . esc_attr($key) . '" class="space-y-2"><h4 class="text-[11px] font-extrabold text-slate-500">' . esc_html($label) . '</h4>' . $group['html'] . '</section>';
        }

        return ['data' => ['groups' => $groups], 'html' => $html, 'count' => $count];
    }

    /** @return array{items:array<int,int>,html:string,count:int} */
    private function searchGroup(string $postType, string $term): array
    {
        $query = new WP_Query([
            'post_type' => $postType,
            'post_status' => 'publish',
            'posts_per_page' => self::RESULT_LIMIT_PER_GROUP,
            'orderby' => 'date',
            'order' => 'DESC',
            's' => $term,
            'ignore_sticky_posts' => true,
            'no_found_rows' => false,
        ]);

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
            get_template_part($postType === 'projects' ? 'template-parts/cards/card-project' : 'template-parts/cards/card-post');
            $html .= (string) ob_get_clean();
        }
        if ($hadCardPost) {
            $GLOBALS['es_card_post'] = $previousCardPost;
        } else {
            unset($GLOBALS['es_card_post']);
        }

        return ['items' => $items, 'html' => $html, 'count' => (int) $query->found_posts];
    }
}
