<?php
/**
 * شل آرشیو نوشته‌ها (وبلاگ) — برش بایت‌دقیق میرور blog.html + category/magazine.html
 * فرگمنت‌ها با temp/gen_p74.py تولید شده‌اند (assert بازسازی دو حالت home/category).
 * mode=home → hero برگه وبلاگ؛ mode=category → hero ترم. هوم فقط برای صفحه نوشته‌ها (page_for_posts).
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {mode: string, term?: object|null} $args */

$ES74_HERO = <<<'ESP74ES74_HERO'
<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36"><div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-50"></div><div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/45 blur-[110px]"></div><div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/65 blur-[110px]"></div><div class="absolute left-[8%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div><div class="absolute right-[12%] top-0 h-full w-px bg-gradient-to-b from-transparent via-teal-300/20 to-transparent"></div></div><div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8"><nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="{ES_HOME_URL}" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a>{ES_CRUMB3_SPAN}</nav><div class="grid items-center gap-12 pb-16 pt-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-14 lg:pb-20 lg:pt-12"><div><div class="mb-6 inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white py-1.5 pl-5 pr-1.5 shadow-sm"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-700 text-white shadow-[0_0_20px_rgba(0,125,45,0.35)]"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-newspaper h-4 w-4" aria-hidden="true"><path d="M15 18h-5"></path><path d="M18 14h-8"></path><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"></path><rect width="8" height="4" x="10" y="6" rx="1"></rect></svg></span><span class="text-[13px] font-extrabold text-emerald-800">{ES_BADGE}</span></div><h1 class="text-[2.5rem] font-black leading-[1.25] tracking-[-0.015em] text-slate-950 sm:text-[3.3rem] lg:text-[3.8rem] lg:leading-[1.18]">{ES_TITLE_A}<br/><span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent">{ES_TITLE_B}</span></h1><p class="mt-6 max-w-[34rem] text-[15.5px] font-medium leading-9 text-slate-600">{ES_DESC}</p><div class="relative mt-8 max-w-[30rem]"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute right-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="text" data-blog-search="true" placeholder="جستجو در مقالات… (مثلاً تونل نوری، والواشر)" class="w-full rounded-2xl border border-emerald-200 bg-white py-4 pl-5 pr-13 text-[14px] font-medium text-slate-800 shadow-[0_18px_40px_-22px_rgba(0,80,40,0.18)] outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15" value="{ES_SEARCH_VALUE}"/></div><div class="mt-5 flex flex-wrap gap-2">{ES_BTN_ROWS}</div></div>{ES_FEATURED}</div></div><div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div></section>
ESP74ES74_HERO;

$ES74_FEATURED = <<<'ESP74ES74_FEATURED'
<div class="relative mx-auto w-full max-w-[30rem]"><div aria-hidden="true" class="absolute left-1/2 top-1/2 h-[70%] w-[70%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-200/50 blur-[80px]"></div><a href="{ES_F_URL}" class="group relative block overflow-hidden rounded-[2.5rem] border border-emerald-100 bg-white p-2 shadow-[0_36px_80px_-30px_rgba(0,80,40,0.28)]"><div class="relative overflow-hidden rounded-[2rem]"><img src="{ES_F_IMG}" alt="{ES_F_TITLE}" class="h-[24rem] w-full object-cover transition-transform duration-[1.4s] group-hover:scale-105 sm:h-[27rem]"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div><span class="absolute right-5 top-5 rounded-full bg-emerald-700 px-4 py-1.5 text-[11.5px] font-black text-white shadow-lg">★ منتخب سردبیر</span><div class="absolute inset-x-0 bottom-0 p-6"><div class="mb-2.5 flex items-center gap-3 text-[11.5px] font-bold text-emerald-200"><span>{ES_F_CHIP}</span><span class="h-1 w-1 rounded-full bg-white/50"></span><span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock3 lucide-clock-3 h-3 w-3" aria-hidden="true"><path d="M12 6v6h4"></path><circle cx="12" cy="12" r="10"></circle></svg>{ES_F_MIN} دقیقه مطالعه</span></div><h3 class="line-clamp-2 text-[19px] font-black leading-8 text-white [text-shadow:0_2px_14px_rgba(0,0,0,0.6)]">{ES_F_TITLE}</h3><span class="mt-3 inline-flex items-center gap-2 rounded-full bg-white/95 px-4 py-2 text-[12.5px] font-extrabold text-emerald-900 shadow-sm transition-colors group-hover:bg-emerald-600 group-hover:text-white">مطالعه مقاله<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span></div></div></a></div>
ESP74ES74_FEATURED;

