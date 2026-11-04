<?php
/** Safe WP_Query argument builder for the product archive filters. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Filters;

final class ProductFilterQuery
{
    private const PAGE_SIZE = 12;
    private const SORTS = ['default', 'price-asc', 'price-desc', 'rating', 'views'];

    /** @param array<string,mixed> $input @return array<string,mixed> */
    public static function buildArgs(array $input): array
    {
        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => self::PAGE_SIZE,
            'ignore_sticky_posts' => true,
            'no_found_rows' => false,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
        ];

        $search = $input['search'] ?? $input['query'] ?? '';
        if (FilterRegistry::isEnabled('search') && is_scalar($search)) {
            $search = trim(sanitize_text_field((string) $search));
            if ($search !== '') $args['s'] = $search;
        }

        $rawCategories = $input['product_cat'] ?? [];
        $rawCategories = is_array($rawCategories) ? $rawCategories : [$rawCategories];
        $categoryTerms = [];
        foreach ($rawCategories as $rawCategory) {
            if (!is_scalar($rawCategory)) continue;
            $slug = sanitize_title((string) $rawCategory);
            if ($slug !== '') $categoryTerms[] = $slug;
        }
        $categoryTerms = array_values(array_unique($categoryTerms));
        if (FilterRegistry::isEnabled('product_cat') && $categoryTerms !== []) {
            $args['tax_query'] = [[
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $categoryTerms,
                'operator' => 'IN',
            ]];
        }

        $min = self::priceBound($input['min_price'] ?? $input['price_min'] ?? null);
        $max = self::priceBound($input['max_price'] ?? $input['price_max'] ?? null);
        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }
        $priceClause = ['key' => '_price', 'type' => 'NUMERIC'];
        if ($min !== null && $max !== null) {
            $priceClause['value'] = [$min, $max];
            $priceClause['compare'] = 'BETWEEN';
        } elseif ($min !== null) {
            $priceClause['value'] = $min;
            $priceClause['compare'] = '>=';
        } elseif ($max !== null) {
            $priceClause['value'] = $max;
            $priceClause['compare'] = '<=';
        }
        if (FilterRegistry::isEnabled('price') && isset($priceClause['value'])) {
            $args['meta_query'] = [$priceClause];
        }

        $sort = FilterRegistry::isEnabled('sort') && isset($input['sort']) && is_scalar($input['sort']) ? sanitize_key((string) $input['sort']) : 'default';
        if (!in_array($sort, self::SORTS, true)) $sort = 'default';
        switch ($sort) {
            case 'price-asc':
                $args['meta_key'] = '_price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                break;
            case 'price-desc':
                $args['meta_key'] = '_price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'rating':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'views':
                $args['meta_key'] = 'number2-viewc';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
        }

        return $args;
    }

    /** @param mixed $value */
    private static function priceBound(mixed $value): ?float
    {
        if (!is_scalar($value) || trim((string) $value) === '' || !is_numeric($value)) {
            return null;
        }
        $price = (float) $value;
        return is_finite($price) && $price >= 0 ? $price : null;
    }
}
