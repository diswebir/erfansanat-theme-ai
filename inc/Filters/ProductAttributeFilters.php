<?php
/** Query-argument modifier for dashboard-enabled WooCommerce pa_* filters. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Filters;

final class ProductAttributeFilters
{
    /** @param array<string,mixed> $selected @return array<int,array<string,mixed>> */
    public static function buildTaxQuery(array $selected): array
    {
        $allowed = FilterRegistry::attributes();
        $clauses = [];
        foreach ($selected as $taxonomy => $rawValues) {
            if (!is_string($taxonomy) || !isset($allowed[$taxonomy]) || !FilterRegistry::isEnabled('attr_' . $taxonomy)) {
                continue;
            }
            $values = is_array($rawValues) ? $rawValues : [$rawValues];
            $terms = [];
            foreach ($values as $value) {
                if (!is_scalar($value)) continue;
                $slug = sanitize_title((string) $value);
                if ($slug !== '') $terms[] = $slug;
            }
            $terms = array_values(array_unique($terms));
            if ($terms === []) continue;
            $clauses[] = [
                'taxonomy' => $taxonomy,
                'field' => 'slug',
                'terms' => $terms,
                'operator' => 'IN',
            ];
        }
        return $clauses;
    }

    /** @param array<string,mixed> $queryArgs @param array<string,mixed> $selected @return array<string,mixed> */
    public static function apply(array $queryArgs, array $selected): array
    {
        $attributes = self::buildTaxQuery($selected);
        if ($attributes === []) return $queryArgs;

        $existing = isset($queryArgs['tax_query']) && is_array($queryArgs['tax_query']) ? $queryArgs['tax_query'] : [];
        if ($existing === []) {
            $queryArgs['tax_query'] = $attributes;
            return $queryArgs;
        }

        if (isset($existing['relation'])) {
            $existing['relation'] = 'AND';
            $queryArgs['tax_query'] = array_merge($existing, $attributes);
        } else {
            $queryArgs['tax_query'] = array_merge(['relation' => 'AND'], $existing, $attributes);
        }
        return $queryArgs;
    }
}