$ES74_SEC = <<<'ESP74ES74_SEC'
<section id="articles" class="bg-white py-14 lg:py-16" data-blog-archive="true" data-items-per-page="4"><div class="mx-auto max-w-7xl scroll-mt-24 px-5 lg:px-8"><div custom="0"><div class="mb-8 flex flex-wrap items-end justify-between gap-4"><div><h2 data-blog-heading="true" class="text-2xl font-extrabold text-slate-900">{ES_HEADING}</h2><p data-blog-summary="true" class="mt-2 text-[13.5px] font-medium text-slate-500"><span data-blog-count="true">{ES_COUNT}</span>{ES_SUM_SUFFIX}</p></div><div class="flex flex-wrap items-center gap-2"><span class="flex items-center gap-1.5 text-[12px] font-extrabold text-slate-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layers h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"></path><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"></path><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"></path></svg><span>آرشیو دسته‌بندی‌ها:</span></span>{ES_LINK_ROWS}<button type="button" data-blog-reset="true" class="{ES_RESET_CLS} rounded-xl border border-slate-200 px-4 py-2 text-[12px] font-bold text-slate-600 transition-colors hover:border-emerald-400 hover:text-emerald-700 cursor-pointer">حذف فیلترها</button></div></div></div>
ESP74ES74_SEC;

$ES74_GRID = <<<'ESP74ES74_GRID'
<div data-blog-grid="true" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2">
ESP74ES74_GRID;

$ES74_BTN_ON = <<<'ESP74ES74_BTN_ON'
<button type="button" data-blog-cat-btn="{NAME}" class="rounded-xl px-4 py-2.5 text-[12.5px] font-bold transition-all cursor-pointer bg-emerald-700 text-white shadow-md shadow-emerald-700/25">{NAME}</button>
ESP74ES74_BTN_ON;

$ES74_BTN_OFF = <<<'ESP74ES74_BTN_OFF'
<button type="button" data-blog-cat-btn="{NAME}" class="rounded-xl px-4 py-2.5 text-[12.5px] font-bold transition-all cursor-pointer border border-slate-200 bg-white text-slate-600 hover:border-emerald-400 hover:text-emerald-700">{NAME}</button>
ESP74ES74_BTN_OFF;

$ES74_LINK_ON = <<<'ESP74ES74_LINK_ON'
<a href="{URL}" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors bg-emerald-700 text-white">{LABEL}</a>
ESP74ES74_LINK_ON;

$ES74_LINK_OFF = <<<'ESP74ES74_LINK_OFF'
<a href="{URL}" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300">{LABEL}</a>
ESP74ES74_LINK_OFF;

$ES74_MAIN_OPEN = <<<'ESP74ES74_MAIN_OPEN'
<main class="min-h-screen bg-[#f8faf9]">
ESP74ES74_MAIN_OPEN;

$ES74_CRUMB_LINK = <<<'ESP74ES74_CRUMB_LINK'
<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="{ES_BLOG_URL}" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">وبلاگ</a></span>
ESP74ES74_CRUMB_LINK;

$ES74_EMPTY = <<<'ESP74ES74_EMPTY'
<div data-blog-empty="true" class="{ES_EMPTY_CLS} rounded-2xl border border-dashed border-slate-300 bg-slate-50/60 py-16 text-center"><p class="text-[14px] font-bold text-slate-500">مقاله‌ای با این جستجو یافت نشد.</p></div>
ESP74ES74_EMPTY;

$ES74_CLOSE = <<<'ESP74ES74_CLOSE'
</div></section></main>
ESP74ES74_CLOSE;


// ═══ داده‌ها ═══
$es_mode = (string) ($args['mode'] ?? 'home');
$es_term = $args['term'] ?? null;
$es_is_cat = $es_mode === 'category' && is_object($es_term) && isset($es_term->term_id);
$es_name = $es_is_cat ? (string) $es_term->name : '';
$es_tid = $es_is_cat ? (int) $es_term->term_id : 0;
$es_tmeta = static fn (string $k, string $def): string => (string) (get_term_meta($es_tid, $k, true) ?: $def);

$es_page_id = (int) get_option('page_for_posts'); // home فقط برای صفحه نوشته‌ها
$es_pmeta = static fn (string $k, string $def): string => (string) (get_post_meta($es_page_id, $k, true) ?: $def);

$es_total_all = (int) (wp_count_posts('post')->publish ?? 0);
$es_per = max(1, (int) es_option('archives.posts_per_page', '4'));
$es_posts = (array) ($GLOBALS['wp_query']->posts ?? []);
$es_total = (int) ($GLOBALS['__es_archive_total'] ?? count($es_posts));
$es_paged = max(1, (int) get_query_var('paged'));
$es_pages = max(1, (int) ceil($es_total / $es_per));
$es_sticky = array_map('intval', (array) get_option('sticky_posts', []));
$es_terms = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);
if (!is_array($es_terms)) {
    $es_terms = [];
}
$es_blog_url = es_page_url('blog');
$es_base_url = $es_is_cat ? (string) get_term_link($es_term) : $es_blog_url;
$es_is_empty = $es_total < 1;

