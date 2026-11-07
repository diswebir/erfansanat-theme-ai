<?php
/**
 * فرم تسویه‌حساب ووکامرس — برش بایت‌دقیق میرور checkout.html
 * فرگمنت‌ها با temp/gen_p610.py تولید شده‌اند.
 *
 * انحراف‌های مستند از میرور (استاندارد ووکامرس):
 *  ۱) form → name="checkout" class="checkout woocommerce-checkout" با action استاندارد
 *  ۲) نام فیلدها customer_* → billing_* / order_comments استاندارد + فیلد اجباری billing_last_name
 *  ۳) radioهای payment: value = اسلاگ درگاه (name=payment_method استاندارد حفظ شد)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_HERO = <<<'ESP6H'
<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34"><div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div><div class="light-dot-grid absolute inset-0 opacity-45"></div><div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div></div><div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8"><nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="{ES_HOME_URL}" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="{ES_SHOP_URL}" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">فروشگاه محصولات</a></span><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="cart.html" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">سبد خرید</a></span><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">تسویه‌حساب نهایی</span></span></nav><div class="pt-6"><div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs"><span class="h-2 w-2 rounded-full bg-emerald-600"></span><span>تسویه‌حساب نهایی و صدور پیش‌فاکتور رسمی</span></div><h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]">ثبت نهایی سفارش و پرداخت </h1><p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]">مشخصات تحویل‌گیرنده یا سازمان خود را وارد نمایید. امکان پرداخت مستقیم از طریق درگاه هوشمند بانک ملت یا دریافت پیش‌فاکتور رسمی مهرشده فراهم است.</p></div></div></section>
ESP6H;
$ES_RECEIPT = <<<'ESP6R'
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16"><div data-checkout-receipt="true" class="hidden mx-auto max-w-3xl rounded-3xl border border-emerald-200 bg-white p-8 shadow-lg lg:p-10"><div class="flex flex-col items-center text-center"><div class="grid h-16 w-16 place-items-center rounded-2xl bg-emerald-100 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-9 w-9" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg></div><span class="mt-4 rounded-full bg-emerald-50 px-3.5 py-1 text-xs font-extrabold text-emerald-800">سفارش شما با موفقیت در سیستم کارخانه ثبت شد</span><h2 class="mt-3 text-2xl font-black text-slate-950">رسید ثبت سفارش و پیش‌فاکتور رسمی</h2><p class="mt-2 text-sm text-slate-600">کارشناسان فروش سازمانی عرفان صنعت اصفهان ظرف کمتر از ۳۰ دقیقه در ساعات اداری جهت هماهنگی ارسال با شما تماس می‌گیرند.</p></div><div class="mt-8 grid grid-cols-2 gap-4 rounded-2xl border border-slate-200/80 bg-slate-50 p-5 sm:grid-cols-4"><div><div class="text-[11px] font-bold text-slate-400">کد پیگیری سفارش</div><div data-receipt-code="true" dir="ltr" class="mt-1 font-mono text-sm font-black text-emerald-800">{ES_CODE}</div></div><div><div class="text-[11px] font-bold text-slate-400">نام خریدار / سازمان</div><div data-receipt-customer="true" class="mt-1 text-xs font-extrabold text-slate-900">{ES_CUSTOMER}</div></div><div><div class="text-[11px] font-bold text-slate-400">روش تسویه انتخابی</div><div data-receipt-method="true" class="mt-1 text-xs font-extrabold text-slate-900">{ES_METHOD}</div></div><div><div class="text-[11px] font-bold text-slate-400">وضعیت گارانتی</div><div class="mt-1 text-xs font-extrabold text-emerald-700">۲۴ ماه تعویض + کد GS1</div></div></div><div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-6"><button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 hover:border-emerald-300 hover:text-emerald-800 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-printer h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"></path><rect x="6" y="14" width="12" height="8" rx="1"></rect></svg><span>چاپ پیش‌فاکتور / ذخیره PDF</span></button><div class="flex items-center gap-2.5"><a href="{ES_SHOP_URL}" class="rounded-xl bg-emerald-700 px-5 py-2.5 text-xs font-extrabold text-white hover:bg-emerald-800">بازگشت به فروشگاه محصولات</a></div></div></div>
ESP6R;
$ES_B1 = <<<'ESP6B1'
<form name="checkout" method="post" action="{ES_CHECKOUT_URL}" class="checkout woocommerce-checkout grid items-start gap-8 lg:grid-cols-12" novalidate><div class="space-y-6 lg:col-span-7"><div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs"><h2 class="mb-4 flex items-center gap-2 text-base font-black text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 lucide-building-2 h-5 w-5 text-emerald-700" aria-hidden="true"><path d="M10 12h4"></path><path d="M10 8h4"></path><path d="M14 21v-3a2 2 0 0 0-4 0v3"></path><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"></path><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"></path></svg><span>۱. نوع خریدار و صورت‌حساب</span></h2><div class="grid gap-3 sm:grid-cols-2"><label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-emerald-500 bg-emerald-50/50 p-4 transition-all"><input type="radio" class="mt-1 h-4 w-4 accent-emerald-700" name="buyer_type" checked="" value="حقیقی / پیمانکار"/><div><div class="text-xs font-black text-slate-900">خریدار حقیقی / پیمانکار اجرایی</div><div class="mt-1 text-[11px] leading-5 text-slate-600">ثبت و ارسال فوری سفارش از انبار کارخانه همراه با کارت گارانتی ۲۴ ماهه</div></div></label><label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50/60 p-4 transition-all hover:border-emerald-300"><input type="radio" class="mt-1 h-4 w-4 accent-emerald-700" name="buyer_type" value="حقوقی / شهرداری و سازمان دولتی"/><div><div class="text-xs font-black text-slate-900">شخص حقوقی / شهرداری و دهیاری (سامانه ستاد)</div><div class="mt-1 text-[11px] leading-5 text-slate-600">صدور فاکتور رسمی ارزش افزوده همراه با کد ملی کالا و کد GS1</div></div></label></div></div>
ESP6B1;
$ES_B2 = <<<'ESP6B2'
<div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs sm:p-7"><h2 class="mb-5 flex items-center gap-2 text-base font-black text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5 text-emerald-700" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg><span>۲. مشخصات تحویل‌گیرنده و مقصد ارسال</span></h2><div class="grid gap-4 sm:grid-cols-2"><div><label class="mb-1.5 block text-xs font-bold text-slate-700">نام و نام خانوادگی / نام سازمان *</label>{ES_F_NAME}</div><div><label class="mb-1.5 block text-xs font-bold text-slate-700">نام خانوادگی *</label>{ES_F_LAST}</div><div><label class="mb-1.5 block text-xs font-bold text-slate-700">شماره موبایل جهت هماهنگی باربری *</label>{ES_F_PHONE}</div><div><label class="mb-1.5 block text-xs font-bold text-slate-700">استان و شهر مقصد *</label>{ES_F_CITY}</div><div><label class="mb-1.5 block text-xs font-bold text-slate-700">شناسه ملی / کد اقتصادی (اختیاری - ویژه فاکتور رسمی)</label>{ES_F_TAX}</div><div class="sm:col-span-2"><label class="mb-1.5 block text-xs font-bold text-slate-700">نشانی دقیق محل تحویل پروژه یا انبار مقصد *</label>{ES_F_ADDR}</div><div class="sm:col-span-2"><label class="mb-1.5 block text-xs font-bold text-slate-700">توضیحات فنی سفارش (رنگ نور، متراژ سفارشی، ولتاژ یا نام باربری ترجیحی)</label>{ES_F_NOTES}</div></div></div>
ESP6B2;
$ES_B3 = <<<'ESP6B3'
<div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs sm:p-7"><h2 class="mb-4 flex items-center gap-2 text-base font-black text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card h-5 w-5 text-emerald-700" aria-hidden="true"><rect width="20" height="14" x="2" y="5" rx="2"></rect><line x1="2" x2="22" y1="10" y2="10"></line></svg><span>۳. انتخاب روش پرداخت یا صدور پیش‌فاکتور</span></h2><div class="space-y-3"><label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-emerald-500 bg-emerald-50/40 p-4"><input type="radio" class="mt-1 h-4 w-4 accent-emerald-700" name="payment_method" checked="" value="{ES_GW1_ID}"/><div class="flex-1"><div class="flex flex-wrap items-center justify-between gap-2"><span class="text-xs font-black text-slate-900">پرداخت آنلاین از طریق درگاه هوشمند بانک ملت</span><span class="rounded-md bg-emerald-700 px-2 py-0.5 text-[10px] font-bold text-white">تایید آنی سفارش</span></div><p class="mt-1 text-[11.5px] leading-5 text-slate-600">پرداخت امن با کلیه کارت‌های بانکی عضو شبکه شتاب (به پرداخت ملت) و ارسال مستقیم از انبار کارخانه.</p></div></label><label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50/60 p-4 hover:border-emerald-300"><input type="radio" class="mt-1 h-4 w-4 accent-emerald-700" name="payment_method" value="{ES_GW2_ID}"/><div class="flex-1"><div class="flex flex-wrap items-center justify-between gap-2"><span class="text-xs font-black text-slate-900">صدور پیش‌فاکتور رسمی و پرداخت پایا / ساتنا (ویژه سازمان‌ها و خرید عمده)</span><span class="rounded-md bg-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-700">همراه با کد GS1 و شناسه کالا</span></div><p class="mt-1 text-[11.5px] leading-5 text-slate-600">مناسب شهرداری‌ها، دهیاری‌ها و پیمانکاران جهت طرح در کمیسیون معاملات یا سامانه تدارکات الکترونیکی دولت (ستاد).</p></div></label></div></div>{ES_PAYMENT_HIDDEN}</div>
ESP6B3;
$ES_ASIDE = <<<'ESP6A'
<aside class="lg:col-span-5 lg:sticky lg:top-28"><div class="overflow-hidden rounded-3xl border border-emerald-200/80 bg-white shadow-[0_20px_50px_-24px_rgba(5,150,105,0.16)]"><div class="border-b border-slate-100 bg-emerald-50/60 px-6 py-4"><h2 class="text-sm font-black text-emerald-950">مرور اقلام سفارش شما</h2></div><div class="space-y-5 p-6"><div data-checkout-items-list="true" class="divide-y divide-slate-100">{ES_ITEMS}<div class="space-y-2 border-t border-slate-100 pt-4 text-xs"><div class="flex items-center justify-between text-slate-600"><span>جمع کل اقلام:</span><span data-checkout-total="true" class="text-base font-black text-emerald-800 tabular-nums">{ES_TOTAL}</span></div><div class="flex items-center justify-between text-slate-500"><span>زمان تحویل تخمینی:</span><span class="font-bold text-slate-700">۲۴ تا ۴۸ ساعت کاری</span></div></div><button type="submit" class="btn-primary w-full justify-center py-3.5 text-sm cursor-pointer whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock h-4 w-4 shrink-0" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg><span>ثبت قطعی سفارش و دریافت کد پیگیری</span></button><div class="grid grid-cols-2 gap-2.5 pt-1 text-[11px] font-semibold text-slate-500"><div class="flex items-center gap-1.5 rounded-xl bg-slate-50 p-2.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg><span>گارانتی ۲۴ ماهه تعویض</span></div><div class="flex items-center gap-1.5 rounded-xl bg-slate-50 p-2.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check2 lucide-file-check-2 h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="m3 15 2 2 4-4"></path></svg><span>فاکتور رسمی سامانه ستاد</span></div></div></div></div></aside></form></section>
ESP6A;
if (!function_exists('WC')) {
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
