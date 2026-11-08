<?php
/**
 * شل نتایج جستجو — ساخت از کامپوننت‌های بایت‌دقیق میرور (blog.html + گرید ۶.۷ + ناو مشترک)
 * فرگمنت‌ها با temp/gen_p76.py استخراج/assert شده‌اند. تب‌ها با GET type (any/product/projects/post).
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES76_SEARCH_HERO = <<<'ESP76ES76_SEARCH_HERO'
<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36"><div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-50"></div><div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/45 blur-[110px]"></div><div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/65 blur-[110px]"></div><div class="absolute left-[8%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div><div class="absolute right-[12%] top-0 h-full w-px bg-gradient-to-b from-transparent via-teal-300/20 to-transparent"></div></div><div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8"><nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="{ES_HOME_URL}" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a>{ES_CRUMB}</nav></div><div class="relative z-10 mx-auto max-w-3xl px-5 pb-16 pt-10 text-center lg:pb-20 lg:pt-12"><div class="mb-6 inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white py-1.5 pl-5 pr-1.5 shadow-sm"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-700 text-white shadow-[0_0_20px_rgba(0,125,45,0.35)]"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg></span><span class="text-[13px] font-extrabold text-emerald-800">{ES_BADGE}</span></div><h1 class="mx-auto mt-6 max-w-4xl text-center text-[1.9rem] font-black leading-[1.5] tracking-[-0.012em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.4]">{ES_TITLE}</h1><p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600">{ES_DESC}</p><div class="mt-8">{ES_FORM}</div><div class="mt-6 flex flex-wrap items-center justify-center gap-2">{ES_TABS}</div></div><div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div></section>
ESP76ES76_SEARCH_HERO;

$ES76_SEC_HEAD = <<<'ESP76ES76_SEC_HEAD'
<section id="results" class="bg-white py-14 lg:py-16" data-search-archive="true"><div class="mx-auto max-w-7xl scroll-mt-24 px-5 lg:px-8"><div class="mb-8 flex flex-wrap items-end justify-between gap-4"><div><h2 data-search-heading="true" class="text-2xl font-extrabold text-slate-900">{ES_HEADING}</h2><p data-search-summary="true" class="mt-2 text-[13.5px] font-medium text-slate-500"><span data-search-count="true">{ES_COUNT}</span> {ES_UNIT}{ES_SUM_SUFFIX}</p></div><div class="flex flex-wrap items-center gap-2">{ES_TABS_LINKS}</div></div><div data-search-grid="true" class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
ESP76ES76_SEC_HEAD;

$ES76_LINK_ON = <<<'ESP76ES76_LINK_ON'
<a href="{URL}" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors bg-emerald-700 text-white">{LABEL}</a>
ESP76ES76_LINK_ON;

$ES76_LINK_OFF = <<<'ESP76ES76_LINK_OFF'
<a href="{URL}" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300">{LABEL}</a>
ESP76ES76_LINK_OFF;

$ES76_404 = <<<'ESP76ES76_404'
<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36"><div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-50"></div><div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/45 blur-[110px]"></div><div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/65 blur-[110px]"></div><div class="absolute left-[8%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div><div class="absolute right-[12%] top-0 h-full w-px bg-gradient-to-b from-transparent via-teal-300/20 to-transparent"></div></div><div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8"><nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="{ES_HOME_URL}" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">۴۰۴</span></span></nav></div><div class="relative z-10 mx-auto max-w-3xl px-5 pb-16 pt-10 text-center lg:pb-20 lg:pt-12"><div class="text-[5.5rem] font-black leading-none tracking-tight text-emerald-800/15 sm:text-[7.5rem]">۴۰۴</div><h1 class="mx-auto mt-6 max-w-4xl text-center text-[1.9rem] font-black leading-[1.5] tracking-[-0.012em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.4] mt-2">این صفحه در دسترس نیست</h1><p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600">آدرس واردشده وجود ندارد یا جابه‌جا شده است. از جستجو استفاده کنید یا از دسترسی سریع زیر کمک بگیرید.</p><div class="mt-8">{ES_FORM}</div><div class="mt-6 flex flex-wrap items-center justify-center gap-2">{ES_QUICK}</div></div><div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div></section>
ESP76ES76_404;

$ES76_GRID_CLS = <<<'ESP76ES76_GRID_CLS'
grid gap-6 sm:grid-cols-2 xl:grid-cols-3
ESP76ES76_GRID_CLS;

$ES76_EMPTY_CLS = <<<'ESP76ES76_EMPTY_CLS'
rounded-2xl border border-dashed border-slate-300 bg-slate-50/60 py-16 text-center
ESP76ES76_EMPTY_CLS;

$ES76_EMPTY_P_CLS = <<<'ESP76ES76_EMPTY_P_CLS'
text-[14px] font-bold text-slate-500
ESP76ES76_EMPTY_P_CLS;

$ES76_CRUMB_PILL = <<<'ESP76ES76_CRUMB_PILL'
<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">{X}</span></span>
ESP76ES76_CRUMB_PILL;


// ═══ داده‌ها ═══
$es_s = (string) get_query_var('s');
$es_type = isset($_GET['type']) && is_string($_GET['type']) ? sanitize_key((string) wp_unslash((string) $_GET['type'])) : '';
$es_types_map = ['product' => 'محصولات', 'projects' => 'پروژه‌ها', 'post' => 'مقالات'];
$es_type = isset($es_types_map[$es_type]) ? $es_type : '';
$es_posts = (array) ($GLOBALS['wp_query']->posts ?? []);
$es_total = (int) ($GLOBALS['__es_archive_total'] ?? count($es_posts));
$es_paged = max(1, (int) get_query_var('paged'));
$es_per = (int) apply_filters('es_search_per_page', 9);
$es_per = max(1, $es_per);
$es_pages = max(1, (int) ceil($es_total / $es_per));
$es_home = home_url('/');
$es_base = $es_home . '?s=' . rawurlencode($es_s) . ($es_type !== '' ? '&type=' . $es_type : '');

/** متغیرها برای فاز ۸ (هوک‌های توسعه) */
$es_search_vars = (array) apply_filters('es_search_vars', [
    's' => $es_s, 'type' => $es_type, 'total' => $es_total, 'per_page' => $es_per,
]);

