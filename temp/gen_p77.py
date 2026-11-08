#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۷.۷ — قالب برگه‌های about و contact با SectionManager

برش بایت‌دقیق میرور (about.html / contact.html) و تولید:
  - page-templates/page-about.php + page-templates/page-contact.php
  - inc/Sections/AboutSections.php + inc/Sections/ContactSections.php
  - template-parts/about/section-{hero,intro,iso,values}.php
  - template-parts/contact/section-{hero,form}.php
  - template-parts/sections/{about-hero,about-intro,about-iso,about-values,contact-hero,contact-form}.php (رپر)
  - temp/p77_expect_about.html + temp/p77_expect_contact.html (خروجی موردانتظار تست)
پچ‌ها:
  - inc/Admin/SectionManager.php (گروه قالب → سکشن: TEMPLATE_GROUPS/group_for_post/get_sections/render_metabox)
  - inc/Sections/HomeSections.php (استخراج contact_schema مشترک)
  - inc/Theme.php (بوت AboutSections/ContactSections)
  - temp/harness/wp-stubs.php (stub get_page_template_slug)
  - inc/Nav/MobileWalker.php (رفع نشتی <?php داخل nowdoc — توکن {ES_LOGO_HTML})

اجرا: python3 temp/gen_p77.py
"""
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

# ── میرورها ──────────────────────────────────────────────────────────────
ABOUT_HTML = open(os.path.join(ROOT, 'uploads/static-mirror/about.html'), encoding='utf-8').read()
CONTACT_HTML = open(os.path.join(ROOT, 'uploads/static-mirror/contact.html'), encoding='utf-8').read()


def main_inner(doc: str) -> str:
    m0 = doc.index('<main>') + len('<main>')
    return doc[m0:doc.index('</main>', m0)]


AB_MAIN = main_inner(ABOUT_HTML)
CT_MAIN = main_inner(CONTACT_HTML)


def section_positions(seg: str) -> list:
    out, off = [], 0
    while True:
        p = seg.find('<section', off)
        if p < 0:
            break
        out.append(p)
        off = p + 8
    return out


AB_SP = section_positions(AB_MAIN)
CT_SP = section_positions(CT_MAIN)
assert len(AB_SP) == 5, f'about sections: {len(AB_SP)}'
assert len(CT_SP) == 2, f'contact sections: {len(CT_SP)}'

FR_HERO = AB_MAIN[:AB_SP[1]]
FR_INTRO = AB_MAIN[AB_SP[1]:AB_SP[2]]
FR_ISO = AB_MAIN[AB_SP[2]:AB_SP[3]]
FR_VALUES = AB_MAIN[AB_SP[3]:AB_SP[4]]
FR_CTA = AB_MAIN[AB_SP[4]:]
FR_CH = CT_MAIN[:CT_SP[1]]
FR_CF = CT_MAIN[CT_SP[1]:]


class Tok:
    """توکن‌گذار فرگمنت: جایگزینی بر اساس موقعیت ران‌های متنی + لنگرهای یکتا"""

    def __init__(self, frag: str):
        self.orig = frag
        self.runs = [(m.start(1), m.group(1)) for m in re.finditer(r'>([^<>]+)<', frag) if m.group(1).strip()]
        self.pos_ops = []
        self.str_ops = []

    def run(self, idx: int, token: str) -> 'Tok':
        pos, raw = self.runs[idx]
        self.pos_ops.append((pos, len(raw), token, raw))
        return self

    def attr(self, old: str, new: str) -> 'Tok':
        n = self.orig.count(old)
        assert n == 1, f'attr anchor x{n}: {old[:70]!r}'
        self.str_ops.append((old, new))
        return self

    def default(self, idx: int) -> str:
        return self.runs[idx][1]

    def result(self) -> str:
        out = self.orig
        for pos, ln, token, raw in sorted(self.pos_ops, key=lambda x: -x[0]):
            assert out[pos:pos + ln] == raw, f'pos mismatch @ {pos}'
            out = out[:pos] + token + out[pos + ln:]
        for old, new in self.str_ops:
            n = out.count(old)
            assert n == 1, f'str anchor x{n}: {old[:70]!r}'
            out = out.replace(old, new)
        return out


# ── توکن‌گذاری سکشن‌ها (جدول‌محور؛ توکن = {ES_<PREFIX>_<KEY>} یکتا) ─────
PREFIX = {'page-about-hero': 'AH', 'page-about-intro': 'AI', 'page-about-iso': 'AO',
          'page-about-values': 'AV', 'contact-hero': 'TH', 'contact-form': 'TF'}

# (slug, [(key, run_idx)], [(key, old_attr, new_attr_core)])
TOKEN_PLAN = {
    'page-about-hero': (
        [('crumb', 2), ('badge', 3), ('h1_pre', 4), ('h1_hl', 5), ('quote', 6), ('brand', 7), ('since', 8),
         ('c1_v', 9), ('c1_l', 10), ('c2_v', 11), ('c2_l', 12),
         ('st1_v', 13), ('st1_l', 14), ('st2_v', 15), ('st2_l', 16), ('st3_v', 17), ('st3_l', 18),
         ('st4_v', 19), ('st4_l', 20)],
        [('home_url', 'href="index.html"', 'href="{%TOKEN%}"')],
    ),
    'page-about-intro': (
        [('fcard_v', 0), ('fcard_l', 1), ('badge', 2), ('h2_pre', 3), ('h2_hl', 4), ('lead', 5),
         ('p1', 6), ('p2', 7),
         ('st1_v', 8), ('st1_l', 9), ('st2_v', 10), ('st2_l', 11), ('st3_v', 12), ('st3_l', 13),
         ('st4_v', 14), ('st4_l', 15)],
        [],
    ),
    'page-about-iso': (
        [('kicker', 0), ('h2_pre', 1), ('h2_hl', 2), ('text', 3), ('b1', 4), ('b2', 5), ('b3', 6),
         ('card_v', 7), ('card_l', 8), ('card_en', 9), ('card_badge', 10)],
        [],
    ),
    'page-about-values': (
        [('h2_pre', 0), ('h2_hl', 1), ('c1_t', 2), ('c1_d', 3), ('c2_t', 4), ('c2_d', 5),
         ('c3_t', 6), ('c3_d', 7), ('c4_t', 8), ('c4_d', 9), ('map_t', 10), ('map_d', 11), ('cta', 12)],
        [('cta_url', 'href="projects.html"', 'href="{%TOKEN%}"')],
    ),
    'contact-hero': (
        [('crumb', 2), ('kicker', 3), ('h1_pre', 4), ('h1_hl', 5), ('desc', 6), ('chip1', 7), ('chip2', 8),
         ('brand', 9), ('since', 10), ('l1', 11), ('l2', 12), ('l3', 13), ('l4', 14),
         ('st1_v', 15), ('st1_l', 16), ('st2_v', 17), ('st2_l', 18), ('st3_v', 19), ('st3_l', 20),
         ('st4_v', 21), ('st4_l', 22)],
        [('home_url', 'href="index.html"', 'href="{%TOKEN%}"')],
    ),
    'contact-form': (
        [('ic1_l', 0), ('ic1_v', 1), ('ic1_s', 2), ('ic2_l', 3), ('ic2_v', 4), ('ic2_s', 5),
         ('ic3_l', 6), ('ic3_v', 7), ('ic3_s', 8), ('ic4_l', 9), ('ic4_v', 10), ('ic4_s', 11),
         ('form_title', 12), ('form_desc', 13), ('ok_t', 14), ('ok_d', 15), ('ok_btn', 16),
         ('l_name', 17), ('l_tel', 18), ('l_org', 19), ('l_msg', 20), ('submit', 21),
         ('mgr_kicker', 22), ('mgr_name', 23), ('mgr_role', 24), ('mgr_phone', 25),
         ('mgr_b1', 26), ('mgr_b2', 27), ('addr', 28), ('postal', 29),
         ('faq_q', 30), ('faq_a1', 31), ('faq_hl', 32), ('faq_a2', 33)],
        [('ph_name', 'placeholder="نام شما"', 'placeholder="{%TOKEN%}"'),
         ('ph_tel', 'placeholder="۰۹۱۲ ..."', 'placeholder="{%TOKEN%}"'),
         ('ph_org', 'placeholder="نام سازمان یا شرکت (اختیاری)"', 'placeholder="{%TOKEN%}"'),
         ('ph_msg', 'placeholder="نوع پروژه (میدان، خیابان، تونل نوری و...)، محل اجرا و توضیحات لازم"', 'placeholder="{%TOKEN%}"'),
         ('form_extra', '<form data-contact-form="true" class="flex mt-8 flex-col gap-5">',
          '<form data-contact-form="true" class="flex mt-8 flex-col gap-5"{%TOKEN%}>'),
         ('ic1_url', 'href="tel:031-91091011" class="text-[15.5px]', 'href="{%TOKEN%}" class="text-[15.5px]'),
         ('ic2_url', 'href="mailto:info@erfansanat.com"', 'href="{%TOKEN%}"'),
         ('mgr_phone_url', 'href="tel:09137976915" class="mt-1', 'href="{%TOKEN%}" class="mt-1'),
         ('map_img', 'src="images/factory.jpg"', 'src="{%TOKEN%}"'),
         ('map_img_alt', 'alt="موقعیت کارخانه عرفان صنعت"', 'alt="{%TOKEN%}"')],
    ),
}


def tok_for(slug: str, key: str) -> str:
    return '{ES_' + PREFIX[slug] + '_' + key.upper() + '}'


TOK = {}   # slug → Tok
for slug, (run_map, attr_map) in TOKEN_PLAN.items():
    t = Tok(dict(_FRAGS_SRC)[slug] if False else globals()['FR_' + {'page-about-hero': 'HERO', 'page-about-intro': 'INTRO',
        'page-about-iso': 'ISO', 'page-about-values': 'VALUES', 'contact-hero': 'CH', 'contact-form': 'CF'}[slug]])
    for key, idx in run_map:
        t.run(idx, tok_for(slug, key))
    for key, old, new in attr_map:
        token = '{ES_HOME_URL}' if key == 'home_url' else tok_for(slug, key)
        t.attr(old, new.replace('{%TOKEN%}', token))
    TOK[slug] = t

HERO = TOK['page-about-hero'].result()
INTRO = TOK['page-about-intro'].result()
ISO = TOK['page-about-iso'].result()
VALUES = TOK['page-about-values'].result()
CH = TOK['contact-hero'].result()
CF = TOK['contact-form'].result()

# ── پیش‌فرض‌های اسکیم = مقادیر میرور (ران خام — با فاصله‌های اصلی) ───────
def defaults(slug, *idxs):
    return [TOK[slug].default(i) for i in idxs]


# فیلدها: (key, type, label, default)
SCHEMA = {
    'page-about-hero': [
        ('crumb', 'text', 'برچسب مسیر — صفحه فعلی', defaults('page-about-hero', 2)[0]),
        ('badge', 'text', 'بج بالای عنوان', defaults('page-about-hero', 3)[0]),
        ('h1_pre', 'text', 'عنوان — بخش اول', defaults('page-about-hero', 4)[0]),
        ('h1_hl', 'text', 'عنوان — بخش گرادیانی', defaults('page-about-hero', 5)[0]),
        ('quote', 'textarea', 'معرفی یک‌خطی (نقل‌قول)', defaults('page-about-hero', 6)[0]),
        ('brand', 'text', 'نام شرکت (کارت شناور پایین)', defaults('page-about-hero', 7)[0]),
        ('since', 'text', 'بج «از سال»', defaults('page-about-hero', 8)[0]),
        ('c1_v', 'text', 'کارت ۱ — مقدار', defaults('page-about-hero', 9)[0]),
        ('c1_l', 'text', 'کارت ۱ — برچسب', defaults('page-about-hero', 10)[0]),
        ('c2_v', 'text', 'کارت ۲ — مقدار', defaults('page-about-hero', 11)[0]),
        ('c2_l', 'text', 'کارت ۲ — برچسب', defaults('page-about-hero', 12)[0]),
        ('st1_v', 'text', 'آمار ۱ — عدد', defaults('page-about-hero', 13)[0]),
        ('st1_l', 'text', 'آمار ۱ — برچسب', defaults('page-about-hero', 14)[0]),
        ('st2_v', 'text', 'آمار ۲ — عدد', defaults('page-about-hero', 15)[0]),
        ('st2_l', 'text', 'آمار ۲ — برچسب', defaults('page-about-hero', 16)[0]),
        ('st3_v', 'text', 'آمار ۳ — عدد', defaults('page-about-hero', 17)[0]),
        ('st3_l', 'text', 'آمار ۳ — برچسب', defaults('page-about-hero', 18)[0]),
        ('st4_v', 'text', 'آمار ۴ — عدد', defaults('page-about-hero', 19)[0]),
        ('st4_l', 'text', 'آمار ۴ — برچسب', defaults('page-about-hero', 20)[0]),
    ],
    'page-about-intro': [
        ('fcard_v', 'text', 'کارت شناور — مقدار', defaults('page-about-intro', 0)[0]),
        ('fcard_l', 'text', 'کارت شناور — برچسب', defaults('page-about-intro', 1)[0]),
        ('badge', 'text', 'بج «شرکت دانش‌بنیان»', defaults('page-about-intro', 2)[0]),
        ('h2_pre', 'text', 'عنوان — بخش اول', defaults('page-about-intro', 3)[0]),
        ('h2_hl', 'text', 'عنوان — بخش سبز', defaults('page-about-intro', 4)[0]),
        ('lead', 'text', 'سرخط معرفی', defaults('page-about-intro', 5)[0]),
        ('p1', 'textarea', 'پاراگراف اول', defaults('page-about-intro', 6)[0]),
        ('p2', 'textarea', 'پاراگراف دوم', defaults('page-about-intro', 7)[0]),
        ('st1_v', 'text', 'آمار ۱ — عدد', defaults('page-about-intro', 8)[0]),
        ('st1_l', 'text', 'آمار ۱ — برچسب', defaults('page-about-intro', 9)[0]),
        ('st2_v', 'text', 'آمار ۲ — عدد', defaults('page-about-intro', 10)[0]),
        ('st2_l', 'text', 'آمار ۲ — برچسب', defaults('page-about-intro', 11)[0]),
        ('st3_v', 'text', 'آمار ۳ — عدد', defaults('page-about-intro', 12)[0]),
        ('st3_l', 'text', 'آمار ۳ — برچسب', defaults('page-about-intro', 13)[0]),
        ('st4_v', 'text', 'آمار ۴ — عدد', defaults('page-about-intro', 14)[0]),
        ('st4_l', 'text', 'آمار ۴ — برچسب', defaults('page-about-intro', 15)[0]),
    ],
    'page-about-iso': [
        ('kicker', 'text', 'بالانویس', defaults('page-about-iso', 0)[0]),
        ('h2_pre', 'text', 'عنوان — بخش اول', defaults('page-about-iso', 1)[0]),
        ('h2_hl', 'text', 'عنوان — بخش سبز', defaults('page-about-iso', 2)[0]),
        ('text', 'textarea', 'متن توضیحی', defaults('page-about-iso', 3)[0]),
        ('b1', 'text', 'ویژگی ۱', defaults('page-about-iso', 4)[0]),
        ('b2', 'text', 'ویژگی ۲', defaults('page-about-iso', 5)[0]),
        ('b3', 'text', 'ویژگی ۳', defaults('page-about-iso', 6)[0]),
        ('card_v', 'text', 'کارت ISO — مقدار', defaults('page-about-iso', 7)[0]),
        ('card_l', 'text', 'کارت ISO — برچسب', defaults('page-about-iso', 8)[0]),
        ('card_en', 'text', 'کارت ISO — عنوان انگلیسی', defaults('page-about-iso', 9)[0]),
        ('card_badge', 'text', 'کارت ISO — بج پایین', defaults('page-about-iso', 10)[0]),
    ],
    'page-about-values': [
        ('h2_pre', 'text', 'عنوان — بخش اول', defaults('page-about-values', 0)[0]),
        ('h2_hl', 'text', 'عنوان — بخش سبز', defaults('page-about-values', 1)[0]),
        ('c1_t', 'text', 'ارزش ۱ — عنوان', defaults('page-about-values', 2)[0]),
        ('c1_d', 'text', 'ارزش ۱ — توضیح', defaults('page-about-values', 3)[0]),
        ('c2_t', 'text', 'ارزش ۲ — عنوان', defaults('page-about-values', 4)[0]),
        ('c2_d', 'text', 'ارزش ۲ — توضیح', defaults('page-about-values', 5)[0]),
        ('c3_t', 'text', 'ارزش ۳ — عنوان', defaults('page-about-values', 6)[0]),
        ('c3_d', 'text', 'ارزش ۳ — توضیح', defaults('page-about-values', 7)[0]),
        ('c4_t', 'text', 'ارزش ۴ — عنوان', defaults('page-about-values', 8)[0]),
        ('c4_d', 'text', 'ارزش ۴ — توضیح', defaults('page-about-values', 9)[0]),
        ('map_t', 'text', 'نقشه ایران — عنوان', defaults('page-about-values', 10)[0]),
        ('map_d', 'textarea', 'نقشه ایران — توضیح', defaults('page-about-values', 11)[0]),
        ('cta', 'text', 'متن دکمه', defaults('page-about-values', 12)[0]),
        ('cta_url', 'url', 'لینک دکمه', '/projects/'),
    ],
    'contact-hero': [
        ('crumb', 'text', 'برچسب مسیر — صفحه فعلی', defaults('contact-hero', 2)[0]),
        ('kicker', 'text', 'بالانویس', defaults('contact-hero', 3)[0]),
        ('h1_pre', 'text', 'عنوان — بخش اول', defaults('contact-hero', 4)[0]),
        ('h1_hl', 'text', 'عنوان — بخش سبز', defaults('contact-hero', 5)[0]),
        ('desc', 'textarea', 'متن معرفی', defaults('contact-hero', 6)[0]),
        ('chip1', 'text', 'چیپ تلفن', defaults('contact-hero', 7)[0]),
        ('chip2', 'text', 'چیپ همراه فروش', defaults('contact-hero', 8)[0]),
        ('brand', 'text', 'نام شرکت', defaults('contact-hero', 9)[0]),
        ('since', 'text', 'بج «از سال»', defaults('contact-hero', 10)[0]),
        ('l1', 'text', 'ردیف ۱ — برچسب', defaults('contact-hero', 11)[0]),
        ('l2', 'text', 'ردیف ۱ — مقدار', defaults('contact-hero', 12)[0]),
        ('l3', 'text', 'ردیف ۲ — برچسب', defaults('contact-hero', 13)[0]),
        ('l4', 'text', 'ردیف ۲ — مقدار', defaults('contact-hero', 14)[0]),
        ('st1_v', 'text', 'آمار ۱ — عدد', defaults('contact-hero', 15)[0]),
        ('st1_l', 'text', 'آمار ۱ — برچسب', defaults('contact-hero', 16)[0]),
        ('st2_v', 'text', 'آمار ۲ — عدد', defaults('contact-hero', 17)[0]),
        ('st2_l', 'text', 'آمار ۲ — برچسب', defaults('contact-hero', 18)[0]),
        ('st3_v', 'text', 'آمار ۳ — عدد', defaults('contact-hero', 19)[0]),
        ('st3_l', 'text', 'آمار ۳ — برچسب', defaults('contact-hero', 20)[0]),
        ('st4_v', 'text', 'آمار ۴ — عدد', defaults('contact-hero', 21)[0]),
        ('st4_l', 'text', 'آمار ۴ — برچسب', defaults('contact-hero', 22)[0]),
    ],
    'contact-form': [
        ('ic1_l', 'text', 'کارت تلفن — برچسب', defaults('contact-form', 0)[0]),
        ('ic1_v', 'text', 'کارت تلفن — مقدار', defaults('contact-form', 1)[0]),
        ('ic1_s', 'text', 'کارت تلفن — زیرنویس', defaults('contact-form', 2)[0]),
        ('ic1_url', 'url', 'کارت تلفن — لینک', 'tel:031-91091011'),
        ('ic2_l', 'text', 'کارت ایمیل — برچسب', defaults('contact-form', 3)[0]),
        ('ic2_v', 'text', 'کارت ایمیل — مقدار', defaults('contact-form', 4)[0]),
        ('ic2_s', 'text', 'کارت ایمیل — زیرنویس', defaults('contact-form', 5)[0]),
        ('ic2_url', 'url', 'کارت ایمیل — لینک', 'mailto:info@erfansanat.com'),
        ('ic3_l', 'text', 'کارت ساعات — برچسب', defaults('contact-form', 6)[0]),
        ('ic3_v', 'text', 'کارت ساعات — مقدار', defaults('contact-form', 7)[0]),
        ('ic3_s', 'text', 'کارت ساعات — زیرنویس', defaults('contact-form', 8)[0]),
        ('ic4_l', 'text', 'کارت آدرس — برچسب', defaults('contact-form', 9)[0]),
        ('ic4_v', 'text', 'کارت آدرس — مقدار', defaults('contact-form', 10)[0]),
        ('ic4_s', 'text', 'کارت آدرس — زیرنویس', defaults('contact-form', 11)[0]),
        ('form_title', 'text', 'عنوان فرم', defaults('contact-form', 12)[0]),
        ('form_desc', 'textarea', 'توضیح فرم', defaults('contact-form', 13)[0]),
        ('ok_t', 'text', 'پیام موفقیت — عنوان', defaults('contact-form', 14)[0]),
        ('ok_d', 'text', 'پیام موفقیت — متن', defaults('contact-form', 15)[0]),
        ('ok_btn', 'text', 'پیام موفقیت — دکمه بازنشانی', defaults('contact-form', 16)[0]),
        ('l_name', 'text', 'برچسب نام و نام خانوادگی', defaults('contact-form', 17)[0]),
        ('ph_name', 'text', 'جانگهدار نام', 'نام شما'),
        ('l_tel', 'text', 'برچسب شماره تماس', defaults('contact-form', 18)[0]),
        ('ph_tel', 'text', 'جانگهدار شماره تماس', '۰۹۱۲ ...'),
        ('l_org', 'text', 'برچسب سازمان', defaults('contact-form', 19)[0]),
        ('ph_org', 'text', 'جانگهدار سازمان', 'نام سازمان یا شرکت (اختیاری)'),
        ('l_msg', 'text', 'برچسب توضیحات', defaults('contact-form', 20)[0]),
        ('ph_msg', 'text', 'جانگهدار توضیحات', 'نوع پروژه (میدان، خیابان، تونل نوری و...)، محل اجرا و توضیحات لازم'),
        ('submit', 'text', 'متن دکمه ارسال', defaults('contact-form', 21)[0]),
        ('mgr_kicker', 'text', 'کارت کارشناس — بالانویس', defaults('contact-form', 22)[0]),
        ('mgr_name', 'text', 'کارت کارشناس — نام', defaults('contact-form', 23)[0]),
        ('mgr_role', 'text', 'کارت کارشناس — سمت', defaults('contact-form', 24)[0]),
        ('mgr_phone', 'text', 'کارت کارشناس — شماره', defaults('contact-form', 25)[0]),
        ('mgr_phone_url', 'url', 'کارت کارشناس — لینک شماره', 'tel:09137976915'),
        ('mgr_b1', 'text', 'دکمه تماس با دفتر', defaults('contact-form', 26)[0]),
        ('mgr_b2', 'text', 'دکمه تماس همراه', defaults('contact-form', 27)[0]),
        ('map_img', 'media', 'تصویر موقعیت کارخانه', 'assets/images/factory.jpg'),
        ('map_img_alt', 'text', 'alt تصویر موقعیت', 'موقعیت کارخانه عرفان صنعت'),
        ('addr', 'textarea', 'نشانی روی تصویر', defaults('contact-form', 28)[0]),
        ('postal', 'text', 'کدپستی', defaults('contact-form', 29)[0]),
        ('faq_q', 'text', 'سوال متداول — پرسش', defaults('contact-form', 30)[0]),
        ('faq_a1', 'textarea', 'سوال متداول — پاسخ (اول)', defaults('contact-form', 31)[0]),
        ('faq_hl', 'text', 'سوال متداول — بخش پررنگ پاسخ', defaults('contact-form', 32)[0]),
        ('faq_a2', 'textarea', 'سوال متداول — پاسخ (دوم)', defaults('contact-form', 33)[0]),
    ],
}

# ── assert بازسازی: توکن → پیش‌فرض باید تکه اصلی را بدهد ─────────────────
TOKEN_DEFAULT = {}
TOKEN_EXPECT = {}
HOME_T = 'https://test.example/'
THEME_T = 'https://test.example/wp-content/themes/erfansanat/'

for sec_key, fields in SCHEMA.items():
    for key, ftype, label, default in fields:
        token = tok_for(sec_key, key)
        assert token not in TOKEN_DEFAULT, f'dup token {token}'
        TOKEN_DEFAULT[token] = default
        if ftype == 'url':
            exp = HOME_T + default.lstrip('/') if default.startswith('/') else default
        elif ftype == 'media':
            exp = THEME_T + default
        else:
            exp = default
        TOKEN_EXPECT[token] = exp

TOKEN_EXPECT['{ES_HOME_URL}'] = HOME_T
TOKEN_EXPECT[tok_for('contact-form', 'form_extra')] = ''

FR_TOK = {
    'page-about-hero': HERO, 'page-about-intro': INTRO, 'page-about-iso': ISO, 'page-about-values': VALUES,
    'contact-hero': CH, 'contact-form': CF,
}
FR_ORIG = {
    'page-about-hero': FR_HERO, 'page-about-intro': FR_INTRO, 'page-about-iso': FR_ISO, 'page-about-values': FR_VALUES,
    'contact-hero': FR_CH, 'contact-form': FR_CF,
}
# نگاشت بازسازی: توکن → مقدار اصلی میرور (برای توکن‌های خاص با پیش‌فرض متفاوت)
REBUILD_MAP = dict(TOKEN_DEFAULT)
REBUILD_MAP['{ES_HOME_URL}'] = 'index.html'
REBUILD_MAP[tok_for('page-about-values', 'cta_url')] = 'projects.html'
REBUILD_MAP[tok_for('contact-form', 'form_extra')] = ''
REBUILD_MAP[tok_for('contact-form', 'map_img')] = 'images/factory.jpg'

for k, tok in FR_TOK.items():
    rebuilt = tok
    used = 0
    for t, d in REBUILD_MAP.items():
        if t in rebuilt:
            rebuilt = rebuilt.replace(t, d)
            used += 1
    assert rebuilt == FR_ORIG[k], f'rebuild failed: {k} (tokens used: {used})'
    print(f'  rebuild OK: {k} ({len(tok)} → {len(FR_ORIG[k])} chars, {used} tokens)')

# ── تولید فایل‌های سکشن ──────────────────────────────────────────────────
SEC_FILES = [
    ('page-about-hero', 'template-parts/about/section-hero.php', 'سکشن هیرو درباره ما', 'about.html',
     'ESABH7', HERO, 'template-parts/sections/page-about-hero.php',
     'هیرو قالب «درباره ما» — کرامب، عنوان، کارت‌های شناور و نوار آمار'),
    ('page-about-intro', 'template-parts/about/section-intro.php', 'سکشن معرفی درباره ما', 'about.html',
     'ESABI7', INTRO, 'template-parts/sections/page-about-intro.php',
     'معرفی کارخانه — کارت شناور، بج، دو پاراگراف و نوار آمار'),
    ('page-about-iso', 'template-parts/about/section-iso.php', 'سکشن گواهینامه ISO', 'about.html',
     'ESABS7', ISO, 'template-parts/sections/page-about-iso.php',
     'گواهینامه ISO 9001:2015 — فهرست ویژگی‌ها و کارت گواهینامه'),
    ('page-about-values', 'template-parts/about/section-values.php', 'سکشن ارزش‌ها و نقشه ایران', 'about.html',
     'ESABV7', VALUES, 'template-parts/sections/page-about-values.php',
     'اصول بنیادین — چهار کارت ارزش، نقشه ایران و دکمه پروژه‌ها'),
    ('contact-hero', 'template-parts/contact/section-hero.php', 'سکشن هیرو تماس با ما', 'contact.html',
     'ESCTH7', CH, 'template-parts/sections/contact-hero.php',
     'هیرو قالب «تماس با ما» — کرامب، عنوان، چیپ‌های تماس و کارت آمار'),
    ('contact-form', 'template-parts/contact/section-form.php', 'سکشن اطلاعات تماس و فرم', 'contact.html',
     'ESCTF7', CF, 'template-parts/sections/contact-form.php',
     'کارت‌های اطلاعات، فرم مشاوره (data-contact-form)، کارت کارشناس، موقعیت و سوال متداول'),
]

SPECIAL_EXPR = {
    '{ES_HOME_URL}': "esc_url(home_url('/'))",
    tok_for('contact-form', 'form_extra'): "(string) apply_filters('es_contact_form_extra', '', $es_f)",
}

TYPE_EXPR = {
    'text': "(string) ($es_f['{K}'] ?? '')",
    'textarea': "(string) ($es_f['{K}'] ?? '')",
    'url': "es_link_url((string) ($es_f['{K}'] ?? ''))",
    'media': "es_media_url((string) ($es_f['{K}'] ?? ''))",
}


def emit_section(slug, out_path, title, mirror, heredoc, frag, wrapper_path, desc):
    assert heredoc not in frag, f'heredoc id collision: {heredoc}'
    keys_in_frag = []
    for t in re.findall(r'\{ES_[A-Z0-9_]+\}', frag):
        if t not in keys_in_frag:
            keys_in_frag.append(t)
    unknown = [t for t in keys_in_frag if t not in SPECIAL_EXPR and t not in TOKEN_DEFAULT]
    assert unknown == [], f'unknown tokens in {slug}: {unknown}'
    pad = max(len(t) for t in keys_in_frag)
    lines = []
    for t in keys_in_frag:
        expr = SPECIAL_EXPR.get(t)
        if expr is None:
            pre = PREFIX[slug] + '_'
            assert t.startswith('{ES_' + pre), f'token {t} prefix mismatch for {slug}'
            key = t[4 + len(pre):-1].lower()
            ftype = next(f[1] for f in SCHEMA[slug] if f[0] == key)
            expr = TYPE_EXPR[ftype].replace('{K}', key)
        lines.append(f"    '{t}'" + ' ' * (pad - len(t)) + f' => {expr},')
    var = '$' + heredoc.lower().replace('7', '_7')
    php = (
        "<?php\n"
        "/**\n"
        f" * {title} — برش بایت‌دقیق میرور {mirror} (تسک ۷.۷)\n"
        f" * {desc}\n"
        " * رندر: SectionManager → رپر template-parts/sections/ → این فایل\n"
        " *\n"
        " * @package ErfanSanat\n"
        " */\n"
        "declare(strict_types=1);\n\n"
        "/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */\n\n"
        "$es_f = $args['fields'] ?? [];\n\n"
        f"{var} = <<<'{heredoc}'\n"
        f"{frag}\n"
        f"{heredoc};\n\n"
        f"echo strtr({var}, [\n"
        + "\n".join(lines) +
        "\n]);\n"
    )
    path = os.path.join(ROOT, out_path)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    open(path, 'w', encoding='utf-8').write(php)
    wrap = (
        "<?php\n"
        "/**\n"
        f" * سکشن {slug} — مسیر رندر SectionManager؛ مارک‌آپ در {out_path}\n"
        " *\n"
        " * @package ErfanSanat\n"
        " */\n"
        "declare(strict_types=1);\n\n"
        f"es_get_template('{out_path}', get_defined_vars());\n"
    )
    wpath = os.path.join(ROOT, wrapper_path)
    os.makedirs(os.path.dirname(wpath), exist_ok=True)
    open(wpath, 'w', encoding='utf-8').write(wrap)
    print(f'  wrote {out_path} + {wrapper_path}')


print('== تولید سکشن‌ها ==')
for row in SEC_FILES:
    emit_section(*row)


# ── تولید کلاس‌های Sections ──────────────────────────────────────────────
def schema_php_rows(slug):
    lines = []
    for key, ftype, label, default in SCHEMA[slug]:
        d = default.replace('\\', '\\\\').replace("'", "\\'")
        lines.append(f"                ['key' => '{key}', 'type' => '{ftype}', 'label' => __('{label}', 'erfansanat'), 'default' => '{d}'],")
    return "\n".join(lines)


ABOUT_SECTIONS_META = [
    ('page-about-hero', 'هیرو درباره ما (کرامب، معرفی، کارت‌های شناور، آمار)'),
    ('page-about-intro', 'معرفی کارخانه و نوار آمار'),
    ('page-about-iso', 'گواهینامه ISO 9001:2015'),
    ('page-about-values', 'اصول بنیادین و نقشه ایران'),
    ('contact', 'تماس با ما (دعوت به مشاوره — مشترک با صفحه اصلی)'),
]
CONTACT_SECTIONS_META = [
    ('contact-hero', 'هیرو تماس با ما (کرامب، چیپ‌های تماس، آمار)'),
    ('contact-form', 'اطلاعات تماس، فرم مشاوره، کارت کارشناس و سوال متداول'),
]


def emit_sections_class(cls_name, group, meta, mirror):
    items = []
    for sid, label in meta:
        if sid == 'contact':
            items.append(
                "        $items['contact'] = [\n"
                "            'label'  => __('" + label + "', 'erfansanat'),\n"
                "            'schema' => \\ErfanSanat\\Theme\\Sections\\HomeSections::contact_schema(),\n"
                "        ];\n"
            )
        else:
            items.append(
                f"        $items['{sid}'] = [\n"
                f"            'label'  => __('{label}', 'erfansanat'),\n"
                "            'schema' => [\n"
                + schema_php_rows(sid) + "\n"
                "            ],\n"
                "        ];\n"
            )
    order = ",\n            ".join(f"'{sid}'" for sid, _ in meta)
    fa_title = 'درباره ما' if group == 'about' else 'تماس با ما'
    php = (
        "<?php\n"
        "/**\n"
        f" * ثبت سکشن‌های قالب برگه «{fa_title}» در SectionManager — ترتیب عین میرور\n"
        f" * ref: uploads/static-mirror/{mirror} (تسک ۷.۷)\n"
        " * حالت متای خالی = رندر کامل میرور (پیش‌فرض‌های اسکیم = مقادیر میرور)\n"
        " *\n"
        " * @package ErfanSanat\n"
        " */\n"
        "declare(strict_types=1);\n\n"
        "namespace ErfanSanat\\Theme\\Sections;\n\n"
        f"final class {cls_name} {{\n\n"
        "    /** ترتیب سکشن‌ها — عین ترتیب <section>های میرور */\n"
        "    public const ORDER = [\n"
        "            " + order + ",\n"
        "    ];\n\n"
        "    public static function init(): void {\n"
        "        add_filter('es_sections_registry', [self::class, 'register']);\n"
        "    }\n\n"
        "    /**\n"
        "     * ثبت سکشن‌ها با اسکیم فیلدها (مقادیر پیش‌فرض = میرور)\n"
        "     *\n"
        "     * @param array<string,array> $registry\n"
        "     * @return array<string,array>\n"
        "     */\n"
        "    public static function register(array $registry): array {\n"
        f"        $registry['{group}'] ??= [];\n\n"
        "        // ── تعریف سکشن‌ها (به ترتیب میرور) ──\n"
        "        $items = [];\n"
        + "".join(items) +
        "        foreach ($items as $id => $def) {\n"
        f"            $registry['{group}'][] = [\n"
        "                'id'     => (string) $id,\n"
        "                'label'  => (string) $def['label'],\n"
        "                'schema' => is_array($def['schema'] ?? null) ? $def['schema'] : [],\n"
        "            ];\n"
        "        }\n"
        "        return $registry;\n"
        "    }\n"
        "}\n"
    )
    path = os.path.join(ROOT, f'inc/Sections/{cls_name}.php')
    open(path, 'w', encoding='utf-8').write(php)
    print(f'  wrote inc/Sections/{cls_name}.php')


print('== تولید کلاس‌های Sections ==')
emit_sections_class('AboutSections', 'about', ABOUT_SECTIONS_META, 'about.html')
emit_sections_class('ContactSections', 'contact', CONTACT_SECTIONS_META, 'contact.html')

# ── قالب‌های برگه ────────────────────────────────────────────────────────
PAGE_ABOUT = """<?php
/**
 * Template Name: درباره ما
 *
 * قالب برگه «درباره ما» — رندر سکشن‌های SectionManager (گروه about)
 * ترتیب/فعال‌بودن و فیلدها از متای _es_sections برگه؛ حالت خالی = عین میرور about.html
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

es_render_sections((int) get_queried_object_id());

get_footer();
"""

PAGE_CONTACT = """<?php
/**
 * Template Name: تماس با ما
 *
 * قالب برگه «تماس با ما» — رندر سکشن‌های SectionManager (گروه contact)
 * ترتیب/فعال‌بودن و فیلدها از متای _es_sections برگه؛ حالت خالی = عین میرور contact.html
 * فرم: data-contact-form (ارسال واقعی و nonce در فاز ۸.۵ — تا آن زمان فقط نمایشی)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

es_render_sections((int) get_queried_object_id());

get_footer();
"""

os.makedirs(os.path.join(ROOT, 'page-templates'), exist_ok=True)
open(os.path.join(ROOT, 'page-templates/page-about.php'), 'w', encoding='utf-8').write(PAGE_ABOUT)
open(os.path.join(ROOT, 'page-templates/page-contact.php'), 'w', encoding='utf-8').write(PAGE_CONTACT)
print('  wrote page-templates/page-about.php + page-contact.php')

# ── فایل‌های موردانتظار تست (توکن → مقدار محیط تست) ─────────────────────
def to_expected(frag):
    out = frag
    for t, v in TOKEN_EXPECT.items():
        out = out.replace(t, v)
    return out


EXP_ABOUT = (to_expected(HERO) + to_expected(INTRO) + to_expected(ISO) + to_expected(VALUES)
             + FR_CTA.replace('href="contact.html"', 'href="' + HOME_T + 'contact/"'))
EXP_CONTACT = to_expected(CH) + to_expected(CF)

open(os.path.join(ROOT, 'temp/p77_expect_about.html'), 'w', encoding='utf-8').write(EXP_ABOUT)
open(os.path.join(ROOT, 'temp/p77_expect_contact.html'), 'w', encoding='utf-8').write(EXP_CONTACT)
print(f'  wrote temp/p77_expect_about.html ({len(EXP_ABOUT)}) + temp/p77_expect_contact.html ({len(EXP_CONTACT)})')

# ── پچ SectionManager: گروه قالب → سکشن ─────────────────────────────────
SM = os.path.join(ROOT, 'inc/Admin/SectionManager.php')
sm = open(SM, encoding='utf-8').read()

anchor = "    private static int $sequence   = 0;\n"
assert sm.count(anchor) == 1, 'SM anchor1'
if 'TEMPLATE_GROUPS' not in sm:
    sm = sm.replace(anchor, anchor + """
    /** نگاشت slug قالب برگه → گروه سکشن (فاز ۷.۷ — about/contact) */
    private const TEMPLATE_GROUPS = [
        'page-templates/page-about.php'   => 'about',
        'page-templates/page-contact.php' => 'contact',
    ];
