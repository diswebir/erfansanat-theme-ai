<?php
/**
 * نوار بالای گرید آرشیو (شمارنده + مرتب‌سازی) — برش بایت‌دقیق میرور shop.html
 * فرگمنت با temp/gen_p68.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_TOPBAR = <<<'ESP68T'
<div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3.5 shadow-2xs sm:px-5"><div class="flex flex-wrap items-center gap-2"><span class="text-[13.5px] font-black text-slate-900">{ES_LABEL}</span><span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-extrabold text-emerald-800"><span data-product-count="true">{ES_COUNT}</span> محصول</span></div><div class="flex flex-wrap items-center gap-2"><div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-down h-3.5 w-3.5 text-emerald-700" aria-hidden="true"><path d="m21 16-4 4-4-4"></path><path d="M17 20V4"></path><path d="m3 8 4-4 4 4"></path><path d="M7 4v16"></path></svg><label for="product-sort-select" class="text-[11.5px] font-bold text-slate-500">مرتب‌سازی:</label><select id="product-sort-select" data-product-sort="true" class="bg-transparent text-[12px] font-extrabold text-slate-800 focus:outline-none cursor-pointer"><option value="default" selected="">پیش‌فرض کارخانه</option>{ES_SORT_OPTIONS}</select></div><button type="button" data-archive-drawer-open="true" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-3.5 py-2 text-[12px] font-extrabold text-white shadow-xs transition-colors hover:bg-emerald-800 lg:hidden cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sliders-horizontal h-3.5 w-3.5" aria-hidden="true"><path d="M10 5H3"></path><path d="M12 19H3"></path><path d="M14 3v4"></path><path d="M16 17v4"></path><path d="M21 12h-9"></path><path d="M21 19h-5"></path><path d="M21 5h-7"></path><path d="M8 10v4"></path><path d="M8 12H3"></path></svg><span>فیلتر و دسته‌بندی</span></button></div></div>
ESP68T;
$es_args = $args ?? [];
$es_sort = (string) ($es_args['sort'] ?? '');
$es_opts = '<option value="popular">پربازدیدترین</option><option value="rating">بالاترین امتیاز</option><option value="price-asc">ارزان‌ترین</option><option value="price-desc">گران‌ترین</option>';
if ($es_sort !== '' && str_contains($es_opts, 'value="' . $es_sort . '"')) {
    $es_opts = str_replace('value="' . $es_sort . '"', 'value="' . $es_sort . '" selected=""', $es_opts);
}
echo strtr($ES_TOPBAR, [
    '{ES_COUNT}'        => es_fa_num((string) ($es_args['total'] ?? 0)),
    '{ES_LABEL}'        => esc_html((string) ($es_args['label'] ?? 'همه محصولات تولیدی عرفان صنعت')),
    '{ES_SORT_OPTIONS}' => $es_opts,
]);