// قرص تب‌های هیرو و لینک‌های سکشن
$es_tab_url = static function (string $t) use ($es_s): string {
    return home_url('/') . '?s=' . rawurlencode($es_s) . ($t === '' ? '' : '&type=' . $t);
};
$es_tabs = '';
$es_tabs_links = '';
foreach (['' => 'همه'] + $es_types_map as $es_t => $es_label) {
    $es_active = ($es_t === $es_type) || ($es_t === '' && $es_type === '');
    $es_url = $es_tab_url($es_t);
    $es_tabs .= strtr($es_active ? $ES76_LINK_ON : $ES76_LINK_OFF, ['{URL}' => esc_url($es_url), '{LABEL}' => esc_html($es_label)]);
    $es_tabs_links .= strtr($es_active ? $ES76_LINK_ON : $ES76_LINK_OFF, ['{URL}' => esc_url($es_url), '{LABEL}' => esc_html($es_label)]);
}

// ═══ رندر ═══
echo strtr($ES76_SEARCH_HERO, [
    '{ES_HOME_URL}' => esc_url($es_home),
    '{ES_CRUMB}'    => strtr($ES76_CRUMB_PILL, ['{X}' => esc_html('جستجو')]),
    '{ES_BADGE}'    => esc_html($es_s === '' ? 'جستجو در سایت عرفان صنعت' : 'نتیجه‌های جستجو'),
    '{ES_TITLE}'    => esc_html($es_s === '' ? 'جستجو' : 'نتایج جستجو برای: ' . $es_s),
    '{ES_DESC}'     => esc_html($es_s === '' ? 'عبارت موردنظر خود را وارد کنید تا محصولات، پروژه‌ها و مقالات مرتبط را ببینید.' : 'در محصولات، پروژه‌های اجراشده و مقالات مجله فنی عرفان صنعت.'),
    '{ES_FORM}'     => get_search_form(),
    '{ES_TABS}'     => $es_s === '' ? '' : $es_tabs,
]);
echo strtr($ES76_SEC_HEAD, [
    '{ES_HEADING}'    => esc_html($es_type === '' ? 'همه نتایج' : 'نتایج ' . $es_types_map[$es_type]),
    '{ES_COUNT}'      => esc_html(es_fa_num((string) $es_total)),
    '{ES_UNIT}'       => esc_html('نتیجه'),
    '{ES_SUM_SUFFIX}' => $es_s !== '' ? ' برای «' . esc_html($es_s) . '»' : '',
    '{ES_TABS_LINKS}' => $es_s === '' ? '' : $es_tabs_links,
]);
if ($es_posts !== []) {
    $es_customs_product = ['0', '0.06', '0.12'];
    $es_customs_project = ['0.04', '0.1'];
    $es_customs_post = ['0.05', '0.12000000000000001', '0.19'];
    $es_i = 0;
    foreach ($es_posts as $es_post) {
        ob_start();
        switch ((string) ($es_post->post_type ?? 'post')) {
            case 'product':
                es_get_template('template-parts/cards/card-product.php', ['post' => $es_post, 'custom' => $es_customs_product[$es_i % 3]]);
                break;
            case 'projects':
                es_get_template('template-parts/cards/card-project.php', ['post' => $es_post, 'custom' => $es_customs_project[$es_i % 2]]);
                break;
            default:
                es_get_template('template-parts/cards/card-post.php', ['post' => $es_post, 'custom' => $es_customs_post[$es_i % 3]]);
        }
        echo (string) ob_get_clean();
        $es_i++;
    }
}
echo '</div>';
$es_empty_cls = $es_posts === [] ? '' : 'hidden ';
echo '<div data-search-empty="true" class="' . $es_empty_cls . $ES76_EMPTY_CLS . '"><p class="' . $ES76_EMPTY_P_CLS . '">';
echo esc_html($es_posts === [] && $es_s === '' ? 'عبارتی برای جستجو وارد نشده است.' : 'نتیجه‌ای برای این جستجو یافت نشد.');
echo '</p></div>';
ob_start();
es_get_template('template-parts/archive/pagination.php', [
    'current'        => $es_paged,
    'total_pages'    => $es_pages,
    'total_products' => $es_total,
    'base_url'       => $es_base,
    'root'           => 'search',
    'count_attr'     => 'data-search-count="true"',
    'unit'           => 'نتیجه',
    'label'          => 'صفحه‌بندی نتایج',
    'always_show'    => false,
    'nav_cls'        => $es_pages > 1 ? 'flex' : 'hidden',
]);
echo (string) ob_get_clean();
echo '</div></section>';