""", 1)

# متد group_for_post — بعد از schema_of
anchor2 = "    /** انواع پست دارای متاباکس سکشن */"
assert sm.count(anchor2) == 1, 'SM anchor2'
if 'function group_for_post' not in sm:
    sm = sm.replace(anchor2, """    /**
     * گروه سکشن‌های یک برگه بر اساس قالب انتخابی — پیش‌فرض: page
     *
     * @param int $post_id
     */
    public static function group_for_post(int $post_id): string {
        $slug  = function_exists('get_page_template_slug') ? (string) get_page_template_slug($post_id) : '';
        $map   = apply_filters('es_sections_template_groups', self::TEMPLATE_GROUPS);
        $group = is_array($map) ? (string) ($map[$slug] ?? '') : '';
        return $group !== '' ? $group : 'page';
    }

""" + anchor2, 1)

old_get = """            $out = [];
            $all = self::registered_flat();
            foreach ($all as $key => $info) {
                $out[] = ['id' => (string) substr((string) $key, (int) strpos((string) $key, '|') + 1), 'enabled' => 1, 'order' => count($out), 'fields' => []];
            }
            return $out;"""
new_get = """            $out = [];
            // پیش‌فرض: سکشن‌های گروه قالب همین برگه، فعال، به ترتیب ثبت (ظاهر عین میرور)
            foreach (self::sections_of(self::group_for_post($post_id)) as $id => $info) {
                $out[] = ['id' => (string) $id, 'enabled' => 1, 'order' => count($out), 'fields' => []];
            }
            return $out;"""
if 'sections_of(self::group_for_post($post_id))' not in sm:
    assert sm.count(old_get) == 1, 'SM anchor3'
    sm = sm.replace(old_get, new_get, 1)

old_mb = """        $saved    = self::get_sections((int) $post->ID);
        $registry = self::registered_flat();

        // نگاشت id خالص → تعریف (کلیدهای مسطح «گروه|id» هستند)
        $by_id = [];
        foreach ($registry as $key => $info) {
            $by_id[(string) substr((string) $key, (int) strpos((string) $key, '|') + 1)] = $info;
        }
