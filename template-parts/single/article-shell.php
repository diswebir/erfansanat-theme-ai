<?php
/**
 * شل سینگل مقاله — برش بایت‌دقیق میرور blog/led-wall-washer-guide.html + point-light-pixel-led.html
 * فرگمنت‌ها با temp/gen_p75.py تولید شده‌اند (assert بازسازی دو میرور).
 * محتوا با TOC::process سرفصل‌بندی می‌شود؛ مرتبط = ۳ مقاله جدید به‌جز جاری.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {post: object} $args */

$ES75_HERO_HEAD = <<<'ESP75ES75_HERO_HEAD'
<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36"><div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-50"></div><div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/45 blur-[110px]"></div><div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/65 blur-[110px]"></div><div class="absolute left-[8%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div><div class="absolute right-[12%] top-0 h-full w-px bg-gradient-to-b from-transparent via-teal-300/20 to-transparent"></div></div><div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8"><div class="flex justify-center"><nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="{ES_HOME_URL}" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a>{ES_CRUMB}
ESP75ES75_HERO_HEAD;

$ES75_CRUMB_PILL = <<<'ESP75ES75_CRUMB_PILL'
<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">{X}</span></span>
ESP75ES75_CRUMB_PILL;

$ES75_CRUMB_LINK = <<<'ESP75ES75_CRUMB_LINK'
<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="{URL}" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">{X}</a></span>
ESP75ES75_CRUMB_LINK;

$ES75_HERO_TAIL = <<<'ESP75ES75_HERO_TAIL'
</nav></div><div class="mt-8 flex items-center justify-center gap-3"><span class="h-px w-8 bg-emerald-400/60"></span><span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[12.5px] font-black text-emerald-800 shadow-xs">{ES_CHIP}</span><span class="h-px w-8 bg-emerald-400/60"></span></div><h1 class="mx-auto mt-6 max-w-4xl text-center text-[1.9rem] font-black leading-[1.5] tracking-[-0.012em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.4]">{ES_TITLE}</h1><p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600">{ES_DESC}</p><div class="mx-auto mt-8 flex max-w-2xl flex-wrap items-center justify-center gap-x-7 gap-y-3 rounded-3xl border border-emerald-100 bg-white px-6 py-4 shadow-[0_20px_50px_-24px_rgba(0,80,40,0.18)]"><span class="flex items-center gap-2 text-[13px] font-bold text-slate-800"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round h-4 w-4" aria-hidden="true"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg></span>{ES_AUTHOR}</span><span class="hidden h-8 w-px bg-emerald-100 sm:block"></span><span class="flex items-center gap-2 text-[13px] font-bold text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path><path d="M8 14h.01"></path><path d="M12 14h.01"></path><path d="M16 14h.01"></path><path d="M8 18h.01"></path><path d="M12 18h.01"></path><path d="M16 18h.01"></path></svg>{ES_DATE}</span><span class="hidden h-8 w-px bg-emerald-100 sm:block"></span><span class="flex items-center gap-2 text-[13px] font-bold text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock3 lucide-clock-3 h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M12 6v6h4"></path><circle cx="12" cy="12" r="10"></circle></svg>{ES_READMIN} دقیقه مطالعه</span></div><div class="mt-5 flex flex-wrap items-center justify-center gap-2">{ES_TAGS_HERO}</div></div>{ES_FEAT}<div aria-hidden="true" class="relative z-10 mt-12 h-10 rounded-t-[2.5rem] bg-[#f8faf9] sm:h-12"></div></section>
ESP75ES75_HERO_TAIL;

$ES75_FEAT_TPL = <<<'ESP75ES75_FEAT_TPL'
<div class="relative z-10 mx-auto mt-10 max-w-[1300px] px-5 lg:px-8"><div class="overflow-hidden rounded-[2rem] border border-emerald-100 bg-white p-2 shadow-[0_36px_80px_-32px_rgba(0,80,40,0.28)]"><img src="{ES_IMG}" alt="{ES_TITLE}" class="h-64 w-full rounded-[1.5rem] object-cover sm:h-96"/></div></div>
ESP75ES75_FEAT_TPL;

$ES75_TAG_PILL = <<<'ESP75ES75_TAG_PILL'
<span class="rounded-full border border-emerald-200 bg-white px-3.5 py-1.5 text-[12px] font-bold text-emerald-800 shadow-2xs">{TAG}</span>
ESP75ES75_TAG_PILL;

$ES75_TAG_BOX_PILL = <<<'ESP75ES75_TAG_BOX_PILL'
<span class="rounded-full bg-emerald-50 px-3 py-1.5 text-[12px] font-bold text-emerald-800">{TAG}</span>
ESP75ES75_TAG_BOX_PILL;

$ES75_CONTENT_OPEN = <<<'ESP75ES75_CONTENT_OPEN'
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16"><div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_300px]"><article class="min-w-0"><div custom="0"><div>
ESP75ES75_CONTENT_OPEN;

$ES75_CONTENT_CLOSE = <<<'ESP75ES75_CONTENT_CLOSE'
</div></div>
ESP75ES75_CONTENT_CLOSE;

