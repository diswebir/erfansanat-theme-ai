<?php
/** Safe query-argument builder for projects archive filters. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Filters;

final class ProjectFilterQuery
{
    private const PAGE_SIZE = 6;

    /** @param array<string,mixed> $input @return array<string,mixed> */
    public static function buildArgs(array $input): array
    {
        $args = [
            'post_type' => 'projects',
            'post_status' => 'publish',
            'posts_per_page' => self::PAGE_SIZE,
            'paged' => max(1, absint($input['page'] ?? 1)),
            'ignore_sticky_posts' => true,
            'no_found_rows' => false,
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        if (FilterRegistry::isEnabled('search')) {
            $search = $input['search'] ?? $input['query'] ?? '';
            if (is_scalar($search)) {
                $search = trim(sanitize_text_field((string) $search));
                if ($search !== '') $args['s'] = $search;
            }
        }

        $taxQuery = ['relation' => 'AND'];
        foreach (['project_type' => 'project_type', 'city' => 'city'] as $key => $taxonomy) {
            if (!FilterRegistry::isEnabled($key)) continue;
            $raw = $input[$key] ?? '';
            if (!is_scalar($raw)) continue;
            $value = trim(sanitize_text_field((string) $raw));
            if ($value === '' || $value === 'همه' || strtolower($value) === 'all') continue;
            $term = get_term_by('slug', sanitize_title($value), $taxonomy);
            if (!$term) $term = get_term_by('name', $value, $taxonomy);
            $taxQuery[] = [
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => $term instanceof \WP_Term ? [(int) $term->term_id] : [-1],
                'include_children' => false,
            ];
        }
        if (count($taxQuery) > 1) $args['tax_query'] = $taxQuery;

        $sort = isset($input['sort']) && is_scalar($input['sort']) ? sanitize_key((string) $input['sort']) : 'newest';
        if ($sort === 'popular') {
            $args['meta_key'] = 'number2-viewc';
            $args['orderby'] = 'meta_value_num';
        } elseif ($sort === 'rating') {
            $args['orderby'] = ['date' => 'DESC'];
        }
        return $args;
    }
}