"""
new_mb = """        $saved = self::get_sections((int) $post->ID);

        // فقط سکشن‌های گروه قالب همین برگه (نگاشت قالب برگه → گروه، فاز ۷.۷)
        $by_id = self::sections_of(self::group_for_post((int) $post->ID));
"""
if 'group_for_post((int) $post->ID)' not in sm:
    assert sm.count(old_mb) == 1, 'SM anchor4'
    sm = sm.replace(old_mb, new_mb, 1)
if 'if ($registry === []) {' in sm:
    assert sm.count("if ($registry === []) {") == 1, 'SM anchor5'
    sm = sm.replace("if ($registry === []) {", "if ($by_id === []) {", 1)
sm = sm.replace("     * سکشن‌های فعال یک برگه به ترتیب ثبت/ذخیره — بدون متا: همه ثبت‌شده‌ها فعال",
                "     * سکشن‌های فعال یک برگه به ترتیب ثبت/ذخیره — بدون متا: سکشن‌های گروه قالب برگه", 1)

open(SM, 'w', encoding='utf-8').write(sm)
print('  patched inc/Admin/SectionManager.php (TEMPLATE_GROUPS + group_for_post + get_sections + render_metabox)')

# ── پچ HomeSections: استخراج contact_schema مشترک ────────────────────────
HS = os.path.join(ROOT, 'inc/Sections/HomeSections.php')
hs = open(HS, encoding='utf-8').read()
if 'function contact_schema' not in hs:
    start = hs.index("        $items['contact'] = [")
    end = hs.index("        $items['map'] = [", start)
    block = hs[start:end]
    assert block.count("'schema' => [") == 1, 'HS anchor1'
    schema_body_start = block.index("'schema' => [") + len("'schema' => [")
    schema_body_end = block.rindex("            ],\n        ];")
    schema_body = block[schema_body_start:schema_body_end]
    new_block = ("        $items['contact'] = [\n"
                 "            'label'  => __('تماس با ما', 'erfansanat'),\n"
                 "            'schema' => self::contact_schema(),\n"
                 "        ];\n")
    hs = hs[:start] + new_block + hs[end:]
    method = ("    /**\n"
              "     * اسکیم سکشن contact — مشترک بین صفحه اصلی و قالب «درباره ما» (AboutSections)\n"
              "     *\n"
              "     * @return list<array<string,mixed>>\n"
              "     */\n"
              "    public static function contact_schema(): array {\n"
              "        return [" + schema_body + "        ];\n"
              "    }\n\n"
              "    public static function init(): void {")
    assert hs.count("    public static function init(): void {") == 1, 'HS anchor2'
    hs = hs.replace("    public static function init(): void {", method, 1)
    open(HS, 'w', encoding='utf-8').write(hs)
    print('  patched inc/Sections/HomeSections.php (contact_schema مشترک)')
else:
    print('  HomeSections: contact_schema قبلاً هست')

# ── پچ Theme.php: بوت AboutSections/ContactSections ─────────────────────
TH = os.path.join(ROOT, 'inc/Theme.php')
th = open(TH, encoding='utf-8').read()
old_boot = """            if (class_exists(Sections\\HomeSections::class)) {
                Sections\\HomeSections::init();
            }
