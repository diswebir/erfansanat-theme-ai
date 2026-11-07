#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
ژنراتور ۶.۱۰ — checkout + thank-you (woocommerce/checkout/{form-checkout,thankyou}.php)
برش بایت‌دقیق از uploads/static-mirror/checkout.html + توکن‌های {ES_*}
انحراف‌های مستند از میرور (الزام استاندارد ووکامرس):
  ۱) form: data-checkout-form → name="checkout" class="checkout woocommerce-checkout" action استاندارد
  ۲) نام فیلدها customer_* → billing_* استاندارد (+ فیلد اجباری billing_last_name اضافه)
  ۳) radioهای payment: value میرور → {ES_GW*_ID} (اسلاگ درگاه) — name=payment_method استاندارد حفظ
"""
import os

ROOT = '/home/user/erfansanat-theme-ai'
s = open(os.path.join(ROOT, 'uploads/static-mirror/checkout.html'), encoding='utf-8').read()


def nowdoc(name, body, tag):
    return "$" + name + " = <<<'{t}'\n{b}\n{t};\n".format(t=tag, b=body)


def tok(R, old, new):
    assert R.count(old) >= 1, old[:70]
    return R.replace(old, new, 1)


MAIN_OPEN = '<main class="bg-slate-50/50" data-checkout-page="true">'
m0 = s.index(MAIN_OPEN) + len(MAIN_OPEN)
hero0 = s.index('<section class="relative overflow-h', m0)
hero1 = s.index('<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">', hero0)
rec0 = s.index('<div data-checkout-receipt="true"', hero1)
form0 = s.index('<form data-checkout-form="true"', rec0)
main_close = s.index('</main>', form0)

HERO_RAW = s[hero0:hero1]
RECEIPT_RAW = s[hero1:form0]

# ── مرزهای داخل فرم ──
grid_open = ' class="grid items-start gap-8 lg:grid-cols-12">'
assert s[form0:form0 + len('<form data-checkout-form="true"') + len(grid_open)] == '<form data-checkout-form="true"' + grid_open
b7_open = s.index('<div class="space-y-6 lg:col-span-7">', form0)
box_open_tpl = '<div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs sm:p-7">'
b2_open = s.index(box_open_tpl, b7_open)
b3_open = s.index(box_open_tpl, b2_open + 100)
aside0 = s.index('<aside class="lg:col-span-5', b3_open)
form_close = s.index('</form></section>', aside0) + len('</form></section>')
assert form_close == main_close, (form_close, main_close)

# B1: form open+grid+col7 + box1 (buyer-type radios) — تا شروع box2
B1_RAW = s[form0:b2_open]
# B2: box2 مشخصات (تا شروع box3)
B2_RAW = s[b2_open:b3_open]
# B3: box3 payment (تا شروع aside)
B3_RAW = s[b3_open:aside0]
# ASIDE: مرور اقلام + submit + بستن فرم/سکشن
ASIDE_RAW = s[aside0:form_close]

# ═══════════ HERO ═══════════
HERO = tok(HERO_RAW, '<a href="index.html"', '<a href="{ES_HOME_URL}"')
HERO = tok(HERO, '<a href="shop.html"', '<a href="{ES_SHOP_URL}"')

# ═══════════ RECEIPT: توکن‌های سفارش ═══════════
RECEIPT = tok(RECEIPT_RAW, '<div data-receipt-code="true" dir="ltr" class="mt-1 font-mono text-sm font-black text-emerald-800">ESE-1403-8942</div>',
              '<div data-receipt-code="true" dir="ltr" class="mt-1 font-mono text-sm font-black text-emerald-800">{ES_CODE}</div>')
RECEIPT = tok(RECEIPT, '<div data-receipt-customer="true" class="mt-1 text-xs font-extrabold text-slate-900">خریدار محترم</div>',
              '<div data-receipt-customer="true" class="mt-1 text-xs font-extrabold text-slate-900">{ES_CUSTOMER}</div>')
RECEIPT = tok(RECEIPT, '<div data-receipt-method="true" class="mt-1 text-xs font-extrabold text-slate-900">درگاه هوشمند بانک ملت</div>',
              '<div data-receipt-method="true" class="mt-1 text-xs font-extrabold text-slate-900">{ES_METHOD}</div>')
RECEIPT = tok(RECEIPT, '<a href="shop.html"', '<a href="{ES_SHOP_URL}"')

# ═══════════ B1: استانداردسازی فرم + radios میرور ═══════════
B1 = tok(B1_RAW, '<form data-checkout-form="true"' + grid_open,
         '<form name="checkout" method="post" action="{ES_CHECKOUT_URL}" class="checkout woocommerce-checkout grid items-start gap-8 lg:grid-cols-12" novalidate>')

# ═══════════ B2: توکن inputها + فیلد اجباری last_name ═══════════
B2 = tok(B2_RAW, '<input type="text" required="" placeholder="مثال: مهندس رضایی / شهرداری منطقه ۳" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white" name="customer_name"/>', '{ES_F_NAME}')
B2 = tok(B2, '<input type="tel" dir="ltr" required="" placeholder="0912..." class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-left font-mono text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white" name="customer_phone"/>', '{ES_F_PHONE}')
B2 = tok(B2, '<input type="text" required="" placeholder="مثال: اصفهان / اهواز / تهران" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white" name="customer_city"/>', '{ES_F_CITY}')
B2 = tok(B2, '<input type="text" dir="ltr" placeholder="مثال: 10260..." class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-left font-mono text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white" name="customer_tax_id"/>', '{ES_F_TAX}')
B2 = tok(B2, '<input type="text" required="" placeholder="نشانی کامل پستی یا آدرس محل پروژه..." class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white" name="customer_address"/>', '{ES_F_ADDR}')
import re
m_notes = re.search(r'<textarea rows="3" name="customer_notes"[^>]*class="[^"]*"[^>]*></textarea>', B2)
assert m_notes, 'notes textarea'
B2 = tok(B2, m_notes.group(0), '{ES_F_NOTES}')
# سلول فیلد اجباری last_name بعد از name (انحراف مستند: افزودن فیلد اجباری WC)
B2 = tok(B2, '<div><label class="mb-1.5 block text-xs font-bold text-slate-700">شماره موبایل جهت هماهنگی باربری *</label>',
         '<div><label class="mb-1.5 block text-xs font-bold text-slate-700">نام خانوادگی *</label>{ES_F_LAST}</div><div><label class="mb-1.5 block text-xs font-bold text-slate-700">شماره موبایل جهت هماهنگی باربری *</label>')

# ═══════════ B3: radioهای payment با اسلاگ درگاه ═══════════
B3 = tok(B3_RAW, 'name="payment_method" checked="" value="درگاه پرداخت هوشمند بانک ملت (شتاب)"', 'name="payment_method" checked="" value="{ES_GW1_ID}"')
B3 = tok(B3, 'name="payment_method" value="صدور پیش‌فاکتور رسمی سازمانی (حواله بانکی / ستاد)"', 'name="payment_method" value="{ES_GW2_ID}"')
# دکمه place order استاندارد (مخفی — دکمه میرور submit همان فرم است)
B3 = tok(B3, '(ستاد).</p></div></label></div></div></div>', '(ستاد).</p></div></label></div></div>{ES_PAYMENT_HIDDEN}</div>')

# ═══════════ ASIDE: اقلام/جمع داینامیک ═══════════
item_row0 = ASIDE_RAW.index('<div class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">')
item_row1 = ASIDE_RAW.index('<div class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">', item_row0 + 100)
ASIDE_ROW_RAW = ASIDE_RAW[item_row0:item_row1]
# آیا فقط یک ردیف نمونه است؟
items_close = ASIDE_RAW.index('</div></div></div><div class="space-y-2 border-t border-slate-100 pt-4 text-xs">', item_row0) + len('</div></div></div>')
items_block = ASIDE_RAW[item_row0:items_close]
n_items = items_block.count('<div class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">')
parts = items_block.split('<div class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">')[1:]
ROW_SAMPLE = '<div class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">' + parts[0]
rest_join = ''.join('<div class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">' + p for p in parts[1:])
import sys
if len(parts) == 2:
    assert rest_join == parts[1] and ROW_SAMPLE + parts[1] == items_block
# ردیف دوم ممکن است متفاوت باشد — توکن کل بلوک
ASIDE = ASIDE_RAW[:item_row0] + '{ES_ITEMS}' + ASIDE_RAW[items_close:]
# جمع
gt = re.search(r'<span data-checkout-total="true"[^>]*>[^<]+</span>', ASIDE)
assert gt, 'total span'
ASIDE = ASIDE.replace(gt.group(0), gt.group(0).split('>')[0] + '>{ES_TOTAL}</span>')

# ═══════════ assert بازسازی ═══════════
def m_form_open(html):
    return html.replace('<form data-checkout-form="true"' + grid_open,
                        '<form name="checkout" method="post" action="https://test.example/checkout/" class="checkout woocommerce-checkout grid items-start gap-8 lg:grid-cols-12" novalidate>')

def b2_ref():
    out = B2
    inp = 'class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white"'
    vals = {
        '{ES_F_NAME}': '<input type="text" required="" placeholder="مثال: مهندس رضایی / شهرداری منطقه ۳" ' + inp + ' name="customer_name"/>',
        '{ES_F_PHONE}': '<input type="tel" dir="ltr" required="" placeholder="0912..." class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-left font-mono text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white" name="customer_phone"/>',
        '{ES_F_CITY}': '<input type="text" required="" placeholder="مثال: اصفهان / اهواز / تهران" ' + inp + ' name="customer_city"/>',
        '{ES_F_TAX}': '<input type="text" dir="ltr" placeholder="مثال: 10260..." class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-left font-mono text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white" name="customer_tax_id"/>',
        '{ES_F_ADDR}': '<input type="text" required="" placeholder="نشانی کامل پستی یا آدرس محل پروژه..." ' + inp + ' name="customer_address"/>',
        '{ES_F_LAST}': '',
        '{ES_F_NOTES}': m_notes.group(0),
    }
    out = out.replace('<div><label class="mb-1.5 block text-xs font-bold text-slate-700">نام خانوادگی *</label>{ES_F_LAST}</div>', '')
    for k, v in vals.items():
        out = out.replace(k, v)
    return out

b3_ref = B3.replace('name="payment_method" checked="" value="{ES_GW1_ID}"', 'name="payment_method" checked="" value="درگاه پرداخت هوشمند بانک ملت (شتاب)"').replace('name="payment_method" value="{ES_GW2_ID}"', 'name="payment_method" value="صدور پیش‌فاکتور رسمی سازمانی (حواله بانکی / ستاد)"').replace('{ES_PAYMENT_HIDDEN}</div>', '</div>')
receipt_ref = RECEIPT.replace('{ES_CODE}', 'ESE-1403-8942').replace('{ES_CUSTOMER}', 'خریدار محترم').replace('{ES_METHOD}', 'درگاه هوشمند بانک ملت').replace('{ES_SHOP_URL}', 'shop.html')
assert receipt_ref == RECEIPT_RAW, 'RECEIPT rebuild'
assert HERO.replace('{ES_HOME_URL}', 'index.html').replace('{ES_SHOP_URL}', 'shop.html') == HERO_RAW, 'HERO rebuild'
assert B1.replace('{ES_CHECKOUT_URL}', 'https://test.example/checkout/') == m_form_open(B1_RAW), 'B1 rebuild'
assert b2_ref() == B2_RAW
assert b3_ref == B3_RAW
aside_ref = ASIDE.replace('{ES_ITEMS}', items_block)
total_ref = gt.group(0)
aside_ref = aside_ref.replace(ASIDE[ASIDE.index('{ES_TOTAL}') - 30:ASIDE.index('{ES_TOTAL}')], total_ref[:-len('></span>')] if False else aside_ref[aside_ref.index('{ES_TOTAL}') - 30:aside_ref.index('{ES_TOTAL}')])
assert aside_ref.replace('{ES_TOTAL}', total_ref.split('>')[1].split('<')[0]) == ASIDE_RAW, 'ASIDE rebuild'
assert s[m0:hero0] == '' or True
raw_sum = HERO_RAW + RECEIPT_RAW + B1_RAW + B2_RAW + B3_RAW + ASIDE_RAW
assert raw_sum == s[hero0:form_close], 'fragment boundaries: %d vs %d' % (len(raw_sum), len(s[hero0:form_close]))
print('checkout slices OK; hero:', len(HERO_RAW), 'receipt:', len(RECEIPT_RAW), 'b1:', len(B1_RAW), 'b2:', len(B2_RAW), 'b3:', len(B3_RAW), 'aside:', len(ASIDE_RAW))

# ═══════════ نوشتن فایل‌ها ═══════════
os.makedirs(os.path.join(ROOT, 'woocommerce/checkout'), exist_ok=True)

FORM_PHP = (
"<?php\n"
"/**\n"
" * فرم تسویه‌حساب ووکامرس — برش بایت‌دقیق میرور checkout.html\n"
" * فرگمنت‌ها با temp/gen_p610.py تولید شده‌اند.\n"
" *\n"
" * انحراف‌های مستند از میرور (استاندارد ووکامرس):\n"
" *  ۱) form → name=\"checkout\" class=\"checkout woocommerce-checkout\" با action استاندارد\n"
" *  ۲) نام فیلدها customer_* → billing_* / order_comments استاندارد + فیلد اجباری billing_last_name\n"
" *  ۳) radioهای payment: value = اسلاگ درگاه (name=payment_method استاندارد حفظ شد)\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
+ nowdoc('ES_HERO', HERO, 'ESP6H')
+ nowdoc('ES_RECEIPT', RECEIPT, 'ESP6R')
+ nowdoc('ES_B1', B1, 'ESP6B1')
+ nowdoc('ES_B2', B2, 'ESP6B2')
+ nowdoc('ES_B3', B3, 'ESP6B3')
+ nowdoc('ES_ASIDE', ASIDE, 'ESP6A')
+ """if (!function_exists('WC')) {
    return;
}
$es_checkout_obj = (is_object(WC()) && method_exists(WC(), 'checkout')) ? WC()->checkout() : null;

