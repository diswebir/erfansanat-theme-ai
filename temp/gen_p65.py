#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۶.۵ — ناحیه خرید محصول (purchase.php) + PurchaseFlow (برش بایت‌دقیق wall-washer).
اجرا: python3 temp/gen_p65.py
"""
import os

ROOT = '/home/user/erfansanat-theme-ai'
SRC = os.path.join(ROOT, 'uploads/static-mirror/product/wall-washer.html')

def nowdoc(name, body, tag):
    return "$" + name + " = <<<'{t}'\n{b}\n{t};\n".format(t=tag, b=body)

s = open(SRC, encoding='utf-8').read()
i = s.find('<div class="lg:col-span-5 lg:sticky lg:top-28">')
assert i > 0
e = s.find('</section>', i)
REGION_RAW = s[i:e]
assert len(REGION_RAW) == 7025, len(REGION_RAW)

R = REGION_RAW
def tok1(R, old, new, n=1):
    assert R.count(old) >= 1, old[:60]
    return R.replace(old, new, n)

# وضعیت
R = tok1(R, '<span class="text-xs font-extrabold text-emerald-950">آماده ثبت سفارش آنلاین</span>',
         '<span class="text-xs font-extrabold text-emerald-950">{ES_STATUS}</span>')
# امتیاز
R = tok1(R, '<span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-0.5 text-amber-600 ring-1 ring-amber-200"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg><span>۴.۹۵</span></span>',
         '{ES_RATING_HTML}')
# SKU
R = tok1(R, '>ESE-WW-036</span>', '>{ES_SKU}</span>')
# قیمت قدیم
R = tok1(R, '<span class="text-xs font-bold text-slate-400 line-through">۳,۲۵۰,۰۰۰ تومان</span>', '{ES_OLD_HTML}')
# قیمت اصلی
R = tok1(R, '<span class="text-2xl font-black text-emerald-800">۲,۸۹۰,۰۰۰ تومان</span>', '{ES_PRICE_HTML}')
# چیپ حداقل سفارش
R = tok1(R, '<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-bold text-emerald-800">حداقل سفارش: ۴ شاخه</span>', '{ES_MIN_HTML}')
# چکیده مشخصات
sum0 = R.find('<div class="border-t border-slate-100 pt-4">')
assert sum0 > 0
sum1 = R.find('<div class="space-y-2.5 pt-1">', sum0)
SUMMARY = R[sum0:sum1]
R = R[:sum0] + '{ES_SUMMARY_HTML}' + R[sum1:]
assert '<div class="border-t border-slate-100 pt-4">' not in R
# دکمه خرید
b0 = R.find('<button type="button" data-pdp-add-to-cart="true"')
assert b0 > 0
b1 = R.find('</button>', b0) + len('</button>')
BTN = R[b0:b1]
R = R[:b0] + '{ES_BTN_HTML}' + R[b1:]
BTN = BTN.replace('data-product-slug="wall-washer"', 'data-product-slug="{ES_SLUG}"', 1)
BTN = BTN.replace('data-product-title="والواشر RGB و تک‌رنگ صنعتی (IP68)"', 'data-product-title="{ES_TITLE}"', 1)
BTN = BTN.replace('data-product-sku="ESE-WW-036"', 'data-product-sku="{ES_SKU}"', 1)
BTN = BTN.replace('data-product-price="0"', 'data-product-price="{ES_PRICE_RAW}"', 1)
BTN = BTN.replace('data-product-img="/images/light-element.jpg"', 'data-product-img="{ES_IMG}"', 1)
BTN = BTN.replace('data-product-image="/images/light-element.jpg"', 'data-product-image="{ES_IMG}"', 1)
BTN = BTN.replace('data-product-category="لینیرلایت و والواشر"', 'data-product-category="{ES_CAT}"', 1)
BTN = BTN.replace('data-product-min="4"', 'data-product-min="{ES_MIN}"', 1)
BTN = BTN.replace('data-product-step="2"', 'data-product-step="{ES_STEP}"', 1)
for t in ('{ES_SLUG}', '{ES_TITLE}', '{ES_SKU}', '{ES_PRICE_RAW}', '{ES_IMG}', '{ES_CAT}', '{ES_MIN}', '{ES_STEP}'):
    assert t in BTN, t
# توست: لینک سبد
R = tok1(R, 'href="../cart.html" class="rounded-lg bg-emerald-700 px-3 py-1.5 font-black text-white transition-colors hover:bg-emerald-800"',
         'href="{ES_CART_URL}" class="rounded-lg bg-emerald-700 px-3 py-1.5 font-black text-white transition-colors hover:bg-emerald-800"')
# تلفن
R = tok1(R, 'href="tel:031-91091011"', 'href="{ES_PHONE_URL}"')
R = tok1(R, '<span>مشاوره: ۰۳۱ - ۹۱۰۹۱۰۱۱</span>', '<span>{ES_PHONE_LINE}</span>')
# لینک دوم سبد
R = tok1(R, 'href="../cart.html" class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-extrabold text-slate-700 transition-all hover:border-emerald-300 hover:bg-white hover:text-emerald-800 whitespace-nowrap"',
         'href="{ES_CART_URL}" class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-extrabold text-slate-700 transition-all hover:border-emerald-300 hover:bg-white hover:text-emerald-800 whitespace-nowrap"')
assert '{ES_SUMMARY_HTML}' in R and '{ES_BTN_HTML}' in R

# بازسازی بایت‌دقیق
RATING = '<span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-0.5 text-amber-600 ring-1 ring-amber-200"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg><span>۴.۹۵</span></span>'
OLD = '<span class="text-xs font-bold text-slate-400 line-through">۳,۲۵۰,۰۰۰ تومان</span>'
PRICE = '<span class="text-2xl font-black text-emerald-800">۲,۸۹۰,۰۰۰ تومان</span>'
MINCHIP = '<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-bold text-emerald-800">حداقل سفارش: ۴ شاخه</span>'
BTN_DETOK = (BTN.replace('{ES_SLUG}', 'wall-washer').replace('{ES_TITLE}', 'والواشر RGB و تک‌رنگ صنعتی (IP68)')
             .replace('{ES_SKU}', 'ESE-WW-036').replace('{ES_PRICE_RAW}', '0')
             .replace('{ES_IMG}', '/images/light-element.jpg').replace('{ES_CAT}', 'لینیرلایت و والواشر')
             .replace('{ES_MIN}', '4').replace('{ES_STEP}', '2'))
reb = (R.replace('{ES_STATUS}', 'آماده ثبت سفارش آنلاین')
        .replace('{ES_RATING_HTML}', RATING)
        .replace('{ES_SKU}', 'ESE-WW-036')
        .replace('{ES_OLD_HTML}', OLD)
        .replace('{ES_PRICE_HTML}', PRICE)
        .replace('{ES_MIN_HTML}', MINCHIP)
        .replace('{ES_SUMMARY_HTML}', SUMMARY)
        .replace('{ES_BTN_HTML}', BTN_DETOK)
        .replace('{ES_CART_URL}', '../cart.html')
        .replace('{ES_PHONE_URL}', 'tel:031-91091011')
        .replace('{ES_PHONE_LINE}', 'مشاوره: ۰۳۱ - ۹۱۰۹۱۰۱۱'))
assert reb == REGION_RAW, f'region rebuild mismatch: {len(reb)} vs {len(REGION_RAW)}'

FRAGS_BTN = nowdoc('ES_BTN', BTN, 'ESP65B')
FRAGS_REGION = nowdoc('ES_REGION', R, 'ESP65A')

OUT = (
"<?php\n"
"/**\n"
" * ناحیه خرید محصول — کارت sticky با قیمت/دکمه دوگانه آنلاین-استعلامی/toast (برش بایت‌دقیق میرور product/wall-washer.html)\n"
" * فرگمنت با temp/gen_p65.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"use ErfanSanat\\Theme\\Frontend\\Purchasable;\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n"
"\n"
+ FRAGS_BTN +
"$es_title = get_the_title($es_pid);\n"
"$es_online = Purchasable::is_online($es_pid);\n\n"
"// قیمت‌ها (کنوانسیون کارت ۵.۱۱: _price خالی → ۰)\n"
"$es_price_raw = trim((string) get_post_meta($es_pid, '_price', true));\n"
"$es_price_raw = $es_price_raw === '' ? '0' : $es_price_raw;\n"
"$es_regular = trim((string) get_post_meta($es_pid, '_regular_price', true));\n"
"$es_fa_price = es_fa_num(number_format((float) $es_price_raw)) . ' تومان';\n"
"$es_old_html = '';\n"
"if ($es_regular !== '' && (float) $es_regular > (float) $es_price_raw && $es_price_raw !== '0') {\n"
"    $es_old_html = '<span class=\"text-xs font-bold text-slate-400 line-through\">' . es_fa_num(number_format((float) $es_regular)) . ' تومان</span>';\n"
"}\n"
"if (!$es_online || $es_price_raw === '0') {\n"
"    $es_price_html = '<span class=\"text-2xl font-black text-emerald-800\">استعلامی</span>';\n"
"    $es_old_html = '';\n"
"} else {\n"
"    $es_price_html = '<span class=\"text-2xl font-black text-emerald-800\">' . $es_fa_price . '</span>';\n"
"}\n\n"
"// حداقل سفارش / گام افزایش (es_min_qty — ارقام لاتین)\n"
"$es_min = (int) get_post_meta($es_pid, 'es_min_qty', true);\n"
"$es_step = (int) get_post_meta($es_pid, 'es_step_qty', true);\n"
"if ($es_min < 1) {\n"
"    $es_min = 1;\n"
"}\n"
"if ($es_step < 1) {\n"
"    $es_step = 1;\n"
"}\n"
"$es_min_html = $es_online && $es_min > 1\n"
"    ? '<span class=\"rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-bold text-emerald-800\">حداقل سفارش: ' . es_fa_num((string) $es_min) . ' شاخه</span>'\n"
"    : '';\n\n"
"// هدر/امتیاز/SKU\n"
"$es_rating = trim((string) get_post_meta($es_pid, '_wc_average_rating', true));\n"
"$es_rating = $es_rating !== '' ? rtrim(rtrim($es_rating, '0'), '.') : '';\n"
"$es_rating_html = $es_rating !== ''\n"
"    ? '<span class=\"inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-0.5 text-amber-600 ring-1 ring-amber-200\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400\" aria-hidden=\"true\"><path d=\"M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z\"></path></svg><span>' . es_fa_num($es_rating) . '</span></span>'\n"
"    : '';\n"
"$es_sku = trim((string) get_post_meta($es_pid, '_sku', true));\n"
"if ($es_sku === '') {\n"
"    $es_sku = trim((string) get_post_meta($es_pid, 'product_id', true));\n"
"}\n\n"
"// چکیده مشخصات: سه سلول اول اتریبیوت‌های مقدار-دار\n"
"$es_summary_cells = [];\n"
"$es_attrs_raw = get_post_meta($es_pid, '_product_attributes', true);\n"
"if (is_array($es_attrs_raw)) {\n"
"    foreach ($es_attrs_raw as $es_key => $es_def) {\n"
"        if (count($es_summary_cells) >= 3) {\n"
"            break;\n"
"        }\n"
"        if (!is_array($es_def)) {\n"
"            continue;\n"
"        }\n"
"        if (!empty($es_def['is_taxonomy'])) {\n"
"            $es_terms = get_the_terms($es_pid, (string) $es_key);\n"
"            $es_val = is_array($es_terms) && $es_terms !== [] ? (string) ($es_terms[0]->name ?? '') : '';\n"
"        } else {\n"
"            $es_val = trim((string) ($es_def['value'] ?? ''));\n"
"        }\n"
"        $es_lbl = (string) ($es_def['label'] ?? $es_key);\n"
"        if ($es_val !== '') {\n"
"            $es_summary_cells[] = '<div class=\"rounded-xl border border-slate-100 bg-slate-50/80 px-2.5 py-2 text-center\"><div class=\"truncate text-[10px] font-semibold text-slate-400\">' . esc_html($es_lbl) . '</div><div class=\"mt-0.5 truncate text-[11.5px] font-extrabold text-slate-800\">' . esc_html($es_val) . '</div></div>';\n"
"        }\n"
"    }\n"
"}\n"
"$es_summary_html = $es_summary_cells === [] ? '' : '<div class=\"border-t border-slate-100 pt-4\"><div class=\"mb-2.5 flex items-center justify-between\"><span class=\"text-xs font-extrabold text-slate-800\">چکیده مشخصات</span><a href=\"#product-details\" data-scroll-to-specs=\"true\" class=\"text-[11px] font-bold text-emerald-700 hover:underline\">مشخصات کامل و کدهای GS1 ↓</a></div><div class=\"grid grid-cols-3 gap-2\">' . implode('', $es_summary_cells) . '</div></div>';\n\n"
"// دکمه: آنلاین ↔ استعلامی\n"
"$es_cats = get_the_terms($es_pid, 'product_cat');\n"
"$es_cat = is_array($es_cats) && $es_cats !== [] ? (string) ($es_cats[0]->name ?? '') : '';\n"
"$es_thumb = (int) get_post_meta($es_pid, '_thumbnail_id', true);\n"
"$es_img = $es_thumb > 0 ? es_media_url($es_thumb) : es_media_url('assets/images/light-tree.jpg');\n"
"$es_slug = (string) get_post_field('post_name', $es_pid);\n"
"$es_btn = strtr($ES_BTN, [\n"
"    '{ES_SLUG}' => esc_attr($es_slug), '{ES_TITLE}' => esc_attr($es_title), '{ES_SKU}' => esc_attr($es_sku),\n"
"    '{ES_PRICE_RAW}' => esc_attr($es_price_raw), '{ES_IMG}' => esc_url($es_img), '{ES_CAT}' => esc_attr($es_cat),\n"
"    '{ES_MIN}' => (string) $es_min, '{ES_STEP}' => (string) $es_step,\n"
"]);\n"
"if (!$es_online) {\n"
"    $es_btn = str_replace('<button type=\"button\" data-pdp-add-to-cart=\"true\"', '<a href=\"' . esc_url(es_cta_url()) . '\" data-pdp-inquiry=\"true\"', $es_btn);\n"
"    $es_btn = preg_replace('/<svg.*?<\\/svg><span data-pdp-add-label=\"true\">[^<]*<\\/span><\\/button>/s', '<span data-pdp-add-label=\"true\">استعلام قیمت و دریافت مشاوره</span></a>', $es_btn);\n"
"    $es_btn = str_replace('cursor-pointer whitespace-nowrap', 'cursor-pointer whitespace-nowrap', $es_btn);\n"
"}\n\n"
"// تماس (پیش‌فرض = میرور)\n"
"$es_contact = class_exists('\\ErfanSanat\\Theme\\Admin\\OptionsOutput') ? \\ErfanSanat\\Theme\\Admin\\OptionsOutput::contact_args() : [];\n"
"$es_phone_raw = (string) ($es_contact['phone'] ?? '031-91091011');\n"
"$es_phone_url = 'tel:' . preg_replace('/[^0-9]+/', '', $es_phone_raw);\n"
"$es_phone_display = (string) es_option('footer.purchase_phone_display', '۰۳۱ - ۹۱۰۹۱۰۱۱');\n\n"
"$es_data = apply_filters('es_product_purchase_args', [\n"
"    'status'     => $es_online ? 'آماده ثبت سفارش آنلاین' : 'استعلام قیمت و مشاوره',\n"
"    'btn'        => $es_btn,\n"
"    'price_html' => $es_price_html,\n"
"    'old_html'   => $es_old_html,\n"
"    'min_html'   => $es_min_html,\n"
"    'summary'    => $es_summary_html,\n"
"    'rating'     => $es_rating_html,\n"
"], $es_pid);\n\n"
+ FRAGS_REGION +
"\necho strtr($ES_REGION, [\n"
"    '{ES_STATUS}'       => esc_html((string) $es_data['status']),\n"
"    '{ES_RATING_HTML}'  => (string) $es_data['rating'],\n"
"    '{ES_SKU}'          => esc_html($es_sku),\n"
"    '{ES_OLD_HTML}'     => (string) $es_data['old_html'],\n"
"    '{ES_PRICE_HTML}'   => (string) $es_data['price_html'],\n"
"    '{ES_MIN_HTML}'     => (string) $es_data['min_html'],\n"
"    '{ES_SUMMARY_HTML}' => (string) $es_data['summary'],\n"
"    '{ES_BTN_HTML}'     => (string) $es_data['btn'],\n"
"    '{ES_CART_URL}'     => esc_url(es_page_url('cart')),\n"
"    '{ES_PHONE_URL}'    => esc_url($es_phone_url),\n"
"    '{ES_PHONE_LINE}'   => esc_html('مشاوره: ' . $es_phone_display),\n"
"]);\n")

open(os.path.join(ROOT, 'template-parts/single-product/purchase.php'), 'w', encoding='utf-8').write(OUT)

PURCHASE_FLOW = '''<?php
/**
 * جریان خرید — بستن خرید آنلاین برای محصولات استعلامی (true_sale=false)
 *
 * فیلترهای ووکامرس: is_purchasable → false و متن دکمه‌ها → «استعلام قیمت».
 * هیچ رفتار پرداختی دیگری تغییر نمی‌کند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\\Theme\\Woo;

use ErfanSanat\\Theme\\Frontend\\Purchasable;

final class PurchaseFlow {

    public static function init(): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        add_filter('woocommerce_is_purchasable', [self::class, 'is_purchasable'], 10, 2);
        add_filter('woocommerce_product_add_to_cart_text', [self::class, 'button_text'], 10, 2);
        add_filter('woocommerce_product_single_add_to_cart_text', [self::class, 'button_text'], 10, 2);
    }

    /** محصول استعلامی (true_sale=false) قابل خرید آنلاین نیست */
    public static function is_purchasable(bool $purchasable, object $product): bool {
        $pid = method_exists($product, 'get_id') ? (int) $product->get_id() : (int) ($product->ID ?? 0);
        if ($pid > 0 && !Purchasable::is_online($pid)) {
            return false;
        }
        return $purchasable;
    }

    /** متن دکمه آرشیو/سینگل برای استعلامی */
    public static function button_text(string $text, object $product): string {
        $pid = method_exists($product, 'get_id') ? (int) $product->get_id() : (int) ($product->ID ?? 0);
        if ($pid > 0 && !Purchasable::is_online($pid)) {
            return 'استعلام قیمت';
        }
        return $text;
    }
}
'''
open(os.path.join(ROOT, 'inc/Woo/PurchaseFlow.php'), 'w', encoding='utf-8').write(PURCHASE_FLOW)

print('purchase.php:', os.path.getsize(os.path.join(ROOT, 'template-parts/single-product/purchase.php')))
print('PurchaseFlow.php:', os.path.getsize(os.path.join(ROOT, 'inc/Woo/PurchaseFlow.php')))
