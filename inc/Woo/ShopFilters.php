<?php
/**
 * فیلترهای GET آرشیو فروشگاه — اعمال امن روی pre_get_posts تا رسیدن فاز ۸ (AJAX)
 *
 * پارامترها: s (جستجو)، purchase (all|online|inquiry)، price (سقف)،
 * ip (term pa_ip-rating)، voltage (term pa_input-voltage)، sort (default|popular|rating|price-asc|price-desc)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Woo;

final class ShopFilters {

    public static function init(): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        add_action('pre_get_posts', [self::class, 'apply'], 20);
    }

    /** دریافت امن پارامتر GET */
    private static function g(string $key): string {
        if (!isset($_GET[$key]) || !is_string($_GET[$key])) {
            return '';
        }
        return sanitize_text_field((string) wp_unslash((string) $_GET[$key]));
    }

    /** args کوئری بر اساس GET — عمومی برای تست */
    public static function query_args(): array {
        $args = [];
        $search = self::g('s');
        if ($search !== '') {
            $args['s'] = $search;
        }
        $purchase = self::g('purchase');
        if ($purchase === 'online') {
            $args['meta_query'][] = ['key' => 'true_sale', 'value' => 'false', 'compare' => '!='];
        } elseif ($purchase === 'inquiry') {
            $args['meta_query'][] = ['key' => 'true_sale', 'value' => 'false'];
        }
        $price = self::g('price');
        if ($price !== '' && ctype_digit($price) && (int) $price > 0) {
            $args['meta_query'][] = [
                'key'     => '_price',
                'value'   => (int) $price,
                'type'    => 'numeric',
                'compare' => '<=',
            ];
        }
        $tax_query = [];
        $ip = self::g('ip');
        if ($ip !== '' && $ip !== 'all') {
            $tax_query[] = ['taxonomy' => 'pa_ip-rating', 'field' => 'slug', 'terms' => $ip];
        }
        $volt = self::g('voltage');
        if ($volt !== '' && $volt !== 'all') {
            $tax_query[] = ['taxonomy' => 'pa_input-voltage', 'field' => 'slug', 'terms' => $volt];
        }
        if ($tax_query !== []) {
            $args['tax_query'] = $tax_query;
        }
        $sort = self::g('sort');
        if ($sort === 'popular') {
            $args['meta_key'] = 'number2-viewc';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        } elseif ($sort === 'rating') {
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        } elseif ($sort === 'price-asc' || $sort === 'price-desc') {
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = $sort === 'price-asc' ? 'ASC' : 'DESC';
        }
        return $args;
    }

    /** اعمال روی کوئری اصلی آرشیو محصول */
    public static function apply(object $query): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        if (!function_exists('is_post_type_archive') || !is_post_type_archive('product') || !$query->is_main_query()) {
            return;
        }
        foreach (self::query_args() as $k => $v) {
            if ($k === 'meta_query') {
                $existing = (array) ($query->get('meta_query') ?? []);
                $query->set('meta_query', array_merge($existing, (array) $v));
                continue;
            }
            $query->set($k, $v);
        }
        $per = (int) es_option('archives.products_per_page', '20');
        $query->set('posts_per_page', max(1, min(100, $per)));
    }
}