"""
if 'AboutSections::init' not in th:
    assert th.count(old_boot) == 1, 'Theme anchor'
    new_boot = old_boot + """            if (class_exists(Sections\\AboutSections::class)) {
                Sections\\AboutSections::init();
            }
            if (class_exists(Sections\\ContactSections::class)) {
                Sections\\ContactSections::init();
            }
"""
    th = th.replace(old_boot, new_boot, 1)
    open(TH, 'w', encoding='utf-8').write(th)
    print('  patched inc/Theme.php (بوت AboutSections/ContactSections)')
else:
    print('  Theme.php: قبلاً پچ شده')

# ── پچ wp-stubs: get_page_template_slug ──────────────────────────────────
WS = os.path.join(ROOT, 'temp/harness/wp-stubs.php')
ws = open(WS, encoding='utf-8').read()
if 'function get_page_template_slug' not in ws:
    anchor_ws = "/** شرطی‌های کوئری برای ViewCounter و فیلترهای ادمین */"
    assert ws.count(anchor_ws) == 1, 'stubs anchor'
    stub = """/** قالب انتخابی برگه — برای نگاشت گروه سکشن‌ها (فاز ۷.۷) */
if (!function_exists('get_page_template_slug')) {
    function get_page_template_slug(int|string|object|null $post = null): string
    {
        $id = is_object($post) ? (int) ($post->ID ?? 0) : (int) $post;
        return (string) ($GLOBALS['__es_stub_template_slug'][$id] ?? '');
    }
}

