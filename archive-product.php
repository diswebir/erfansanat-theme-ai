<?php
/**
 * آرشیو محصولات (shop) — برش بایت‌دقیق میرور shop.html؛ فیلترها/گرید/صفحه‌بندی داینامیک
 * فرگمنت‌ها با temp/gen_p67.py تولید شده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

$es_q = $GLOBALS['wp_query'] ?? null;
$es_total = (int) ($GLOBALS['__es_archive_total'] ?? ($es_q->found_posts ?? 0));
$es_paged = max(1, (int) get_query_var('paged'));
$es_per = (int) es_option('archives.products_per_page', '20');
$es_pages = max(1, (int) ceil($es_total / max(1, $es_per)));
$es_is_empty = $es_total < 1;

$es_get = static fn(string $k): string => isset($_GET[$k]) && is_string($_GET[$k]) ? (string) $_GET[$k] : '';

// گزینه‌های selectها از termها
$es_opt = static function (string $tax, string $all_label, string $sel) use ($es_get): string {
    $out = '';
    foreach ((array) get_terms(['taxonomy' => $tax, 'hide_empty' => false]) as $t) {
        if (is_wp_error($t) || !is_object($t)) {
            continue;
        }
        $v = (string) $t->slug;
        $lbl = (string) $t->name . ($t->description !== '' ? ' (' . $t->description . ')' : '');
        $out .= '<option value="' . esc_attr($v) . '"' . ($es_get($sel) === $v ? ' selected=""' : '') . '>' . esc_html($lbl) . '</option>';
    }
    return $out;
};

// درخت دسته‌ها (قالب میرور)
$es_cat_tree = '';
$es_terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
foreach ((array) $es_terms as $t) {
    if (is_wp_error($t) || !is_object($t)) {
        continue;
    }
    $es_n = (int) ($t->count ?? 0);
    $es_cat_tree .= '<div class="space-y-1"><a href="' . esc_url(get_term_link($t)) . '" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800"><span class="flex items-center gap-2"><span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500/70"></span><span class="leading-5">' . esc_html((string) $t->name) . '</span></span><span class="rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-slate-100 text-slate-500">' . es_fa_num((string) $es_n) . '</span></a></div>';
}

echo '<main class="bg-[#f6f8f7]">';
echo strtr($ES_HERO, [
    '{ES_H1_A}'     => esc_html('فروشگاه محصولات '),
    '{ES_H1_B}'     => esc_html('نورپردازی شهری و صنعتی'),
    '{ES_HERO_DESC}' => esc_html((string) es_option('archives.shop_desc', 'مجموعه‌ای کامل از تجهیزات نورپردازی پیشرفته؛ از پوینت لایت پیکسل، ریسه‌های بلوطی، فندقی و سوزنی IP68 تا درختان نوری هوشمند، خورشیدنماها و باکس کنترل مرکزی')),
]);

// سکشن آرشیو: aside فیلترها + ستون گرید
echo '<section class="relative overflow-hidden pb-16 lg:pb-24"><div class="mx-auto max-w-7xl px-5 lg:px-8"><div class="grid items-start gap-8 lg:grid-cols-12">';

ob_start();
es_get_template('template-parts/archive/product-filters.php', [
    'total'       => $es_total,
    'search'      => $es_get('s'),
    'mode'        => $es_get('purchase') ?: 'all',
    'price_label' => $es_get('price') !== '' ? esc_html('سقف: ' . es_fa_num(number_format((float) $es_get('price'))) . ' تومان') : 'همه قیمت‌ها',
    'price_value' => $es_get('price') ?: '15000000',
    'ip_options'  => $es_opt('pa_ip-rating', 'همه استانداردها', 'ip'),
    'volt_options' => $es_opt('pa_input-voltage', 'همه ولتاژها', 'voltage'),
    'cat_tree'    => $es_cat_tree,
]);
echo (string) ob_get_clean();

echo '<div class="lg:col-span-8 xl:col-span-9">';

// نوار بالای گرید
echo strtr($ES_TOPBAR, [
    '{ES_COUNT}'       => es_fa_num((string) $es_total),
    '{ES_SORT_EXTRA}'  => $es_get('sort') === 'popular' ? '<option value="popular" selected="">پربازدیدترین</option>' : '',
]);

echo strtr($ES_GRID_OPEN, []);
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
        'base_url' => get_post_type_archive_link('product'),
    ]);
    echo (string) ob_get_clean();
}

echo strtr($ES_EMPTY, ['{ES_EMPTY_CLS}' => $es_is_empty ? 'block' : 'hidden']);
echo '</div></div></section>';
echo '</main>';
get_footer();
