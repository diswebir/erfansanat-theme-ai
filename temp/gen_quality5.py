#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۱۱الف — برش بایت‌دقیق quality از ref (سکشن جاافتاده در درخت تسک‌ها).
اجرا: python3 temp/gen_quality5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

def nowdoc(name, body, tag):
    return f"${name} = <<<'{tag}'\n{body}\n{tag};\n"

h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/quality.html'), encoding='utf-8').read()
assert h.endswith('</section')

# هدر تا گرید ویژگی‌ها
GRID = '<div class="mt-8 sm:mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">'
i = h.find(GRID)
F_HEAD = h[:i + len(GRID)]
F_HEAD = F_HEAD.replace('<span>مزیت\u200cهای رقابتی کارخانه</span>', '<span>{ES_KICKER}</span>')
F_HEAD = F_HEAD.replace('استانداردهای <span class="text-emerald-700">کیفیت و ماندگاری</span>', 'استاندارد{ES_PRE_SPAN}های <span class="text-emerald-700">{ES_HL}</span>')
# «استانداردهای» → «استاندارد» + «های» تا توکن وسط کلمه ممکن شود؛ اما بایت‌دقیق باید برسد:
# بازگردانی: الگوی ساده‌تر — کل متن h2 بین تگ‌ها
F_HEAD = F_HEAD.replace('استاندارد{ES_PRE_SPAN}های <span class="text-emerald-700">{ES_HL}</span>', '{ES_TITLE_PRE}<span class="text-emerald-700">{ES_HL}</span>')
F_HEAD = F_HEAD.replace('<h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">{ES_TITLE_PRE}', '<h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">{ES_TITLE_PRE}')
F_HEAD = re.sub(r'(<p class="text-sm leading-7 font-medium text-slate-600 sm:text-\[15px\] sm:leading-8">)[^<]*(</p>)', r'\1{ES_EXCERPT}\2', F_HEAD)
assert all(t in F_HEAD for t in ['{ES_KICKER}', '{ES_TITLE_PRE}', '{ES_HL}', '{ES_EXCERPT}'])

# بدنه: کارت‌های ویژگی + باند فرآیند + گام‌ها
body = h[i + len(GRID):]

# چهار کارت ویژگی (wrapper custom + card-light)
fmarks = [m.start() for m in re.finditer(r'<div custom="[^"]*"><div class="card-light flex h-full flex-col justify-between rounded-2xl p-5 sm:p-6">', body)]
# پایان گرید ویژگی‌ها = شروع باند فرآیند
j = body.find('<div class="mt-8 sm:mt-10" custom="0.14">')
assert fmarks == sorted(fmarks) and len(fmarks) == 4, fmarks
FEAT_BLOCK = body[fmarks[0]:j]

# کارت قالب: کارت اول را توکن‌دار کن
fm = re.search(r'<div custom="[^"]*"><div class="card-light flex h-full flex-col justify-between rounded-2xl p-5 sm:p-6">.*?</div></div></div>', FEAT_BLOCK, re.S)
CARD = fm.group(0)
CARD = re.sub(r'(<div custom=")[^"]*(">)', r'\1{ES_CUSTOM}\2', CARD)
CARD = re.sub(r'<svg xmlns="http://www\.w3\.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1\.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-[a-z0-9-]+ h-5\.5 w-5\.5" aria-hidden="true">.*?</svg>', '{ES_ICON}', CARD, flags=re.S)
CARD = re.sub(r'(<span class="font-mono text-xs font-extrabold text-emerald-700 tabular-nums">)[^<]*(</span>)', r'\1{ES_BADGE}\2', CARD)
CARD = re.sub(r'(<h3 class="mt-4 text-base font-black text-slate-900">)[^<]*(</h3>)', r'\1{ES_TITLE}\2', CARD)
CARD = re.sub(r'(<p class="mt-2 text-xs sm:text-\[13px\] font-medium leading-6 text-slate-600">)[^<]*(</p>)', r'\1{ES_DESC}\2', CARD)
assert all(t in CARD for t in ['{ES_CUSTOM}', '{ES_ICON}', '{ES_BADGE}', '{ES_TITLE}', '{ES_DESC}'])

def norm(s):
    s = re.sub(r'<svg.*?</svg>', 'SVG', s, flags=re.S)
    s = re.sub(r'custom="[^"]*"', 'CU', s)
    s = re.sub(r'>([^<>]+)</', '>X</', s)
    s = re.sub(r'\d', 'N', s)
    return s
