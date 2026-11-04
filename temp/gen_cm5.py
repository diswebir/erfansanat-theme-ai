#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۱۰ — برش بایت‌دقیق contact + map از ref و تولید قالب‌ها.
اجرا: python3 temp/gen_cm5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

def nowdoc(name, body, tag):
    return f"${name} = <<<'{tag}'\n{body}\n{tag};\n"

# ═══════════════ contact ═══════════════
h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/contact.html'), encoding='utf-8').read()
assert h.endswith('</section')
F = h + '>'

F = F.replace('<span>مشاوره مهندسی و استعلام قیمت مستقیم از کارخانه</span>', '<span>{ES_KICKER}</span>')
F = F.replace('>روشنایی و هویت بصری متمایز را به <span class="text-emerald-700">شهر خود</span> دعوت کنید</h2>', '>{ES_TITLE_PRE}<span class="text-emerald-700">{ES_HL}</span>{ES_TITLE_POST}</h2>')
F = re.sub(r'(<p class="max-w-xl text-sm leading-7 font-medium text-slate-600 sm:text-\[15px\] sm:leading-8">)[^<]*(</p>)', r'\1{ES_DESC}\2', F)
F = F.replace('<a href="tel:031-91091011" class="group inline-flex items-center justify-center gap-2.5 rounded-xl bg-emerald-700 px-6 py-3.5 text-xs sm:text-sm font-black text-white transition-colors hover:bg-emerald-800">', '<a href="{ES_CTA1_URL}" class="group inline-flex items-center justify-center gap-2.5 rounded-xl bg-emerald-700 px-6 py-3.5 text-xs sm:text-sm font-black text-white transition-colors hover:bg-emerald-800">')
F = F.replace('<span>تماس مستقیم با کارشناسان فروش</span>', '<span>{ES_CTA1}</span>')
F = F.replace('<a href="contact.html" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-[#f6f8f7] px-5 py-3.5 text-xs sm:text-sm font-extrabold text-slate-800 transition-colors hover:border-emerald-300 hover:bg-emerald-50/60">', '<a href="{ES_CTA2_URL}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-[#f6f8f7] px-5 py-3.5 text-xs sm:text-sm font-extrabold text-slate-800 transition-colors hover:border-emerald-300 hover:bg-emerald-50/60">')
F = F.replace('<span>درخواست پیش\u200cفاکتور رسمی ستاد</span>', '<span>{ES_CTA2}</span>')
F = F.replace('>تماس مستقیم با واحد فروش و مهندسی</div>', '>{ES_PHONE_LABEL}</div>')
F = F.replace('<a href="tel:031-91091011" class="mt-0.5 block truncate text-sm sm:text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700">۰۳۱ - ۹۱۰۹۱۰۱۱</a>', '<a href="{ES_PHONE_URL}" class="mt-0.5 block truncate text-sm sm:text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700">{ES_PHONE}</a>')
F = F.replace('>۱۰ خط ویژه</div>', '>{ES_PHONE_SUB}</div>')
F = F.replace('>پست الکترونیک سازمانی</div>', '>{ES_EMAIL_LABEL}</div>')
F = F.replace('<a href="mailto:info@erfansanat.com" dir="ltr" class="mt-0.5 block truncate text-sm sm:text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700">info@erfansanat.com</a>', '<a href="mailto:{ES_EMAIL}" dir="ltr" class="mt-0.5 block truncate text-sm sm:text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700">{ES_EMAIL}</a>')
F = F.replace('>دریافت استعلام فنی و پیش\u200cفاکتور رسمی</div>', '>{ES_EMAIL_SUB}</div>')
F = F.replace('>ساعات پاسخگویی کارخانه</div>', '>{ES_HOURS_LABEL}</div>')
F = F.replace('>شنبه تا پنجشنبه · ۷:۰۰ الی ۱۶:۳۰</div>', '>{ES_HOURS}</div>')
F = F.replace('>پشتیبانی تلفنی پروژه\u200cها در تمام ایام هفته</div>', '>{ES_HOURS_SUB}</div>')

need = ['{ES_KICKER}', '{ES_TITLE_PRE}', '{ES_HL}', '{ES_TITLE_POST}', '{ES_DESC}', '{ES_CTA1_URL}', '{ES_CTA1}', '{ES_CTA2_URL}', '{ES_CTA2}',
        '{ES_PHONE_LABEL}', '{ES_PHONE_URL}', '{ES_PHONE}', '{ES_PHONE_SUB}', '{ES_EMAIL_LABEL}', '{ES_EMAIL}', '{ES_EMAIL_SUB}',
        '{ES_HOURS_LABEL}', '{ES_HOURS}', '{ES_HOURS_SUB}']
assert all(t in F for t in need), [t for t in need if t not in F]