// متغیرها برای فاز ۸ (هوک‌های توسعه)
$es_blog_vars = (array) apply_filters('es_blog_archive_vars', [
    'mode' => $es_mode, 'term' => $es_is_cat ? $es_term : null, 'total' => $es_total, 'per_page' => $es_per,
]);

// کارت ویژه هیرو: نخستین نوشته sticky، وگرنه نخستین مقاله
$es_fpost = null;
foreach ($es_posts as $es_p) {
    if (in_array((int) ($es_p->ID ?? 0), $es_sticky, true)) {
        $es_fpost = $es_p;
        break;
    }
}
if ($es_fpost === null) {
    $es_fpost = $es_posts[0] ?? null;
}
$es_featured_html = '';
if (is_object($es_fpost)) {
    $es_fid = (int) $es_fpost->ID;
    $es_ftitle = (string) ($es_fpost->post_title ?? '');
    $es_fterms = get_the_terms($es_fid, 'category');
    $es_fchip = (is_array($es_fterms) && isset($es_fterms[0]->name)) ? (string) $es_fterms[0]->name : '';
    $es_fimg_id = (int) get_post_meta($es_fid, '_thumbnail_id', true);
    $es_fimg = es_media_url($es_fimg_id > 0 ? $es_fimg_id : 'assets/images/light-tunnel.jpg');
    $es_fmin = (int) get_post_meta($es_fid, 'es_read_time', true);
    if ($es_fmin < 1) {
        $es_fwords = count(preg_split('/\s+/u', trim(strip_tags((string) ($es_fpost->post_content ?? '')))) ?: []);
        $es_fmin = max(1, (int) ceil($es_fwords / 225));
    }
    $es_featured_html = strtr($ES74_FEATURED, [
        '{ES_F_URL}'   => esc_url((string) get_permalink($es_fid)),
        '{ES_F_IMG}'   => esc_url($es_fimg),
        '{ES_F_TITLE}' => esc_attr($es_ftitle),
        '{ES_F_CHIP}'  => esc_html($es_fchip),
        '{ES_F_MIN}'   => es_fa_num((string) $es_fmin),
    ]);
}

// دکمه‌های دسته هیرو (JS) + لینک‌های دسته سکشن
$es_btn_rows = '';
$es_link_rows = '';
$es_btn_on = $es_is_cat ? $es_name : 'همه';
$es_btn_rows .= strtr($es_btn_on === 'همه' ? $ES74_BTN_ON : $ES74_BTN_OFF, ['{NAME}' => esc_html('همه')]);
if (!$es_is_cat) {
    $es_link_rows .= strtr($ES74_LINK_ON, ['{URL}' => esc_url($es_blog_url), '{LABEL}' => esc_html('همه مطالب')]);
} else {
    $es_link_rows .= strtr($ES74_LINK_OFF, ['{URL}' => esc_url($es_blog_url), '{LABEL}' => esc_html('همه مطالب')]);
}
foreach ($es_terms as $es_t) {
    $es_tname = (string) $es_t->name;
    $es_active = $es_is_cat && $es_tname === $es_name;
    $es_btn_rows .= strtr($es_active ? $ES74_BTN_ON : $ES74_BTN_OFF, ['{NAME}' => esc_html($es_tname)]);
    $es_tlink = (string) get_term_link($es_t);
    $es_link_rows .= strtr($es_active ? $ES74_LINK_ON : $ES74_LINK_OFF, ['{URL}' => esc_url($es_tlink), '{LABEL}' => esc_html($es_tname)]);
}