n0 = norm(CARD)
fcards = re.findall(r'<div custom="[^"]*"><div class="card-light flex h-full flex-col justify-between rounded-2xl p-5 sm:p-6">.*?</div></div></div>', FEAT_BLOCK, re.S)
assert len(fcards) == 4
for k, c in enumerate(fcards):
    assert norm(c) == n0, f'feat{k} differs'
# تأخیرهای میرور
DELAYS = [re.search(r'<div custom="([^"]*)">', c).group(1) for c in fcards]
assert DELAYS == ['0.05', '0.1', '0.15000000000000002', '0.2'], DELAYS

# باند فرآیند + گام‌ها + بستن‌ها
BAND = body[j:]
BAND = BAND.replace('<h3 class="text-base sm:text-lg font-black text-slate-900">فرآیند اجرای پروژه در عرفان صنعت؛ <span class="text-emerald-700">از ایده تا درخشش در شهر</span></h3>', '<h3 class="text-base sm:text-lg font-black text-slate-900">{ES_PROC_PRE} <span class="text-emerald-700">{ES_PROC_HL}</span></h3>')
BAND = BAND.replace('<span class="text-xs font-bold text-slate-500">۵ گام استاندارد مهندسی و اجرایی</span>', '<span class="text-xs font-bold text-slate-500">{ES_PROC_LABEL}</span>')
assert '{ES_PROC_PRE}' in BAND and '{ES_PROC_HL}' in BAND and '{ES_PROC_LABEL}' in BAND

# پنج گام
sgrid = BAND.find('<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">')
sgrid_end = sgrid + len('<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">')
BAND_HEAD = BAND[:sgrid_end]
STEPS_TAIL = BAND[sgrid_end:]
sm = re.search(r'<div class="flex items-center gap-3\.5 rounded-2xl border border-slate-100 bg-\[#f6f8f7\]/80 p-3\.5 lg:flex-col lg:items-start lg:p-4">.*?</div></div></div>', STEPS_TAIL, re.S)
STEP = sm.group(0)
STEP = re.sub(r'<svg xmlns="http://www\.w3\.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1\.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-[a-z0-9-]+ h-5 w-5" aria-hidden="true">.*?</svg>', '{ES_ICON}', STEP, flags=re.S)
STEP = re.sub(r'(<span class="text-xs font-black text-emerald-700/70 tabular-nums">)[^<]*(</span>)', r'\1{ES_STEP}\2', STEP)
STEP = re.sub(r'(<div class="text-xs sm:text-\[13px\] font-extrabold text-slate-900">)[^<]*(</div>)', r'\1{ES_TITLE}\2', STEP)
STEP = re.sub(r'(<div class="mt-0\.5 text-\[11px\] font-medium text-slate-500">)[^<]*(</div>)', r'\1{ES_SUB}\2', STEP)
assert all(t in STEP for t in ['{ES_ICON}', '{ES_STEP}', '{ES_TITLE}', '{ES_SUB}'])

steps = re.findall(r'<div class="flex items-center gap-3\.5 rounded-2xl border border-slate-100 bg-\[#f6f8f7\]/80 p-3\.5 lg:flex-col lg:items-start lg:p-4">.*?</div></div></div>', STEPS_TAIL, re.S)
assert len(steps) == 5
for k, st in enumerate(steps):
    assert norm(st) == norm(steps[0]), f'step{k} differs'
TAIL = STEPS_TAIL[STEPS_TAIL.rfind(steps[4]) + len(steps[4]):]
assert TAIL == '</div></div></div></div></section', repr(TAIL)

# آیکون‌های جدید
ip_path = os.path.join(ROOT, 'inc/Frontend/Icons.php')
ip = open(ip_path, encoding='utf-8').read()
for svg_full in re.findall(r'<svg xmlns="http://www\.w3\.org/2000/svg"[^>]*>.*?</svg>', h, re.S):
    cm = re.search(r'lucide lucide-([a-z0-9-]+) h-5', svg_full)
    if cm and f"'{cm.group(1)}' =>" not in ip:
        inner = re.match(r'<svg [^>]*>(.*)</svg>$', svg_full, re.S).group(1)
        idx = ip.rfind('    ];')
        ip = ip[:idx] + f"        '{cm.group(1)}' => <<<'ESIX'\n{inner}\nESIX,\n" + ip[idx:]
open(ip_path, 'w', encoding='utf-8').write(ip)

