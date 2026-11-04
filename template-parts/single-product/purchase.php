<?php
/**
 * ناحیه خرید محصول — کارت sticky با قیمت/دکمه دوگانه آنلاین-استعلامی/toast (برش بایت‌دقیق میرور product/wall-washer.html)
 * فرگمنت با temp/gen_p65.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Frontend\Purchasable;

$es_pid = (int) ($args['pid'] ?? 0);

$ES_BTN = <<<'ESP65B'
<button type="button" data-pdp-add-to-cart="true" data-product-slug="{ES_SLUG}" data-product-title="{ES_TITLE}" data-product-sku="{ES_SKU}" data-product-price="{ES_PRICE_RAW}" data-product-img="{ES_IMG}" data-product-image="{ES_IMG}" data-product-category="{ES_CAT}" data-product-min="{ES_MIN}" data-product-step="{ES_STEP}" class="group relative inline-flex w-full items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-700 px-5 py-4 text-sm font-black text-white shadow-[0_12px_28px_-8px_rgba(0,154,56,0.48)] ring-1 ring-emerald-500/40 transition-all duration-300 hover:-translate-y-0.5 hover:from-emerald-800 hover:via-emerald-700 hover:to-emerald-800 hover:shadow-[0_16px_34px_-8px_rgba(0,154,56,0.62)] active:translate-y-0 cursor-pointer whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg><span data-pdp-add-label="true">افزودن به سبد خرید و پیش‌فاکتور</span></button>
ESP65B;
$es_title = get_the_title($es_pid);
$es_online = Purchasable::is_online($es_pid);

// قیمت‌ها (کنوانسیون کارت ۵.۱۱: _price خالی → ۰)
$es_price_raw = trim((string) get_post_meta($es_pid, '_price', true));
$es_price_raw = $es_price_raw === '' ? '0' : $es_price_raw;
$es_regular = trim((string) get_post_meta($es_pid, '_regular_price', true));
$es_fa_price = es_fa_num(number_format((float) $es_price_raw)) . ' تومان';
$es_old_html = '';
if ($es_regular !== '' && (float) $es_regular > (float) $es_price_raw && $es_price_raw !== '0') {
    $es_old_html = '<span class="text-xs font-bold text-slate-400 line-through">' . es_fa_num(number_format((float) $es_regular)) . ' تومان</span>';
}
if (!$es_online || $es_price_raw === '0') {
    $es_price_html = '<span class="text-2xl font-black text-emerald-800">استعلامی</span>';
    $es_old_html = '';
} else {
    $es_price_html = '<span class="text-2xl font-black text-emerald-800">' . $es_fa_price . '</span>';
}

// حداقل سفارش / گام افزایش (es_min_qty — ارقام لاتین)
$es_min = (int) get_post_meta($es_pid, 'es_min_qty', true);
$es_step = (int) get_post_meta($es_pid, 'es_step_qty', true);
if ($es_min < 1) {
    $es_min = 1;
}
if ($es_step < 1) {
    $es_step = 1;
}
$es_min_html = $es_online && $es_min > 1
    ? '<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-bold text-emerald-800">حداقل سفارش: ' . es_fa_num((string) $es_min) . ' شاخه</span>'
    : '';

// هدر/امتیاز/SKU
$es_rating = trim((string) get_post_meta($es_pid, '_wc_average_rating', true));
$es_rating = $es_rating !== '' ? rtrim(rtrim($es_rating, '0'), '.') : '';
$es_rating_html = $es_rating !== ''
    ? '<span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-0.5 text-amber-600 ring-1 ring-amber-200"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg><span>' . es_fa_num($es_rating) . '</span></span>'
    : '';
$es_sku = trim((string) get_post_meta($es_pid, '_sku', true));
if ($es_sku === '') {
    $es_sku = trim((string) get_post_meta($es_pid, 'product_id', true));
}

// چکیده مشخصات: سه سلول اول اتریبیوت‌های مقدار-دار
$es_summary_cells = [];
$es_attrs_raw = get_post_meta($es_pid, '_product_attributes', true);
if (is_array($es_attrs_raw)) {
    foreach ($es_attrs_raw as $es_key => $es_def) {
        if (count($es_summary_cells) >= 3) {
            break;
        }
        if (!is_array($es_def)) {
            continue;
        }
        if (!empty($es_def['is_taxonomy'])) {
            $es_terms = get_the_terms($es_pid, (string) $es_key);
            $es_val = is_array($es_terms) && $es_terms !== [] ? (string) ($es_terms[0]->name ?? '') : '';
        } else {
            $es_val = trim((string) ($es_def['value'] ?? ''));
        }
        $es_lbl = (string) ($es_def['label'] ?? $es_key);
        if ($es_val !== '') {
            $es_summary_cells[] = '<div class="rounded-xl border border-slate-100 bg-slate-50/80 px-2.5 py-2 text-center"><div class="truncate text-[10px] font-semibold text-slate-400">' . esc_html($es_lbl) . '</div><div class="mt-0.5 truncate text-[11.5px] font-extrabold text-slate-800">' . esc_html($es_val) . '</div></div>';
        }
    }
}
$es_summary_html = $es_summary_cells === [] ? '' : '<div class="border-t border-slate-100 pt-4"><div class="mb-2.5 flex items-center justify-between"><span class="text-xs font-extrabold text-slate-800">چکیده مشخصات</span><a href="#product-details" data-scroll-to-specs="true" class="text-[11px] font-bold text-emerald-700 hover:underline">مشخصات کامل و کدهای GS1 ↓</a></div><div class="grid grid-cols-3 gap-2">' . implode('', $es_summary_cells) . '</div></div>';

// دکمه: آنلاین ↔ استعلامی
$es_cats = get_the_terms($es_pid, 'product_cat');
$es_cat = is_array($es_cats) && $es_cats !== [] ? (string) ($es_cats[0]->name ?? '') : '';
$es_thumb = (int) get_post_meta($es_pid, '_thumbnail_id', true);
$es_img = $es_thumb > 0 ? es_media_url($es_thumb) : es_media_url('assets/images/light-tree.jpg');
$es_slug = (string) get_post_field('post_name', $es_pid);
$es_btn = strtr($ES_BTN, [
    '{ES_SLUG}' => esc_attr($es_slug), '{ES_TITLE}' => esc_attr($es_title), '{ES_SKU}' => esc_attr($es_sku),
    '{ES_PRICE_RAW}' => esc_attr($es_price_raw), '{ES_IMG}' => esc_url($es_img), '{ES_CAT}' => esc_attr($es_cat),
    '{ES_MIN}' => (string) $es_min, '{ES_STEP}' => (string) $es_step,
]);
if (!$es_online) {
    $es_btn = str_replace('<button type="button" data-pdp-add-to-cart="true"', '<a href="' . esc_url(es_cta_url()) . '" data-pdp-inquiry="true"', $es_btn);
    $es_btn = preg_replace('/<svg.*?<\/svg><span data-pdp-add-label="true">[^<]*<\/span><\/button>/s', '<span data-pdp-add-label="true">استعلام قیمت و دریافت مشاوره</span></a>', $es_btn);
    $es_btn = str_replace('cursor-pointer whitespace-nowrap', 'cursor-pointer whitespace-nowrap', $es_btn);
}

// تماس (پیش‌فرض = میرور)
$es_contact = class_exists('\ErfanSanat\Theme\Admin\OptionsOutput') ? \ErfanSanat\Theme\Admin\OptionsOutput::contact_args() : [];
$es_phone_raw = (string) ($es_contact['phone'] ?? '031-91091011');
$es_phone_url = 'tel:' . preg_replace('/[^0-9]+/', '', $es_phone_raw);
$es_phone_display = (string) es_option('footer.purchase_phone_display', '۰۳۱ - ۹۱۰۹۱۰۱۱');

$es_data = apply_filters('es_product_purchase_args', [
    'status'     => $es_online ? 'آماده ثبت سفارش آنلاین' : 'استعلام قیمت و مشاوره',
    'btn'        => $es_btn,
    'price_html' => $es_price_html,
    'old_html'   => $es_old_html,
    'min_html'   => $es_min_html,
    'summary'    => $es_summary_html,
    'rating'     => $es_rating_html,
], $es_pid);

$ES_REGION = <<<'ESP65A'
<div class="lg:col-span-5 lg:sticky lg:top-28"><div custom="0.08"><div class="overflow-hidden rounded-3xl border border-emerald-200/80 bg-white shadow-[0_20px_50px_-24px_rgba(5,150,105,0.16)]"><div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-emerald-50/60 px-5 py-3.5 sm:px-6"><div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20"></span><span class="text-xs font-extrabold text-emerald-950">{ES_STATUS}</span></div><div class="flex items-center gap-2 text-[11.5px] font-bold text-slate-600">{ES_RATING_HTML}<span dir="ltr" class="rounded-md bg-white px-2 py-0.5 font-mono text-[11px] font-bold text-slate-600 ring-1 ring-slate-200/80">{ES_SKU}</span></div></div><div class="space-y-5 p-5 sm:p-6"><div><div class="mb-1 flex items-center gap-2">{ES_OLD_HTML}<span class="rounded-md bg-rose-50 px-2 py-0.5 text-[10px] font-extrabold text-rose-700">قیمت مصوب کارخانه</span></div><div class="flex flex-wrap items-baseline justify-between gap-2">{ES_PRICE_HTML}{ES_MIN_HTML}</div><p class="mt-1 text-[11.5px] leading-5 text-slate-500">فروش مستقیم از کارخانه عرفان صنعت اصفهان بدون واسطه (همراه با فاکتور رسمی)</p></div>{ES_SUMMARY_HTML}<div class="space-y-2.5 pt-1">{ES_BTN_HTML}<div data-pdp-added-banner="true" data-pdp-cart-toast="true" class="hidden items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-xs"><span class="font-extrabold text-emerald-900">✓ به سبد خرید اضافه شد</span><a href="{ES_CART_URL}" class="rounded-lg bg-emerald-700 px-3 py-1.5 font-black text-white transition-colors hover:bg-emerald-800">مشاهده سبد خرید ←</a></div><div class="grid grid-cols-2 gap-2.5"><a href="{ES_PHONE_URL}" class="flex items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50/80 px-3 py-2.5 text-xs font-extrabold text-emerald-900 transition-all hover:border-emerald-400 hover:bg-emerald-100/80 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span>{ES_PHONE_LINE}</span></a><a href="{ES_CART_URL}" class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-extrabold text-slate-700 transition-all hover:border-emerald-300 hover:bg-white hover:text-emerald-800 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg><span>مشاهده سبد خرید</span></a></div></div></div></div></div></div></div>
ESP65A;

echo strtr($ES_REGION, [
    '{ES_STATUS}'       => esc_html((string) $es_data['status']),
    '{ES_RATING_HTML}'  => (string) $es_data['rating'],
    '{ES_SKU}'          => esc_html($es_sku),
    '{ES_OLD_HTML}'     => (string) $es_data['old_html'],
    '{ES_PRICE_HTML}'   => (string) $es_data['price_html'],
    '{ES_MIN_HTML}'     => (string) $es_data['min_html'],
    '{ES_SUMMARY_HTML}' => (string) $es_data['summary'],
    '{ES_BTN_HTML}'     => (string) $es_data['btn'],
    '{ES_CART_URL}'     => esc_url(es_page_url('cart')),
    '{ES_PHONE_URL}'    => esc_url($es_phone_url),
    '{ES_PHONE_LINE}'   => esc_html('مشاوره: ' . $es_phone_display),
]);
