#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۹ — برش بایت‌دقیق blog-preview از ref و تولید قالب WP_Query.
اجرا: python3 temp/gen_blog5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

def nowdoc(name, body, tag):
    return f"${name} = <<<'{tag}'\n{body}\n{tag};\n"

h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/blog-preview.html'), encoding='utf-8').read()
assert h.endswith('</section')

# هدر تا گرید
GRID = '<div class="mt-8 sm:mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">'
i = h.find(GRID)
F_HEAD = h[:i + len(GRID)]
F_HEAD = F_HEAD.replace('<span>دانشنامه و مجله فنی</span>', '<span>{ES_KICKER}</span>')
F_HEAD = F_HEAD.replace('جدیدترین مقالات و <span class="text-emerald-700">راهنماهای تخصصی نورپردازی</span>', 'جدیدترین مقالات و <span class="text-emerald-700">{ES_HL}</span>')
F_HEAD = re.sub(r'(<p class="text-sm leading-7 font-medium text-slate-600 sm:text-\[15px\] sm:leading-8">)[^<]*(</p>)', r'\1{ES_EXCERPT}\2', F_HEAD)
F_HEAD = F_HEAD.replace('<a href="blog.html" class="group inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200/90 bg-white px-5 py-2.5 text-xs font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800 sm:text-sm"><span>مشاهده آرشیو مقالات</span>', '<a href="{ES_ARCHIVE_URL}" class="group inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200/90 bg-white px-5 py-2.5 text-xs font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800 sm:text-sm"><span>{ES_ARCHIVE_TEXT}</span>')
assert all(t in F_HEAD for t in ['{ES_KICKER}', '{ES_HL}', '{ES_EXCERPT}', '{ES_ARCHIVE_URL}', '{ES_ARCHIVE_TEXT}'])

# کارت اول → قالب
body = h[i + len(GRID):]
m = re.search(r'<a href="[^"]*" class="card-light group flex h-full flex-col justify-between rounded-2xl p-3\.5">.*?</a>', body, re.S)
CARD = m.group(0)
CARD = re.sub(r'href="[^"]*"', 'href="{ES_URL}"', CARD, count=1)
CARD = re.sub(r'(<img src=")images/[^"]*(" alt=")[^"]*(")', r'\1{ES_IMG}\2{ES_TITLE}\3', CARD)
CARD = re.sub(r'(<span class="text-emerald-700">)[^<]*(</span>)', r'\1{ES_CHIP}\2', CARD)
CARD = re.sub(r'(<span>·</span><span>)[^<]+(</span><span>·</span><span>)[^<]+(</span>)', r'\1{ES_DATE}\2{ES_MIN} دقیقه مطالعه\3', CARD, count=1)
CARD = re.sub(r'(<h3 class="mt-1\.5 line-clamp-2 text-right text-sm leading-6 font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700">)[^<]*(</h3>)', r'\1{ES_TITLE}\2', CARD)
CARD = re.sub(r'(<p class="mt-1\.5 line-clamp-2 text-right text-xs leading-6 font-medium text-slate-500">)[^<]*(</p>)', r'\1{ES_EXCERPT_CARD}\2', CARD)
assert all(t in CARD for t in ['{ES_URL}', '{ES_IMG}', '{ES_TITLE}', '{ES_CHIP}', '{ES_DATE}', '{ES_MIN}', '{ES_EXCERPT_CARD}']), [t for t in ['{ES_URL}', '{ES_IMG}', '{ES_CHIP}', '{ES_DATE}', '{ES_MIN}', '{ES_EXCERPT_CARD}'] if t not in CARD]

# اسکلت ۴ کارت هم‌الگو
def norm_card(s):
    s = re.sub(r'<svg.*?</svg>', 'SVG', s, flags=re.S)
    s = re.sub(r'href="[^"]*"', 'H', s)
    s = re.sub(r'src="[^"]*"', 'S', s)
    s = re.sub(r'alt="[^"]*"', 'T', s)
    s = re.sub(r'>([^<>]+)</', '>X</', s)
    s = re.sub(r'\d', 'N', s)
    return s
n0 = norm_card(CARD)
cards = re.findall(r'<a href="[^"]*" class="card-light group flex h-full flex-col justify-between rounded-2xl p-3\.5">.*?</a>', body, re.S)
assert len(cards) == 4, len(cards)
for k, c in enumerate(cards):
    assert norm_card(c) == n0, f'card{k} differs'