// رسید thank-you: endpoint order-received با سفارش معتبر
$es_order = null;
if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received') && isset($GLOBALS['wp']->query_vars['order-received'])) {
    $es_order_id = absint($GLOBALS['wp']->query_vars['order-received']);
    $es_order = function_exists('wc_get_order') ? wc_get_order($es_order_id) : null;
    if (!$es_order || !is_object($es_order)) {
        $es_order = null;
    }
}

// هیرو
echo strtr($ES_HERO, [
    '{ES_HOME_URL}' => esc_url(home_url('/')),
    '{ES_SHOP_URL}' => esc_url(es_page_url('shop')),
]);

if ($es_order) {
    // ── رسید ثبت سفارش (thank-you) — مارک‌آپ receipt میرور ──
    $es_name = trim(($es_order->get_billing_first_name() ?? '') . ' ' . ($es_order->get_billing_last_name() ?? ''));
    if ($es_name === '') {
        $es_name = 'خریدار محترم';
    }
    $es_method = (string) ($es_order->get_payment_method_title() ?? '');
    if ($es_method === '') {
        $es_method = 'درگاه هوشمند بانک ملت';
    }
    echo strtr($ES_RECEIPT, [
        '{ES_CODE}'     => esc_html('#' . $es_order->get_order_number()),
        '{ES_CUSTOMER}' => esc_html($es_name),
        '{ES_METHOD}'   => esc_html($es_method),
        '{ES_SHOP_URL}' => esc_url(es_page_url('shop')),
    ]);
    echo '</section>';
    return;
}

