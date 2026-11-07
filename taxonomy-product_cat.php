<?php
/**
 * آرشیو دسته محصول — هیرو/گرید/صفحه‌بندی از شل shop (۶.۷) + FAQ دسته از متای ترم (۳.۱۳)
 * برش هیرو: میرور product_cat/wall-washer.html با temp/gen_p68.py.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$es_term = get_queried_object();
if (!is_object($es_term) || ($es_term->term_id ?? 0) < 1) {
    get_template_part('archive-product');
    return;
}
$es_tid = (int) $es_term->term_id;
$es_label = (string) ($es_term->name ?? '');
$es_desc = trim(term_description($es_tid));
$es_q = $GLOBALS['wp_query'] ?? null;
$es_total = (int) ($GLOBALS['__es_archive_total'] ?? ($es_q->found_posts ?? 0));
$es_paged = max(1, (int) get_query_var('paged'));
$es_per = (int) es_option('archives.products_per_page', '20');
$es_pages = max(1, (int) ceil($es_total / max(1, $es_per)));
$es_is_empty = $es_total < 1;

$es_get = static fn(string $k): string => isset($_GET[$k]) && is_string($_GET[$k]) ? (string) $_GET[$k] : '';

$es_opt = static function (string $tax, string $sel) use ($es_get): string {
    $out = '';
    foreach ((array) get_terms(['taxonomy' => $tax, 'hide_empty' => false]) as $t) {
        if (is_wp_error($t) || !is_object($t)) {
            continue;
        }
        $out .= '<option value="' . esc_attr((string) $t->slug) . '"' . ($es_get($sel) === (string) $t->slug ? ' selected=""' : '') . '>' . esc_html((string) $t->name) . '</option>';
    }
    return $out;
};

// درخت دسته‌ها (ترم فعلی = فعال میرور)
$es_cat_tree = '';
foreach ((array) get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]) as $t) {
    if (is_wp_error($t) || !is_object($t) || (int) $t->term_id === $es_tid) {
        continue;
    }
    $es_cat_tree .= '<div class="space-y-1"><a href="' . esc_url(get_term_link($t)) . '" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800"><span class="flex items-center gap-2"><span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500/70"></span><span class="leading-5">' . esc_html((string) $t->name) . '</span></span><span class="rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-slate-100 text-slate-500">' . es_fa_num((string) ($t->count ?? 0)) . '</span></a></div>';
}

echo '<main class="bg-[#f6f8f7]">';
ob_start();
es_get_template('template-parts/archive/hero-cat.php', [
    'h1_a' => 'تولید و قیمت کارخانه ',
    'h1_b' => $es_label,
    'label' => $es_label,
    'desc' => $es_desc !== '' ? $es_desc : (string) es_option('archives.cat_desc_fallback', 'والواشرهای خطی پرقدرت و لینیر لایت‌های معماری با بدنه آلومینیوم اکسترود صنعتی، درجه حفاظت IP68، نمایش ۱۶ میلیون رنگ و لنزهای اپتیکی دقیق.'),
    'img'  => es_get_category_thumb($es_tid),
]);
echo (string) ob_get_clean();

echo '<section id="archive" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-12 lg:px-8 lg:py-16" data-products-archive="true" data-initial-category="' . esc_attr($es_label) . '" data-items-per-page="9"><div class="grid items-start gap-8 lg:grid-cols-12">';

ob_start();
es_get_template('template-parts/archive/product-filters.php', [
    'total' => $es_total, 'search' => $es_get('s'), 'mode' => $es_get('purchase') ?: 'all',
    'price_label' => $es_get('price') !== '' ? esc_html('سقف: ' . es_fa_num(number_format((float) $es_get('price'))) . ' تومان') : 'همه قیمت‌ها',
    'price_value' => $es_get('price') ?: '15000000',
    'ip_options' => $es_opt('pa_ip-rating', 'ip'), 'volt_options' => $es_opt('pa_input-voltage', 'voltage'),
    'cat_tree' => $es_cat_tree,
]);
echo (string) ob_get_clean();

echo '<div class="lg:col-span-8 xl:col-span-9">';
$es_topbar_label = $es_label;
ob_start();
es_get_template('template-parts/archive/topbar.php', ['total' => $es_total, 'label' => $es_topbar_label, 'sort' => $es_get('sort')]);
echo (string) ob_get_clean();

echo '<div data-product-grid="true" class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">';
if (!$es_is_empty) {
    $es_i = 0;
    foreach (($es_q->posts ?? []) as $es_post) {
        ob_start();
        es_get_template('template-parts/cards/card-product.php', ['post' => $es_post, 'custom' => (string) round(($es_i % 3) * 0.06, 2)]);
        echo (string) ob_get_clean();
        $es_i++;
    }
}
echo '</div>';

if (!$es_is_empty && $es_pages > 1) {
    ob_start();
    es_get_template('template-parts/archive/pagination.php', [
        'current' => $es_paged, 'total_pages' => $es_pages, 'total_products' => $es_total,
        'base_url' => (string) get_term_link($es_term),
    ]);
    echo (string) ob_get_clean();
}

es_get_template('template-parts/archive/product-empty.php', ['show' => $es_is_empty]);

// FAQ دسته (متای ترم) — مخفی اگر خالی
ob_start();
echo '<div class="mt-16 lg:mt-20">';
es_get_template('template-parts/archive/category-faq.php', [
    'label' => $es_label,
    'rows'  => es_get_category_faq($es_tid),
]);
echo (string) ob_get_clean();
echo '</div>';
echo '</section>';
echo '</main>';
get_footer();