open(os.path.join(ROOT, 'template-parts/sections/quality.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن quality — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-quality.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-quality.php', get_defined_vars());
""")

q_php = f"""<?php
/**
 * سکشن مزیت‌های رقابتی و فرآیند اجرا (quality) — برش بایت‌دقیق میرور
 * دو رپیتر: ویژگی‌ها (۴ کارت) و گام‌ها (۵ کارت)؛ آیکون‌های این سکشن stroke=1.75.
 * فرگمنت‌ها با temp/gen_quality5.py از refs/quality.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\\Theme\\Frontend\\Icons;

/** @var array {{section_id: string, fields: array<string, mixed>, post_id: int}} $args */

$es_f = $args['fields'] ?? [];
$es_feats = is_array($es_f['features'] ?? null) ? array_values($es_f['features']) : [];
$es_steps = is_array($es_f['steps'] ?? null) ? array_values($es_f['steps']) : [];
if ($es_feats === [] && $es_steps === []) {{
    return; // بدون محتوا → سکشن مخفی
}}

/** فیلتر es_quality_args — توسعه‌پذیری سکشن مزیت‌ها */
$es_args = apply_filters('es_quality_args', ['fields' => $es_f, 'features' => $es_feats, 'steps' => $es_steps, 'section_id' => (string) ($args['section_id'] ?? 'quality')]);
if (is_array($es_args)) {{
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_feats = is_array($es_args['features'] ?? null) ? array_values($es_args['features']) : $es_feats;
    $es_steps = is_array($es_args['steps'] ?? null) ? array_values($es_args['steps']) : $es_steps;
}}

/** تأخیرهای نمایش میرور (عیناً حفظ شده؛ شامل اعشار بلند کارت ۴) */
$es_delays = {DELAYS!r};

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/quality.html
 */
{nowdoc('F_HEAD', F_HEAD, 'ESQ11')}
{nowdoc('CARD_TPL', CARD, 'ESQ11')}
{nowdoc('BAND_HEAD', BAND_HEAD, 'ESQ11')}
{nowdoc('STEP_TPL', STEP, 'ESQ11')}

echo strtr($F_HEAD, [
    '{{ES_KICKER}}'  => (string) ($es_f['kicker'] ?? ''),
    '{{ES_TITLE_PRE}}' => (string) ($es_f['title_pre'] ?? ''),
    '{{ES_HL}}'      => (string) ($es_f['title_hl'] ?? ''),
    '{{ES_EXCERPT}}' => (string) ($es_f['excerpt'] ?? ''),
]);

// کارت‌های ویژگی
foreach ($es_feats as $es_i => $es_row) {{
    if (!is_array($es_row)) {{
        continue;
    }}
    $es_icn = (string) ($es_row['icon'] ?? 'badge-check');
    echo strtr($CARD_TPL, [
        '{{ES_CUSTOM}}' => (string) ($es_delays[$es_i] ?? end($es_delays)),
        '{{ES_ICON}}'   => Icons::svg($es_icn, 'lucide lucide-' . $es_icn . ' h-5.5 w-5.5', '1.75'),
        '{{ES_BADGE}}'  => (string) ($es_row['badge'] ?? ''),
        '{{ES_TITLE}}'  => (string) ($es_row['title'] ?? ''),
        '{{ES_DESC}}'   => (string) ($es_row['desc'] ?? ''),
    ]);
}}
echo '</div>';

// باند فرآیند + گام‌ها
echo strtr($BAND_HEAD, [
    '{{ES_PROC_PRE}}'   => (string) ($es_f['process_pre'] ?? ''),
    '{{ES_PROC_HL}}'    => (string) ($es_f['process_hl'] ?? ''),
    '{{ES_PROC_LABEL}}' => (string) ($es_f['process_label'] ?? ''),
]);
foreach ($es_steps as $es_row) {{
    if (!is_array($es_row)) {{
        continue;
    }}
    $es_icn = (string) ($es_row['icon'] ?? 'search');
    echo strtr($STEP_TPL, [
        '{{ES_ICON}}'  => Icons::svg($es_icn, 'lucide lucide-' . $es_icn . ' h-5 w-5', '1.75'),
        '{{ES_STEP}}'  => (string) ($es_row['step'] ?? ''),
        '{{ES_TITLE}}' => (string) ($es_row['title'] ?? ''),
        '{{ES_SUB}}'   => (string) ($es_row['sub'] ?? ''),
    ]);
}}
echo '{TAIL}>';
"""
open(os.path.join(ROOT, 'template-parts/home/section-quality.php'), 'w', encoding='utf-8').write(q_php)
print('quality done:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-quality.php')))