$ES75_TAGS_SHARE = <<<'ESP75ES75_TAGS_SHARE'
<div custom="0.1"><div class="mt-12 flex flex-col gap-5 rounded-3xl border border-emerald-100 bg-white p-6 shadow-xs sm:flex-row sm:items-center sm:justify-between"><div class="flex flex-wrap items-center gap-2"><span class="text-[13px] font-bold text-slate-700">برچسب‌ها:</span>{ES_TAGS_BOX}</div><button type="button" data-share-btn="true" class="flex items-center gap-2.5 cursor-pointer text-slate-500 transition-colors hover:text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-share2 lucide-share-2 h-4.5 w-4.5 text-slate-400" aria-hidden="true"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line></svg><span data-share-label="true" class="text-[13px] font-bold">اشتراک‌گذاری مقاله</span></button></div></div></article>
ESP75ES75_TAGS_SHARE;

$ES75_ASIDE = <<<'ESP75ES75_ASIDE'
<aside class="flex flex-col gap-6"><div custom="0.08"><nav class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-xs"><h4 class="mb-4 flex items-center gap-2 text-[15px] font-extrabold text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list h-4.5 w-4.5 text-emerald-700" aria-hidden="true"><path d="M3 5h.01"></path><path d="M3 12h.01"></path><path d="M3 19h.01"></path><path d="M8 5h13"></path><path d="M8 12h13"></path><path d="M8 19h13"></path></svg>سرفصل‌های مقاله</h4><ul class="space-y-2.5 border-r border-emerald-100 pr-4">{ES_TOC_ITEMS}</ul></nav></div><div custom="0.14"><div class="relative overflow-hidden rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-teal-50/70 p-6 text-center shadow-sm"><div class="absolute -top-12 left-1/2 h-32 w-32 -translate-x-1/2 rounded-full bg-emerald-200/40 blur-[50px]"></div><h4 class="relative text-[15.5px] font-extrabold text-slate-900">مشاوره فنی رایگان پروژه</h4><p class="relative mt-2.5 text-[13px] leading-7 text-slate-600">برای انتخاب بهترین تجهیزات نورپردازی و دریافت استعلام قیمت روز با کارشناسان ما تماس بگیرید.</p><a href="tel:031-91091011" class="relative mt-5 flex items-center justify-center gap-2.5 rounded-2xl bg-emerald-700 px-5 py-3.5 text-[13.5px] font-black text-white shadow-sm transition-colors hover:bg-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4.5 w-4.5" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>۰۳۱ - ۹۱۰۹۱۰۱۱</a><a href="tel:09137976915" class="relative mt-3 flex items-center justify-center gap-2.5 rounded-2xl border border-emerald-200 bg-white px-5 py-3 text-[13px] font-bold text-emerald-800 transition-colors hover:border-emerald-400 hover:bg-emerald-50"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle h-4.5 w-4.5" aria-hidden="true"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"></path></svg>گفتگو با کارشناس فروش (۰۹۱۳۷۹۷۶۹۱۵)</a></div></div></aside></div></section>
ESP75ES75_ASIDE;

$ES75_REL_CHROME = <<<'ESP75ES75_REL_CHROME'
<section class="border-t border-emerald-100 bg-white py-14 lg:py-16"><div class="mx-auto max-w-7xl px-5 lg:px-8"><div custom="0"><div class="mb-8 flex items-center justify-between"><h3 class="text-2xl font-extrabold text-slate-900">مقالات مرتبط</h3><a href="{ES_BLOG_URL}" class="group flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-800">همه مقالات<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div></div><div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
ESP75ES75_REL_CHROME;

$ES75_CARD_RELATED = <<<'ESP75ES75_CARD_RELATED'
<div custom="{ES_CUSTOM}"><a href="{ES_URL}" class="card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]"><div class="relative h-44 overflow-hidden"><img src="{ES_IMG}" alt="{ES_TITLE}" loading="lazy" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-108"/><span class="absolute right-4 top-4 rounded-full bg-emerald-950/75 px-3 py-1 text-[11px] font-bold text-emerald-100 backdrop-blur-md">{ES_CHIP}</span></div><div class="flex flex-1 flex-col gap-2.5 p-5"><span class="text-[11.5px] font-semibold text-slate-500">{ES_DATE}</span><h4 class="text-[15.5px] font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h4><p class="line-clamp-2 text-[12.5px] leading-6 text-slate-500">{ES_EXCERPT}</p><span class="mt-auto flex items-center gap-1.5 pt-2 text-[12.5px] font-bold text-emerald-700">ادامه مطلب<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform duration-400 group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span></div></a></div>
ESP75ES75_CARD_RELATED;

$ES75_REL_TAIL = <<<'ESP75ES75_REL_TAIL'
</div></div></section>
ESP75ES75_REL_TAIL;

$ES75_TOC_ITEM = <<<'ESP75ES75_TOC_ITEM'
<li><a href="#{ES_ID}" class="block text-[13px] font-medium leading-6 text-slate-600 transition-colors hover:text-emerald-700">{ES_TITLE}</a></li>
ESP75ES75_TOC_ITEM;


