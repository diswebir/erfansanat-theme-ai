#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۸ — برش بایت‌دقیق aftersale + customers از ref و تولید قالب‌ها.
اجرا: python3 temp/gen_ac5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

def nowdoc(name, body, tag):
    return f"${name} = <<<'{tag}'\n{body}\n{tag};\n"

# ═══════════════ aftersale ═══════════════
h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/aftersale.html'), encoding='utf-8').read()
assert h.endswith('</section')
F = h + '>'

F = F.replace('<span>شبکه خدمات پس از فروش ESE</span>', '<span>{ES_BADGE}</span>')
F = F.replace('<span>پوشش سراسری ۳۱ استان کشور با مرکزیت کارخانه اصفهان</span>', '<span>{ES_KICKER}</span>')
F = F.replace('<h2 class="mt-3.5 text-xl font-black text-white sm:text-2xl lg:text-[1.75rem]">پشتیبانی فنی و گارانتی تعویض در سراسر ایران</h2>', '<h2 class="mt-3.5 text-xl font-black text-white sm:text-2xl lg:text-[1.75rem]">{ES_TITLE}</h2>')
F = re.sub(r'(<p class="mt-3 max-w-xl text-right text-xs leading-7 font-medium text-emerald-50/90 sm:text-\[13\.5px\] sm:leading-7">)[^<]*(</p>)', r'\1{ES_DESC}\2', F)
for n, txt in [(1, 'ارسال مستقیم'), (2, '۲۴ ماه گارانتی'), (3, '۵ سال پشتیبانی')]:
    F = F.replace('<div class="text-xs font-black text-white tabular-nums">' + txt + '</div>', '<div class="text-xs font-black text-white tabular-nums">{ES_S' + str(n) + 'T}</div>')
F = F.replace('<div class="mt-0.5 text-[11px] font-medium text-emerald-100/85">از کارخانه مرکزی اصفهان</div>', '<div class="mt-0.5 text-[11px] font-medium text-emerald-100/85">{ES_S1D}</div>')
F = F.replace('<div class="mt-0.5 text-[11px] font-medium text-emerald-100/85">ضمانت تعویض بی\u200cقیدوشرط</div>', '<div class="mt-0.5 text-[11px] font-medium text-emerald-100/85">{ES_S2D}</div>')
F = F.replace('<div class="mt-0.5 text-[11px] font-medium text-emerald-100/85">تامین قطعات در ۳۱ استان</div>', '<div class="mt-0.5 text-[11px] font-medium text-emerald-100/85">{ES_S3D}</div>')
F = F.replace('<a href="tel:tel:03191091011"', '<a href="{ES_PHONE_URL}"')
F = F.replace('<span dir="ltr">۰۳۱-۹۱۰۹۱۰۱۱</span>', '<span dir="ltr">{ES_PHONE}</span>')

# صحت توکن‌گذاری
need = ['{ES_BADGE}', '{ES_KICKER}', '{ES_TITLE}', '{ES_DESC}', '{ES_S1T}', '{ES_S1D}', '{ES_S2T}', '{ES_S2D}', '{ES_S3T}', '{ES_S3D}', '{ES_PHONE_URL}', '{ES_PHONE}']
assert all(t in F for t in need), [t for t in need if t not in F]
assert F.count('{ES_S1T}') == 1 and F.count('{ES_PHONE}') == 1