"""
    ws = ws.replace(anchor_ws, stub + anchor_ws, 1)
    open(WS, 'w', encoding='utf-8').write(ws)
    print('  patched temp/harness/wp-stubs.php (get_page_template_slug)')
else:
    print('  wp-stubs: get_page_template_slug قبلاً هست')

# ── پچ MobileWalker: نشتی <?php در nowdoc (توکن {ES_LOGO_HTML}) ─────────
MW = os.path.join(ROOT, 'inc/Nav/MobileWalker.php')
mw = open(MW, encoding='utf-8').read()
LEAK = ("<?php if (es_logo_url() !== ''): ?><img src=\"<?php echo esc_url(es_logo_url()); ?>\" "
        "alt=\"<?php echo esc_attr(es_logo_alt()); ?>\" class=\"h-9 w-9 rounded-xl object-contain\">"
        "<?php else: ?><div class=\"grid h-9 w-9 place-items-center rounded-xl border border-emerald-200 "
        "bg-emerald-50 text-xs font-black text-emerald-800\">ESE</div><?php endif; ?>")
if mw.count(LEAK) == 1:
    mw = mw.replace(LEAK, '{ES_LOGO_HTML}', 1)
    old_tr = """        $out = strtr(self::F_HEAD, [
            '{ES_HOME_URL}'  => esc_url(home_url('/')),
            '{ES_BLOGNAME}'  => esc_html(get_bloginfo('name') !== '' ? get_bloginfo('name') : 'عرفان صنعت اصفهان'),
        ]);"""
    new_tr = """        $es_logo = es_logo_url();
        $out = strtr(self::F_HEAD, [
            '{ES_HOME_URL}'   => esc_url(home_url('/')),
            '{ES_BLOGNAME}'   => esc_html(get_bloginfo('name') !== '' ? get_bloginfo('name') : 'عرفان صنعت اصفهان'),
            '{ES_LOGO_HTML}'  => $es_logo !== ''
                ? '<img src="' . esc_url($es_logo) . '" alt="' . esc_attr(es_logo_alt()) . '" class="h-9 w-9 rounded-xl object-contain">'
                : '<div class="grid h-9 w-9 place-items-center rounded-xl border border-emerald-200 bg-emerald-50 text-xs font-black text-emerald-800">ESE</div>',
        ]);"""
    assert mw.count(old_tr) == 1, 'MW anchor'
    mw = mw.replace(old_tr, new_tr, 1)
    open(MW, 'w', encoding='utf-8').write(mw)
    print('  patched inc/Nav/MobileWalker.php (رفع نشتی PHP → {ES_LOGO_HTML})')
else:
    assert '{ES_LOGO_HTML}' in mw, 'MobileWalker: نه نشتی، نه توکن!'
    print('  MobileWalker: قبلاً پچ شده')

print('gen_p77 done.')