// ═══ داده‌ها ═══
$es_post = $args['post'] ?? null;
if (!is_object($es_post)) {
    return;
}
$es_pid = (int) ($es_post->ID ?? 0);
$es_title = (string) ($es_post->post_title ?? '');
$es_excerpt = (string) ($es_post->post_excerpt ?? '');
$es_terms = get_the_terms($es_pid, 'category');
$es_chip = (is_array($es_terms) && isset($es_terms[0]->name)) ? (string) $es_terms[0]->name : '';
$es_img_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);
$es_img = es_media_url($es_img_id > 0 ? $es_img_id : 'assets/images/light-tunnel.jpg');
$es_date = es_fa_num(get_the_date('j F Y', $es_post));
$es_readmin = (int) get_post_meta($es_pid, 'es_read_time', true);
if ($es_readmin < 1) {
    $es_words = count(preg_split('/\s+/u', trim(strip_tags((string) ($es_post->post_content ?? '')))) ?: []);
    $es_readmin = max(1, (int) ceil($es_words / 225));
}
$es_author = (string) (get_post_meta($es_pid, 'es_author', true) ?: 'کارشناسان فنی عرفان صنعت');
$es_tags = array_values(array_filter(array_map('trim', explode(',', (string) get_post_meta($es_pid, 'es_tags', true)))));
$es_blog_url = es_page_url('blog');

/** متغیرها برای فاز ۸ (هوک‌های توسعه) */
$es_article_vars = (array) apply_filters('es_article_vars', [
    'post_id' => $es_pid, 'chip' => $es_chip, 'read_minutes' => $es_readmin, 'tags' => $es_tags,
]);

// محتوا + TOC (تزریق id و جمع سرفصل‌ها)
\ErfanSanat\Theme\Frontend\TOC::reset();
$es_content = \ErfanSanat\Theme\Frontend\TOC::process((string) ($es_post->post_content ?? ''));
$es_toc_items_html = '';
foreach (\ErfanSanat\Theme\Frontend\TOC::items() as $es_i => $es_t) {
    $es_toc_items_html .= strtr($ES75_TOC_ITEM, ['{ES_ID}' => esc_attr((string) $es_t['id']), '{ES_TITLE}' => esc_html($es_t['title'])]);
}

// ردیف‌های تگ
$es_tags_hero = '';
$es_tags_box = '';
foreach ($es_tags as $es_tag) {
    $es_tags_hero .= strtr($ES75_TAG_PILL, ['{TAG}' => esc_html('#' . $es_tag)]);
    $es_tags_box .= strtr($ES75_TAG_BOX_PILL, ['{TAG}' => esc_html($es_tag)]);
}

// مقالات مرتبط: ۳ مقاله جدید به‌جز جاری
$es_related = es_related_posts($es_pid, 3);
$es_rel_customs = ['0.05', '0.12000000000000001', '0.19'];
$es_rel_cards = '';
foreach ($es_related as $es_i => $es_rp) {
    ob_start();
    es_get_template('template-parts/cards/card-related.php', ['post' => $es_rp, 'custom' => $es_rel_customs[$es_i % 3]]);
    $es_rel_cards .= (string) ob_get_clean();
}

// ═══ رندر ═══
echo strtr($ES75_HERO_HEAD, [
    '{ES_HOME_URL}' => esc_url(home_url('/')),
    '{ES_BLOG_URL}' => esc_url($es_blog_url),
    '{ES_CRUMB}'    => strtr($ES75_CRUMB_LINK, ['{URL}' => esc_url($es_blog_url), '{X}' => esc_html('وبلاگ')])
        . strtr($ES75_CRUMB_PILL, ['{X}' => esc_html($es_chip)])
        . strtr($ES75_CRUMB_PILL, ['{X}' => esc_html($es_title)]),
]);
echo strtr($ES75_HERO_TAIL, [
    '{ES_CHIP}'      => esc_html($es_chip),
    '{ES_TITLE}'     => esc_html($es_title),
    '{ES_DESC}'      => esc_html($es_excerpt),
    '{ES_AUTHOR}'    => esc_html($es_author),
    '{ES_DATE}'      => esc_html($es_date),
    '{ES_READMIN}'   => es_fa_num((string) $es_readmin),
    '{ES_TAGS_HERO}' => $es_tags_hero,
    '{ES_FEAT}'      => strtr($ES75_FEAT_TPL, ['{ES_IMG}' => esc_url($es_img), '{ES_TITLE}' => esc_attr($es_title)]),
]);
echo $ES75_CONTENT_OPEN;
echo $es_content;
echo $ES75_CONTENT_CLOSE;
echo strtr($ES75_TAGS_SHARE, ['{ES_TAGS_BOX}' => $es_tags_box]);
echo strtr($ES75_ASIDE, ['{ES_TOC_ITEMS}' => $es_toc_items_html]);
echo strtr($ES75_REL_CHROME, ['{ES_BLOG_URL}' => esc_url($es_blog_url)]);
echo $es_rel_cards;
echo $ES75_REL_TAIL;

