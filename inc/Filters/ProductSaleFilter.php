<?php
/** Query-argument modifier for the legacy true_sale product purchase modes. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Filters;

final class ProductSaleFilter
{
    /** @param array<string,mixed> $queryArgs @return array<string,mixed> */
    public static function apply(array $queryArgs, mixed $mode): array
    {
        $mode = is_scalar($mode) ? sanitize_key((string) $mode) : 'all';
        if (!FilterRegistry::isEnabled('true_sale')) $mode = 'all';
        if (!in_array($mode, ['all', 'online', 'call'], true) || $mode === 'all') {
            return $queryArgs;
        }

        $existing = isset($queryArgs['meta_query']) && is_array($queryArgs['meta_query']) ? $queryArgs['meta_query'] : [];
        if ($mode === 'call') {
            $saleClause = ['key' => 'true_sale', 'value' => 'false', 'compare' => '='];
        } else {
            $saleClause = [
                'relation' => 'OR',
                ['key' => 'true_sale', 'value' => 'false', 'compare' => '!='],
                ['key' => 'true_sale', 'compare' => 'NOT EXISTS'],
            ];
        }

        $queryArgs['meta_query'] = $existing === []
            ? [$saleClause]
            : array_merge(['relation' => 'AND'], $existing, [$saleClause]);
        return $queryArgs;
    }
}
