<?php
/**
 * سکشن ویترین مقالات (blog-preview) — برش بایت‌دقیق میرور + WP_Query
 * فرگمنت‌ها با temp/gen_blog5.py از refs/blog-preview.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];
$es_count = max(1, (int) ($es_f['count'] ?? 4));

/** فیلتر es_blog_preview_query_args — آرگومان‌های کوئری مقالات */
$es_query_args = apply_filters('es_blog_preview_query_args', [
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => $es_count,
    'ignore_sticky_posts' => true,
]);
$es_q = new WP_Query(is_array($es_query_args) ? $es_query_args : []);
$es_posts = is_array($es_q->posts ?? null) ? $es_q->posts : [];
if ($es_posts === []) {
    return; // بدون مقاله → سکشن مخفی
}

/** فیلتر es_blog_preview_args — توسعه‌پذیری سکشن مقالات */
$es_args = apply_filters('es_blog_preview_args', ['fields' => $es_f, 'query' => $es_q, 'section_id' => (string) ($args['section_id'] ?? 'blog-preview')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}

// لینک آرشیو: برگه نوشته‌ها (page_for_posts)؛ fallback مسیر /blog/
$es_archive_id = (int) get_option('page_for_posts');
$es_archive = $es_archive_id > 0 ? get_permalink($es_archive_id) : '';
$es_archive = is_string($es_archive) && $es_archive !== '' ? $es_archive : es_link_url('/blog/');

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/blog-preview.html
 */
$F_HEAD = <<<'ESB9'
<section id="blog-preview" aria-label="ویترین مقالات کاربردی عرفان صنعت" class="mx-auto max-w-[1300px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8"><div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end"><div class="flex max-w-2xl flex-col gap-3 items-start text-right"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>{ES_KICKER}</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">جدیدترین مقالات و <span class="text-emerald-700">{ES_HL}</span></h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">{ES_EXCERPT}</p></div></div><a href="{ES_ARCHIVE_URL}" class="group inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200/90 bg-white px-5 py-2.5 text-xs font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800 sm:text-sm"><span>{ES_ARCHIVE_TEXT}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4 text-emerald-700 transition-transform group-hover:-translate-x-0.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a></div><div class="mt-8 sm:mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
ESB9;

$CARD_TPL = <<<'ESB9'
<a href="{ES_URL}" class="card-light group flex h-full flex-col justify-between rounded-2xl p-3.5"><div><div class="relative aspect-[4/3] w-full overflow-hidden rounded-xl bg-slate-900"><img src="{ES_IMG}" alt="{ES_TITLE}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div></div><div class="mt-3 flex items-center gap-2 text-[11px] font-bold text-slate-500 tabular-nums"><span class="text-emerald-700">{ES_CHIP}</span><span>·</span><span>{ES_DATE}</span><span>·</span><span>{ES_MIN} دقیقه مطالعه</span></div><h3 class="mt-1.5 line-clamp-2 text-right text-sm leading-6 font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h3><p class="mt-1.5 line-clamp-2 text-right text-xs leading-6 font-medium text-slate-500">{ES_EXCERPT_CARD}</p></div><div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-2.5 text-xs font-extrabold text-emerald-700"><span>مطالعه مقاله</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3.5 w-3.5 transition-transform group-hover:-translate-y-0.5 group-hover:-translate-x-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></div></a>
ESB9;


echo strtr($F_HEAD, [
    '{ES_KICKER}'      => (string) ($es_f['kicker'] ?? ''),
    '{ES_HL}'          => (string) ($es_f['title_highlight'] ?? ''),
    '{ES_EXCERPT}'     => (string) ($es_f['excerpt'] ?? ''),
    '{ES_ARCHIVE_URL}' => $es_archive,
    '{ES_ARCHIVE_TEXT}' => (string) ($es_f['archive_text'] ?? ''),
]);

foreach ($es_posts as $es_post) {
    $es_pid = (int) ($es_post->ID ?? 0);
    $es_title = (string) ($es_post->post_title ?? '');
    // تصویر شاخص؛ alt = عنوان مقاله (الگوی میرور)
    $es_img = es_media_url((int) get_post_meta($es_pid, '_thumbnail_id', true));
    // چیپ دسته: اولین دسته؛ fallback فیلد سکشن
    $es_terms = get_the_terms($es_pid, 'category');
    $es_chip = (is_array($es_terms) && isset($es_terms[0]->name)) ? (string) $es_terms[0]->name : (string) ($es_f['chip_fallback'] ?? '');
    // زمان مطالعه: متا es_read_time؛ وگرنه برآورد از حجم متن (۲۲۵ واژه در دقیقه)
    $es_min = (int) get_post_meta($es_pid, 'es_read_time', true);
    if ($es_min < 1) {
        $es_words = count(preg_split('/\s+/u', trim(strip_tags((string) ($es_post->post_content ?? '')))) ?: []);
        $es_min = max(1, (int) ceil($es_words / 225));
    }
    echo strtr($CARD_TPL, [
        '{ES_URL}'          => (string) get_permalink($es_pid),
        '{ES_IMG}'          => $es_img,
        '{ES_TITLE}'        => $es_title,
        '{ES_CHIP}'         => $es_chip,
        '{ES_DATE}'         => es_fa_num(get_the_date('j F Y', $es_post)),
        '{ES_MIN}'          => es_fa_num((string) $es_min),
        '{ES_EXCERPT_CARD}' => (string) ($es_post->post_excerpt !== '' ? $es_post->post_excerpt : wp_trim_words(strip_tags((string) ($es_post->post_content ?? '')), 32)),
    ]);
}
echo '</div></section>';