// متن‌های هیرو و سکشن — متا با fallback میرور
if ($es_is_cat) {
    $es_crumb_extra = strtr($ES74_CRUMB_LINK, ['{ES_BLOG_URL}' => esc_url($es_blog_url)]);
    $es_crumb3 = esc_html($es_tmeta('es_blog_crumb', $es_name));
    $es_badge = esc_html($es_tmeta('es_blog_badge', 'آرشیو دسته‌بندی: ' . $es_name . ' · ' . es_fa_num((string) $es_total_all) . ' مقاله'));
    $es_title_a = esc_html($es_tmeta('es_blog_title_a', $es_name));
    $es_title_b = esc_html($es_tmeta('es_blog_title_b', 'نورپردازی شهری'));
    $es_desc_def = (string) ($es_term->description ?? '');
    $es_desc = esc_html(trim($es_desc_def) !== '' ? $es_desc_def : $es_tmeta('es_blog_desc', 'مقالات تخصصی، راهنماهای خرید، اخبار و رویدادها و دانش فنی صنعت روشنایی شهری و صنعتی؛ نوشته‌شده توسط کارشناسان فنی شرکت دانش‌بنیان عرفان صنعت اصفهان'));
    $es_heading = esc_html($es_tmeta('es_blog_heading', 'آرشیو مطالب «' . $es_name . '»'));
    $es_sum_suffix = ' مقاله در دسته «' . esc_html($es_name) . '»';
    $es_reset_cls = 'inline-flex';
    $es_search_value = (string) get_query_var('s');
} else {
    $es_crumb_extra = '';
    $es_crumb3 = esc_html($es_pmeta('es_blog_crumb', 'وبلاگ'));
    $es_badge = esc_html($es_pmeta('es_blog_badge', 'مجله فنی و آموزشی عرفان صنعت · ' . es_fa_num((string) $es_total_all) . ' مقاله'));
    $es_title_a = esc_html($es_pmeta('es_blog_title_a', 'اخبار و مقالات'));
    $es_title_b = esc_html($es_pmeta('es_blog_title_b', 'نورپردازی شهری'));
    $es_desc = esc_html($es_pmeta('es_blog_desc', 'مقالات تخصصی، راهنماهای خرید، اخبار و رویدادها و دانش فنی صنعت روشنایی شهری و صنعتی؛ نوشته‌شده توسط کارشناسان فنی شرکت دانش‌بنیان عرفان صنعت اصفهان'));
    $es_heading = esc_html($es_pmeta('es_blog_heading', 'آخرین مقالات و اخبار'));
    $es_sum_suffix = ' مقاله';
    $es_reset_cls = 'hidden';
    $es_search_value = isset($_GET['s']) && is_string($_GET['s']) ? sanitize_text_field((string) wp_unslash((string) $_GET['s'])) : '';
}

// ═══ رندر ═══
echo $ES74_MAIN_OPEN;
echo strtr($ES74_HERO, [
    '{ES_HOME_URL}'      => esc_url(home_url('/')),
    '{ES_CRUMB3_SPAN}'   => $es_crumb_extra . '<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">' . $es_crumb3 . '</span></span>',
    '{ES_BADGE}'         => $es_badge,
    '{ES_TITLE_A}'       => $es_title_a,
    '{ES_TITLE_B}'       => $es_title_b,
    '{ES_DESC}'          => $es_desc,
    '{ES_SEARCH_VALUE}'  => esc_attr($es_search_value),
    '{ES_BTN_ROWS}'      => $es_btn_rows,
    '{ES_FEATURED}'      => $es_featured_html,
]);
echo strtr($ES74_SEC, [
    '{ES_HEADING}'     => $es_heading,
    '{ES_COUNT}'       => esc_html(es_fa_num((string) $es_total)),
    '{ES_SUM_SUFFIX}'  => $es_sum_suffix,
    '{ES_LINK_ROWS}'   => $es_link_rows,
    '{ES_RESET_CLS}'   => $es_reset_cls,
]);
echo $ES74_GRID;
if (!$es_is_empty) {
    $es_i = 0;
    foreach ($es_posts as $es_post) {
        ob_start();
        es_get_template('template-parts/cards/card-post.php', [
            'post'        => $es_post,
            'nested'      => true,
            'visible'     => $es_i < $es_per,
            'featured'    => in_array((int) ($es_post->ID ?? 0), $es_sticky, true),
            'search_chip' => false,
            'custom'      => ['0.05', '0.12000000000000001', '0.19'][$es_i % 3],
        ]);
        echo (string) ob_get_clean();
        $es_i++;
    }
}
echo '</div>';
ob_start();
es_get_template('template-parts/archive/pagination.php', [
    'current'     => $es_paged,
    'total_pages' => $es_pages,
    'base_url'    => $es_base_url,
    'root'        => 'blog',
    'label'       => 'صفحه‌بندی مقالات',
    'nav_cls'     => $es_pages > 1 ? 'flex' : 'hidden',
    'cur_label'   => 'صفحه',
    'count_seg'   => '',
    'box_cls'     => 'bg-slate-50/70 px-5 py-4',
    'navbtn_cls'  => 'bg-white px-3 py-2',
    'page_btn'    => true,
    'next_href'   => false,
]);
echo (string) ob_get_clean();
echo strtr($ES74_EMPTY, ['{ES_EMPTY_CLS}' => $es_is_empty ? 'block' : 'hidden']);
echo $ES74_CLOSE;