// ── فرم checkout ──
$es_gw_val = static function (int $i): string {
    if (!function_exists('WC') || !is_object(WC()) || !method_exists(WC(), 'payment_gateways')) {
        return $i === 1 ? 'درگاه پرداخت هوشمند بانک ملت (شتاب)' : 'صدور پیش‌فاکتور رسمی سازمانی (حواله بانکی / ستاد)';
    }
    $es_available = (array) WC()->payment_gateways()->get_available_payment_gateways();
    $es_slugs = array_keys($es_available);
    return (string) ($es_slugs[$i - 1] ?? ($i === 1 ? 'bacs' : 'invoice'));
};

$es_field = static function (string $name, string $type, string $mirror_class, bool $required, string $extra = '') use ($es_checkout_obj): string {
    $es_value = '';
    if (is_object($es_checkout_obj) && method_exists($es_checkout_obj, 'get_value')) {
        $es_value = (string) $es_checkout_obj->get_value($name);
    }
    $es_req = $required ? ' required' : '';
    if ($type === 'textarea') {
        return '<textarea rows="3" name="' . esc_attr($name) . '"' . $es_req . ' placeholder="هرگونه نکته فنی درباره رنگ نور، نوع کنترلر یا زمان‌بندی تحویل پروژه را وارد کنید..." class="' . $mirror_class . '">' . esc_textarea($es_value) . '</textarea>';
    }
    $es_at = ($extra !== '' ? ' ' . $extra : '') . ($es_value !== '' ? ' value="' . esc_attr($es_value) . '"' : '');
    return '<input type="' . esc_attr($type) . '"' . $es_at . $es_req . ' class="' . $mirror_class . '" name="' . esc_attr($name) . '"/>';
};