TAIL = body[body.rfind(cards[3]) + len(cards[3]):]
assert TAIL == '</div></section', repr(TAIL)

open(os.path.join(ROOT, 'template-parts/sections/blog-preview.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن blog-preview — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-blog.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-blog.php', get_defined_vars());
""")

blog_php = f"""<?php
/**
 * سکشن ویترین مقالات (blog-preview) — برش بایت‌دقیق میرور + WP_Query
 * فرگمنت‌ها با temp/gen_blog5.py از refs/blog-preview.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {{section_id: string, fields: array<string, mixed>, post_id: int}} $args */

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
if ($es_posts === []) {{
    return; // بدون مقاله → سکشن مخفی
}}

/** فیلتر es_blog_preview_args — توسعه‌پذیری سکشن مقالات */
$es_args = apply_filters('es_blog_preview_args', ['fields' => $es_f, 'query' => $es_q, 'section_id' => (string) ($args['section_id'] ?? 'blog-preview')]);
if (is_array($es_args)) {{
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}}

// لینک آرشیو: برگه نوشته‌ها (page_for_posts)؛ fallback مسیر /blog/
$es_archive_id = (int) get_option('page_for_posts');
$es_archive = $es_archive_id > 0 ? get_permalink($es_archive_id) : '';
$es_archive = is_string($es_archive) && $es_archive !== '' ? $es_archive : es_link_url('/blog/');

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/blog-preview.html
 */
{nowdoc('F_HEAD', F_HEAD, 'ESB9')}
{nowdoc('CARD_TPL', CARD, 'ESB9')}

echo strtr($F_HEAD, [
    '{{ES_KICKER}}'      => (string) ($es_f['kicker'] ?? ''),
    '{{ES_HL}}'          => (string) ($es_f['title_highlight'] ?? ''),
    '{{ES_EXCERPT}}'     => (string) ($es_f['excerpt'] ?? ''),
    '{{ES_ARCHIVE_URL}}' => $es_archive,
    '{{ES_ARCHIVE_TEXT}}' => (string) ($es_f['archive_text'] ?? ''),
]);

foreach ($es_posts as $es_post) {{
    $es_pid = (int) ($es_post->ID ?? 0);
    $es_title = (string) ($es_post->post_title ?? '');
    // تصویر شاخص؛ alt = عنوان مقاله (الگوی میرور)
    $es_img = es_media_url((int) get_post_meta($es_pid, '_thumbnail_id', true));
    // چیپ دسته: اولین دسته؛ fallback فیلد سکشن
    $es_terms = get_the_terms($es_pid, 'category');
    $es_chip = (is_array($es_terms) && isset($es_terms[0]->name)) ? (string) $es_terms[0]->name : (string) ($es_f['chip_fallback'] ?? '');
    // زمان مطالعه: متا es_read_time؛ وگرنه برآورد از حجم متن (۲۲۵ واژه در دقیقه)
    $es_min = (int) get_post_meta($es_pid, 'es_read_time', true);
    if ($es_min < 1) {{
        $es_words = count(preg_split('/\\s+/u', trim(strip_tags((string) ($es_post->post_content ?? '')))) ?: []);
        $es_min = max(1, (int) ceil($es_words / 225));
    }}
    echo strtr($CARD_TPL, [
        '{{ES_URL}}'          => (string) get_permalink($es_pid),
        '{{ES_IMG}}'          => $es_img,
        '{{ES_TITLE}}'        => $es_title,
        '{{ES_CHIP}}'         => $es_chip,
        '{{ES_DATE}}'         => es_fa_num(get_the_date('j F Y', $es_post)),
        '{{ES_MIN}}'          => es_fa_num((string) $es_min),
        '{{ES_EXCERPT_CARD}}' => (string) ($es_post->post_excerpt !== '' ? $es_post->post_excerpt : wp_trim_words(strip_tags((string) ($es_post->post_content ?? '')), 32)),
    ]);
}}
echo '{TAIL}>';
"""
open(os.path.join(ROOT, 'template-parts/home/section-blog.php'), 'w', encoding='utf-8').write(blog_php)
print('blog-preview done:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-blog.php')))
