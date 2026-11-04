<?php
/**
 * صفحه‌بندی آرشیو فروشگاه — برش بایت‌دقیق میرور shop.html (nav) با دکمه‌های میرور
 * فرگمنت با temp/gen_p67.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_PAGI = <<<'ESP67B'
<nav aria-label="صفحه‌بندی محصولات" data-pagination-root="products" class="flex mt-10 flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/90 bg-white px-5 py-4 shadow-2xs"><div class="text-[12.5px] font-bold text-slate-600">نمایش صفحه <span data-pagination-current="true" class="font-black text-emerald-800">{ES_CUR}</span> از <span data-pagination-total="true" class="font-black text-slate-900">{ES_TOTAL_PAGES}</span> (مجموعاً <span data-product-count="true">{ES_TOTAL_PRODUCTS}</span> محصول)</div><div class="flex items-center gap-1.5" data-pagination-buttons="true"><button type="button" data-page-prev="true"{ES_PREV_ATTR} class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 disabled:opacity-40 cursor-pointer"{ES_PREV_URL}><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-4 w-4" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg><span>قبلی</span></button>{ES_PAGES_HTML}<button type="button" data-page-next="true"{ES_NEXT_ATTR} class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 disabled:opacity-40 cursor-pointer"{ES_NEXT_URL}><span>بعدی</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-4 w-4" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg></button></div></nav>
ESP67B;
$es_args = $args ?? [];
$es_current = max(1, (int) ($es_args['current'] ?? 1));
$es_total_pages = max(1, (int) ($es_args['total_pages'] ?? 1));
$es_base = (string) ($es_args['base_url'] ?? '');
$es_url = static function (int $n) use ($es_base): string {
    return $n <= 1 ? $es_base : $es_base . (str_contains($es_base, '?') ? '&' : '?') . 'paged=' . $n;
};

if ($es_total_pages <= 1) {
    return; // تک‌صفحه‌ای → بدون نوار
}
$es_pages_html = '';
foreach (range(1, $es_total_pages) as $es_n) {
    if ($es_n === $es_current) {
        $es_pages_html .= '<button type="button" data-page-num="' . $es_n . '" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer bg-emerald-700 text-white shadow-sm shadow-emerald-700/25">' . es_fa_num((string) $es_n) . '</button>';
    } else {
        $es_pages_html .= '<a href="' . esc_url($es_url($es_n)) . '" data-page-num="' . $es_n . '" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer border border-slate-200 bg-white text-slate-700 hover:border-emerald-300 hover:text-emerald-800">' . es_fa_num((string) $es_n) . '</a>';
    }
}
echo strtr($ES_PAGI, [
    '{ES_CUR}'            => es_fa_num((string) $es_current),
    '{ES_TOTAL_PAGES}'    => es_fa_num((string) $es_total_pages),
    '{ES_TOTAL_PRODUCTS}' => es_fa_num((string) ($es_args['total_products'] ?? 0)),
    '{ES_PREV_ATTR}'      => $es_current <= 1 ? ' disabled=""' : '',
    '{ES_PREV_URL}'       => $es_current > 1 ? ' data-href="' . esc_url($es_url($es_current - 1)) . '"' : '',
    '{ES_PAGES_HTML}'     => $es_pages_html,
    '{ES_NEXT_ATTR}'      => $es_current >= $es_total_pages ? ' disabled=""' : '',
    '{ES_NEXT_URL}'       => $es_current < $es_total_pages ? ' data-href="' . esc_url($es_url($es_current + 1)) . '"' : '',
]);
