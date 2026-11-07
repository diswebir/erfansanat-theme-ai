<?php
/**
 * آرشیو محصولات (shop) — برش بایت‌دقیق میرور shop.html؛ فیلترها/گرید/صفحه‌بندی داینامیک
 * فرگمنت‌ها با temp/gen_p67.py تولید شده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_HERO = <<<'ESP67H'
<section class="relative overflow-hidden bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-36"><div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 500px at 78% -6%, rgba(0,177,64,0.1), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-50"></div><div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-emerald-200/50 blur-[110px]"></div><div class="absolute -left-28 bottom-10 h-72 w-72 rounded-full bg-teal-100/60 blur-[110px]"></div><div class="absolute left-[10%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div></div><div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8"><nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="index.html" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">فروشگاه محصولات</span></span></nav><div class="grid items-center gap-12 pb-8 pt-10 lg:grid-cols-[1.02fr_0.98fr] lg:gap-14 lg:pb-12 lg:pt-12"><div><div class="mb-6 inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white py-1.5 pl-5 pr-1.5 shadow-sm"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-600 text-white shadow-md shadow-emerald-600/25"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck h-4 w-4" aria-hidden="true"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg></span><span class="text-[13px] font-extrabold text-emerald-800">فروشگاه رسمی تولیدات کارخانه عرفان صنعت</span></div><h1 class="text-[2.3rem] font-black leading-[1.28] tracking-[-0.015em] text-slate-950 sm:text-[3rem] lg:text-[3.4rem] lg:leading-[1.22]">{ES_H1_A}<br/><span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent">{ES_H1_B}</span></h1><p class="mt-6 max-w-[36rem] text-[15.5px] font-medium leading-9 text-slate-600 lg:text-[16px]">{ES_HERO_DESC}</p><div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4"><div class="flex items-center gap-2.5 rounded-2xl border border-emerald-100 bg-white px-3 py-2.5 shadow-xs"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><span class="text-[11.5px] font-extrabold leading-4 text-slate-700">استاندارد IP67 / IP68</span></div><div class="flex items-center gap-2.5 rounded-2xl border border-emerald-100 bg-white px-3 py-2.5 shadow-xs"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><span class="text-[11.5px] font-extrabold leading-4 text-slate-700">دارای کد GS1 و ستاد</span></div><div class="flex items-center gap-2.5 rounded-2xl border border-emerald-100 bg-white px-3 py-2.5 shadow-xs"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><span class="text-[11.5px] font-extrabold leading-4 text-slate-700">۲۴ ماه گارانتی تعویض</span></div><div class="flex items-center gap-2.5 rounded-2xl border border-emerald-100 bg-white px-3 py-2.5 shadow-xs"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><span class="text-[11.5px] font-extrabold leading-4 text-slate-700">۵ سال خدمات پس از فروش</span></div></div></div><div class="relative mx-auto w-full max-w-[30rem]"><div aria-hidden="true" class="absolute left-1/2 top-1/2 h-[70%] w-[70%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-200/50 blur-[80px]"></div><div class="relative overflow-hidden rounded-[2.5rem] border-4 border-white shadow-[0_40px_80px_-30px_rgba(0,80,40,0.35)]"><img src="images/light-tree.jpg" alt="" class="h-[22rem] w-full object-cover sm:h-[25rem] lg:h-[28rem]"/><div class="absolute inset-0 bg-gradient-to-t from-emerald-950/45 via-transparent to-transparent"></div><div class="absolute bottom-4 inset-x-4 flex items-center justify-between rounded-2xl border border-white/50 bg-white/85 px-4 py-3 backdrop-blur-md"><div class="flex flex-col"><span class="text-[11px] font-bold text-slate-500">پیشنهاد ویژه کارخانه</span><span class="text-[14px] font-black text-slate-900">استعلام قیمت روز + مشاوره رایگان</span></div><span class="rounded-xl bg-emerald-700 px-3.5 py-2 text-[12.5px] font-black text-white shadow-lg">تماس بگیرید</span></div></div><span class="absolute -top-4 right-8 flex items-center gap-2 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 shadow-xl"><span class="relative flex h-2.5 w-2.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-70"></span><span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-600"></span></span><span class="text-[12.5px] font-extrabold text-slate-900">موجود در انبار کارخانه</span></span><span class="absolute -bottom-5 left-6 flex items-center gap-2.5 rounded-2xl bg-emerald-800 px-4 py-3 text-white shadow-[0_20px_40px_-16px_rgba(0,80,40,0.5)]"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-5 w-5 text-amber-300" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg><span class="flex flex-col leading-tight"><span class="text-[13px] font-black">۲۴ ماه گارانتی</span><span class="text-[10.5px] font-medium text-emerald-200">۵ سال خدمات پس از فروش</span></span></span></div></div></div><div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div></section><section id="archive" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-12 lg:px-8 lg:py-16" data-products-archive="true" data-initial-category="همه" data-items-per-page="9"><div class="grid items-start gap-8 lg:grid-cols-12">
ESP67H;
$ES_TOPBAR = <<<'ESP67T'
<div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3.5 shadow-2xs sm:px-5"><div class="flex flex-wrap items-center gap-2"><span class="text-[13.5px] font-black text-slate-900">همه محصولات تولیدی عرفان صنعت</span><span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-extrabold text-emerald-800"><span data-product-count="true">{ES_COUNT}</span> محصول</span></div><div class="flex flex-wrap items-center gap-2"><div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-down h-3.5 w-3.5 text-emerald-700" aria-hidden="true"><path d="m21 16-4 4-4-4"></path><path d="M17 20V4"></path><path d="m3 8 4-4 4 4"></path><path d="M7 4v16"></path></svg><label for="product-sort-select" class="text-[11.5px] font-bold text-slate-500">مرتب‌سازی:</label><select id="product-sort-select" data-product-sort="true" class="bg-transparent text-[12px] font-extrabold text-slate-800 focus:outline-none cursor-pointer"><option value="default" selected="">پیش‌فرض کارخانه</option>{ES_SORT_EXTRA}</select></div><button type="button" data-archive-drawer-open="true" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-3.5 py-2 text-[12px] font-extrabold text-white shadow-xs transition-colors hover:bg-emerald-800 lg:hidden cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sliders-horizontal h-3.5 w-3.5" aria-hidden="true"><path d="M10 5H3"></path><path d="M12 19H3"></path><path d="M14 3v4"></path><path d="M16 17v4"></path><path d="M21 12h-9"></path><path d="M21 19h-5"></path><path d="M21 5h-7"></path><path d="M8 10v4"></path><path d="M8 12H3"></path></svg><span>فیلتر و دسته‌بندی</span></button></div></div>
ESP67T;
$ES_GRID_OPEN = <<<'ESP67G'
<div data-product-grid="true" class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
ESP67G;
$ES_EMPTY = <<<'ESP67E'
<div data-product-empty="true" class="{ES_EMPTY_CLS} rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 py-16 text-center"><p class="text-[14.5px] font-extrabold text-slate-700">محصولی با این مشخصات یافت نشد.</p><p class="mt-1.5 text-[12.5px] text-slate-500">عبارت جستجو را تغییر دهید یا فیلترهای فعال را پاک کنید.</p><a href="shop.html" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-[12.5px] font-bold text-white hover:bg-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rotate-ccw h-3.5 w-3.5" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg><span>نمایش همه محصولات</span></a></div></div></div>
ESP67E;
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

// سکشن آرشیو: archive-open میرور داخل فرگمنت هیرو است (id="archive")؛ اینجا ستون‌ها
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
echo '</section>';
echo '</main>';
get_footer();