open(os.path.join(ROOT, 'template-parts/sections/aftersale.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن aftersale — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-aftersale.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-aftersale.php', get_defined_vars());
""")

aft_php = f"""<?php
/**
 * سکشن خدمات پس از فروش (aftersale) — برش بایت‌دقیق میرور + فیلدهای SectionManager
 * نقشه ایران SVG تزئینی میرور عیناً حفظ شده است.
 * فرگمنت با temp/gen_ac5.py از refs/aftersale.html برش خورده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {{section_id: string, fields: array<string, mixed>, post_id: int}} $args */

$es_f = $args['fields'] ?? [];

/** فیلتر es_aftersale_args — توسعه‌پذیری سکشن خدمات پس از فروش */
$es_args = apply_filters('es_aftersale_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'aftersale')]);
if (is_array($es_args)) {{
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}}

{nowdoc('ES_AFTERSALE', F, 'ESA8')}

echo strtr($ES_AFTERSALE, [
    '{{ES_BADGE}}'     => (string) ($es_f['badge'] ?? ''),
    '{{ES_KICKER}}'    => (string) ($es_f['kicker'] ?? ''),
    '{{ES_TITLE}}'     => (string) ($es_f['title'] ?? ''),
    '{{ES_DESC}}'      => (string) ($es_f['desc'] ?? ''),
    '{{ES_S1T}}'       => (string) ($es_f['s1_title'] ?? ''),
    '{{ES_S1D}}'       => (string) ($es_f['s1_desc'] ?? ''),
    '{{ES_S2T}}'       => (string) ($es_f['s2_title'] ?? ''),
    '{{ES_S2D}}'       => (string) ($es_f['s2_desc'] ?? ''),
    '{{ES_S3T}}'       => (string) ($es_f['s3_title'] ?? ''),
    '{{ES_S3D}}'       => (string) ($es_f['s3_desc'] ?? ''),
    '{{ES_PHONE}}'     => (string) ($es_f['phone'] ?? ''),
    '{{ES_PHONE_URL}}' => (string) ($es_f['phone_url'] ?? ''),
]);
"""
open(os.path.join(ROOT, 'template-parts/home/section-aftersale.php'), 'w', encoding='utf-8').write(aft_php)
print('aftersale done:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-aftersale.php')))

# ═══════════════ customers ═══════════════
h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/customers.html'), encoding='utf-8').read()
assert h.endswith('</section')

# هدر تا گرید
i = h.find('<div class="mt-8 sm:mt-10 grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-6">')
F_HEAD = h[:i + len('<div class="mt-8 sm:mt-10 grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-6">')]
F_HEAD = F_HEAD.replace('<span>شبکه گسترده کارفرمایان</span>', '<span>{ES_KICKER}</span>')
F_HEAD = F_HEAD.replace('مشتریان و سازمان\u200cهای <span class="text-emerald-700">همکار عرفان صنعت</span>', 'مشتریان و سازمان\u200cهای <span class="text-emerald-700">{ES_HL}</span>')
F_HEAD = re.sub(r'(<p class="text-sm leading-7 font-medium text-slate-600 sm:text-\[15px\] sm:leading-8">)[^<]*(</p>)', r'\1{ES_EXCERPT}\2', F_HEAD)

# بدنه: کارت‌ها + بستن‌ها
body = h[i + len('<div class="mt-8 sm:mt-10 grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-6">'):]
assert body.count('card-light') == 12
# کارت اول به‌عنوان قالب
m = re.search(r'<div class="card-light flex flex-col items-start justify-between rounded-2xl p-4 text-right">.*?</div></div></div>', body, re.S)
CARD = m.group(0)
CARD = CARD.replace('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-landmark h-4.5 w-4.5" aria-hidden="true"><path d="M10 18v-7"></path><path d="M11.12 2.198a2 2 0 0 1 1.76.006l7.866 3.847c.476.233.31.949-.22.949H3.474c-.53 0-.695-.716-.22-.949z"></path><path d="M14 18v-7"></path><path d="M18 18v-7"></path><path d="M3 22h18"></path><path d="M6 18v-7"></path></svg>', '{ES_ICON}')
CARD = re.sub(r'(<span class="font-mono text-\[11px\] font-bold text-slate-400 tabular-nums">)[^<]*(</span>)', r'\1{ES_NUM}\2', CARD)
CARD = CARD.replace('<div class="text-xs sm:text-[13px] font-extrabold text-slate-900">شهرداری اصفهان</div>', '<div class="text-xs sm:text-[13px] font-extrabold text-slate-900">{ES_TITLE}</div>')
CARD = CARD.replace('<div class="mt-0.5 line-clamp-1 text-[10.5px] font-medium text-slate-500">پروژه\u200cهای تونل و المان نوری</div>', '<div class="mt-0.5 line-clamp-1 text-[10.5px] font-medium text-slate-500">{ES_SUB}</div>')
assert all(t in CARD for t in ['{ES_ICON}', '{ES_NUM}', '{ES_TITLE}', '{ES_SUB}'])

# اطمینان: اسکلت ۱۲ کارت هم‌الگو
def norm_card(s):
    s = re.sub(r'<svg.*?</svg>', 'SVG', s, flags=re.S)
    s = re.sub(r'>([^<>]+)</', '>X</', s)
    s = re.sub(r'\d', 'N', s)
    return s
n0 = norm_card(CARD)
cards = re.findall(r'<div class="card-light flex flex-col items-start justify-between rounded-2xl p-4 text-right">.*?</div></div></div>', body, re.S)
assert len(cards) == 12
for k, c in enumerate(cards):
    assert norm_card(c) == n0, f'card{k} differs'

# بستن‌ها: بعد از آخرین کارت
tail_start = body.rfind(cards[11]) + len(cards[11])
TAIL = body[tail_start:]
assert TAIL == '</div></section', repr(TAIL)

open(os.path.join(ROOT, 'template-parts/sections/customers.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن customers — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-customers.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-customers.php', get_defined_vars());
""")

cus_php = f"""<?php
/**
 * سکشن مشتریان و سازمان‌های همکار (customers) — برش بایت‌دقیق میرور + رپیتر کارت‌ها
 * فرگمنت‌ها با temp/gen_ac5.py از refs/customers.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\\Theme\\Frontend\\Icons;

/** @var array {{section_id: string, fields: array<string, mixed>, post_id: int}} $args */

$es_f = $args['fields'] ?? [];
$es_rows = is_array($es_f['rows'] ?? null) ? array_values($es_f['rows']) : [];
if ($es_rows === []) {{
    return; // بدون مشتری → سکشن مخفی
}}

/** فیلتر es_customers_args — توسعه‌پذیری سکشن مشتریان */
$es_args = apply_filters('es_customers_args', ['fields' => $es_f, 'rows' => $es_rows, 'section_id' => (string) ($args['section_id'] ?? 'customers')]);
if (is_array($es_args)) {{
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_rows = is_array($es_args['rows'] ?? null) ? array_values($es_args['rows']) : $es_rows;
}}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/customers.html
 */
{nowdoc('F_HEAD', F_HEAD, 'ESC8')}
{nowdoc('CARD_TPL', CARD, 'ESC8')}

echo strtr($F_HEAD, [
    '{{ES_KICKER}}'  => (string) ($es_f['kicker'] ?? ''),
    '{{ES_HL}}'      => (string) ($es_f['title_highlight'] ?? ''),
    '{{ES_EXCERPT}}' => (string) ($es_f['excerpt'] ?? ''),
]);

foreach ($es_rows as $es_i => $es_row) {{
    if (!is_array($es_row)) {{
        continue;
    }}
    echo strtr($CARD_TPL, [
        '{{ES_ICON}}'  => Icons::svg((string) ($es_row['icon'] ?? 'landmark'), 'lucide lucide-' . (string) ($es_row['icon'] ?? 'landmark') . ' h-4.5 w-4.5'),
        '{{ES_NUM}}'   => es_fa_num(str_pad((string) ($es_i + 1), 2, '0', STR_PAD_LEFT)),
        '{{ES_TITLE}}' => (string) ($es_row['title'] ?? ''),
        '{{ES_SUB}}'   => (string) ($es_row['sub'] ?? ''),
    ]);
}}
echo '{TAIL}>';
"""
open(os.path.join(ROOT, 'template-parts/home/section-customers.php'), 'w', encoding='utf-8').write(cus_php)
print('customers done:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-customers.php')))