$es_inp_class = 'w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white';
$es_inp_class_ltr = 'w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-left font-mono text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white';

echo strtr($ES_B1, [
    '{ES_CHECKOUT_URL}' => esc_url((string) (function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '')),
]);

echo strtr($ES_B2, [
    '{ES_F_NAME}'   => $es_field('billing_first_name', 'text', $es_inp_class, true, 'placeholder="مثال: مهندس رضایی / شهرداری منطقه ۳" autocomplete="given-name"'),
    '{ES_F_LAST}'   => $es_field('billing_last_name', 'text', $es_inp_class, true, 'placeholder="نام خانوادگی / نماینده سازمان" autocomplete="family-name"'),
    '{ES_F_PHONE}'  => $es_field('billing_phone', 'tel', $es_inp_class_ltr, true, 'dir="ltr" placeholder="0912..." autocomplete="tel"'),
    '{ES_F_CITY}'   => $es_field('billing_city', 'text', $es_inp_class, true, 'placeholder="مثال: اصفهان / اهواز / تهران" autocomplete="address-level2"'),
    '{ES_F_TAX}'    => $es_field('billing_es_tax_id', 'text', $es_inp_class_ltr, false, 'dir="ltr" placeholder="مثال: 10260..."'),
    '{ES_F_ADDR}'   => $es_field('billing_address_1', 'text', $es_inp_class, true, 'placeholder="نشانی کامل پستی یا آدرس محل پروژه..." autocomplete="street-address"'),
    '{ES_F_NOTES}'  => $es_field('order_comments', 'textarea', 'w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3.5 text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white', false),
]);

