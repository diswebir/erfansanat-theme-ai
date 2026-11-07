<?php
/**
 * آرشیو برچسب محصول — گرید ساده + صفحه‌بندی (نوار فیلتر ندارد)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
$es_term = get_queried_object();
$es_label = is_object($es_term) ? (string) ($es_term->name ?? '') : '';
$es_q = $GLOBALS['wp_query'] ?? null;
$es_total = (int) ($GLOBALS['__es_archive_total'] ?? ($es_q->found_posts ?? 0));
$es_paged = max(1, (int) get_query_var('paged'));
$es_per = (int) es_option('archives.products_per_page', '20');
$es_pages = max(1, (int) ceil($es_total / max(1, $es_per)));
echo '<main class="bg-[#f6f8f7]">';
echo '<section class="relative overflow-hidden pb-16 lg:pb-24"><div class="mx-auto max-w-7xl px-5 lg:px-8">';
echo '<div class="mb-7 flex flex-wrap items-end justify-between gap-4"><div><div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700"><span>برچسب محصول</span></div><h1 class="text-2xl font-extrabold text-slate-900">' . esc_html($es_label) . '</h1></div><span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-extrabold text-emerald-800">' . es_fa_num((string) $es_total) . ' محصول</span></div>';
echo '<div data-product-grid="true" class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">';
if ($es_total > 0) {
    $es_i = 0;
    foreach (($es_q->posts ?? []) as $es_post) {
        ob_start();
        es_get_template('template-parts/cards/card-product.php', ['post' => $es_post, 'custom' => (string) round(($es_i % 3) * 0.06, 2)]);
        echo (string) ob_get_clean();
        $es_i++;
    }
}
echo '</div>';
if ($es_total > 0 && $es_pages > 1) {
    ob_start();
    es_get_template('template-parts/archive/pagination.php', [
        'current' => $es_paged, 'total_pages' => $es_pages, 'total_products' => $es_total,
        'base_url' => (string) get_term_link($es_term),
    ]);
    echo (string) ob_get_clean();
}
echo '</div></section>';
echo '</main>';
get_footer();