open(os.path.join(ROOT, 'template-parts/sections/contact.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن contact — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-contact.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-contact.php', get_defined_vars());
""")

con_php = f"""<?php
/**
 * سکشن تماس با ما (contact) — برش بایت‌دقیق میرور + فیلدهای SectionManager
 * مقادیر تماس: فیلد سکشن در اولویت؛ فیلد خالی → گزینه‌های مرکزی (es_contact_args).
 * فرگمنت با temp/gen_cm5.py از refs/contact.html برش خورده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\\Theme\\Admin\\OptionsOutput;

/** @var array {{section_id: string, fields: array<string, mixed>, post_id: int}} $args */

$es_f = $args['fields'] ?? [];

/** فیلتر es_contact_args — توسعه‌پذیری سکشن تماس */
$es_args = apply_filters('es_contact_section_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'contact')]);
if (is_array($es_args)) {{
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}}

$es_c = OptionsOutput::contact_args();

// رزولوشن مقادیر: فیلد → گزینه مرکزی → خالی
$es_phone = trim((string) ($es_f['phone'] ?? ''));
$es_phone = $es_phone !== '' ? $es_phone : es_fa_num((string) $es_c['phone']);
$es_email = trim((string) ($es_f['email'] ?? ''));
$es_email = $es_email !== '' ? $es_email : (string) $es_c['email'];
$es_hours = trim((string) ($es_f['hours'] ?? ''));
$es_hours = $es_hours !== '' ? $es_hours : (string) $es_c['hours'];
$es_phone_url = trim((string) ($es_f['phone_url'] ?? ''));
$es_phone_url = $es_phone_url !== '' ? $es_phone_url : 'tel:' . (string) $es_c['phone'];

// مخفی‌سازی: بدون متن دعوت و بدون هیچ تماس
if ((string) ($es_f['desc'] ?? '') === '' && $es_phone === '' && $es_email === '') {{
    return;
}}

{nowdoc('ES_CONTACT', F, 'ESC10')}

echo strtr($ES_CONTACT, [
    '{{ES_KICKER}}'      => (string) ($es_f['kicker'] ?? ''),
    '{{ES_TITLE_PRE}}'   => (string) ($es_f['title_pre'] ?? ''),
    '{{ES_HL}}'          => (string) ($es_f['title_hl'] ?? ''),
    '{{ES_TITLE_POST}}'  => (string) ($es_f['title_post'] ?? ''),
    '{{ES_DESC}}'        => (string) ($es_f['desc'] ?? ''),
    '{{ES_CTA1_URL}}'    => es_link_url((string) ($es_f['cta1_url'] ?? '')),
    '{{ES_CTA1}}'        => (string) ($es_f['cta1_text'] ?? ''),
    '{{ES_CTA2_URL}}'    => es_link_url((string) ($es_f['cta2_url'] ?? '')),
    '{{ES_CTA2}}'        => (string) ($es_f['cta2_text'] ?? ''),
    '{{ES_PHONE_LABEL}}' => (string) ($es_f['phone_label'] ?? ''),
    '{{ES_PHONE_URL}}'   => $es_phone_url,
    '{{ES_PHONE}}'       => $es_phone,
    '{{ES_PHONE_SUB}}'   => (string) ($es_f['phone_sub'] ?? ''),
    '{{ES_EMAIL_LABEL}}' => (string) ($es_f['email_label'] ?? ''),
    '{{ES_EMAIL}}'       => $es_email,
    '{{ES_EMAIL_SUB}}'   => (string) ($es_f['email_sub'] ?? ''),
    '{{ES_HOURS_LABEL}}' => (string) ($es_f['hours_label'] ?? ''),
    '{{ES_HOURS}}'       => $es_hours,
    '{{ES_HOURS_SUB}}'   => (string) ($es_f['hours_sub'] ?? ''),
]);
"""
open(os.path.join(ROOT, 'template-parts/home/section-contact.php'), 'w', encoding='utf-8').write(con_php)
print('contact done:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-contact.php')))

# ═══════════════ map ═══════════════
h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/map.html'), encoding='utf-8').read()
assert h.endswith('</section')
M = h + '>'

M = M.replace('>مراجعه حضوری و بازدید از خط تولید</div>', '>{ES_KICKER}</div>')
M = M.replace('<h3 class="mt-1.5 text-lg font-black text-white">نشانی کارخانه و دفتر مرکزی اصفهان</h3>', '<h3 class="mt-1.5 text-lg font-black text-white">{ES_TITLE}</h3>')
M = re.sub(r'(<p class="mt-2\.5 text-xs leading-6 font-medium text-slate-300">)[^<]*(</p>)', r'\1{ES_ADDRESS}\2', M)
M = M.replace('<span class="font-bold text-slate-300 tabular-nums">کدپستی: ۸۴۱۸۱۴۸۶۷۸</span>', '<span class="font-bold text-slate-300 tabular-nums">{ES_POSTAL}</span>')
M = M.replace('<a href="contact.html" class="inline-flex items-center gap-1.5 font-extrabold text-emerald-300 hover:text-white"><span>مسیریابی و ثبت درخواست بازدید</span>', '<a href="{ES_DIR_URL}" class="inline-flex items-center gap-1.5 font-extrabold text-emerald-300 hover:text-white"><span>{ES_DIR_TEXT}</span>')
M = M.replace('<a href="contact.html" class="group flex items-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3 shadow-md transition-transform hover:-translate-y-0.5">', '<a href="{ES_PIN_URL}" class="group flex items-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3 shadow-md transition-transform hover:-translate-y-0.5">')
M = M.replace('>کارخانه دانش\u200cبنیان عرفان صنعت اصفهان (ESE)</div>', '>{ES_PIN_TITLE}</div>')
M = M.replace('>اصفهان · شهرک صنعتی جی · مشاهده جزئیات تماس</div>', '>{ES_PIN_SUB}</div>')

need = ['{ES_KICKER}', '{ES_TITLE}', '{ES_ADDRESS}', '{ES_POSTAL}', '{ES_DIR_URL}', '{ES_DIR_TEXT}', '{ES_PIN_URL}', '{ES_PIN_TITLE}', '{ES_PIN_SUB}']
assert all(t in M for t in need), [t for t in need if t not in M]

# بستر نقشه: svg تزئینی ↔ iframe ادمین
SVG_MAP = re.search(r'<svg viewBox="0 0 1000 400".*?</svg>', M, re.S)
assert SVG_MAP
SVG_MAP = SVG_MAP.group(0)
M = M.replace(SVG_MAP, '{ES_MAP_BG}')

open(os.path.join(ROOT, 'template-parts/sections/map.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن map — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-map.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-map.php', get_defined_vars());
""")

map_php = f"""<?php
/**
 * سکشن نقشه کارخانه (map) — برش بایت‌دقیق میرور + فیلدهای SectionManager
 * بستر نقشه: بدون embed → SVG تزئینی میرور؛ با embed → iframe فقط از فیلد ادمین (هرگز ورودی کاربر).
 * فرگمنت با temp/gen_cm5.py از refs/map.html برش خورده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\\Theme\\Admin\\OptionsOutput;

/** @var array {{section_id: string, fields: array<string, mixed>, post_id: int}} $args */

$es_f = $args['fields'] ?? [];

/** فیلتر es_map_args — توسعه‌پذیری سکشن نقشه */
$es_args = apply_filters('es_map_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'map')]);
if (is_array($es_args)) {{
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/map.html
 */
{nowdoc('ES_MAP_SVG', SVG_MAP, 'ESM10')}
{nowdoc('ES_MAP', M, 'ESM10')}

$es_c = OptionsOutput::contact_args();
$es_address = trim((string) ($es_f['address'] ?? ''));
$es_address = $es_address !== '' ? $es_address : (string) $es_c['address'];
$es_postal = trim((string) ($es_f['postal'] ?? ''));
$es_postal = $es_postal !== '' ? $es_postal : 'کدپستی: ' . (string) $es_c['postal_code'];

// مخفی‌سازی: بدون نشانی و عنوان
if ($es_address === '' && (string) ($es_f['title'] ?? '') === '') {{
    return;
}}

// بستر نقشه: iframe ادمین با wp_kses گسترده (iframe مجاز فقط از آپشن)؛ وگرنه SVG میرور
$es_embed = trim((string) ($es_f['embed'] ?? ''));
if ($es_embed !== '') {{
    $es_bg = wp_kses($es_embed, [
        'iframe' => ['src' => true, 'width' => true, 'height' => true, 'frameborder' => true, 'style' => true, 'allow' => true, 'allowfullscreen' => true, 'loading' => true, 'title' => true, 'referrerpolicy' => true],
    ]);
}} else {{
    $es_bg = $ES_MAP_SVG;
}}

echo strtr($ES_MAP, [
    '{{ES_MAP_BG}}'   => $es_bg,
    '{{ES_KICKER}}'   => (string) ($es_f['kicker'] ?? ''),
    '{{ES_TITLE}}'    => (string) ($es_f['title'] ?? ''),
    '{{ES_ADDRESS}}'  => $es_address,
    '{{ES_POSTAL}}'   => $es_postal,
    '{{ES_DIR_URL}}'  => es_link_url((string) ($es_f['directions_url'] ?? '')),
    '{{ES_DIR_TEXT}}' => (string) ($es_f['directions_text'] ?? ''),
    '{{ES_PIN_URL}}'  => es_link_url((string) ($es_f['pin_url'] ?? '')),
    '{{ES_PIN_TITLE}}' => (string) ($es_f['pin_title'] ?? ''),
    '{{ES_PIN_SUB}}'  => (string) ($es_f['pin_sub'] ?? ''),
]);
"""
open(os.path.join(ROOT, 'template-parts/home/section-map.php'), 'w', encoding='utf-8').write(map_php)
print('map done:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-map.php')))