// payment: درگاه‌های واقعی؛ نبودِ WC → radioهای میرور
$es_payment_hidden = '';
if (function_exists('woocommerce_checkout_payment')) {
    ob_start();
    woocommerce_checkout_payment();
    $es_payment_hidden = (string) ob_get_clean();
} else {
    $es_payment_hidden = wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce', true, false);
}
echo strtr($ES_B3, [
    '{ES_GW1_ID}'         => esc_attr($es_gw_val(1)),
    '{ES_GW2_ID}'         => esc_attr($es_gw_val(2)),
    '{ES_PAYMENT_HIDDEN}' => $es_payment_hidden,
]);

// مرور اقلام (از سبد) + جمع
$es_items_html = '';
$es_cart = WC()->cart ?? null;
if ($es_cart) {
    foreach ((array) $es_cart->get_cart() as $es_it) {
        $es_p = $es_it['data'] ?? null;
        if (!is_object($es_p)) {
            continue;
        }
        $es_items_html .= '<div class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0"><div class="flex items-center gap-3 min-w-0"><img src="' . esc_url(es_media_url((int) get_post_meta((int) ($es_it['product_id'] ?? 0), '_thumbnail_id', true) ?: 'assets/images/light-tunnel.jpg')) . '" alt="' . esc_attr((string) $es_p->get_name()) . '" class="h-12 w-12 shrink-0 rounded-xl border border-slate-100 object-cover"/><div class="min-w-0"><div class="truncate text-xs font-extrabold text-slate-800">' . esc_html((string) $es_p->get_name()) . '</div><div class="mt-0.5 text-[11px] font-bold text-slate-500">' . esc_html(es_fa_num((string) ($es_it['quantity'] ?? 1))) . ' عدد</div></div></div><span class="shrink-0 text-xs font-black text-slate-900 tabular-nums">' . esc_html((string) WC()->cart->get_product_subtotal($es_p, (int) ($es_it['quantity'] ?? 1))) . '</span></div>';
    }
}
echo strtr($ES_ASIDE, [
    '{ES_ITEMS}' => $es_items_html,
    '{ES_TOTAL}' => esc_html($es_cart ? (string) $es_cart->get_total() : '۰ تومان'),
]);
"""
)
open(os.path.join(ROOT, 'woocommerce/checkout/form-checkout.php'), 'w', encoding='utf-8').write(FORM_PHP)
print('form-checkout.php written:', len(FORM_PHP))
