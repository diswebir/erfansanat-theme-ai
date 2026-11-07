<?php
/**
 * سبد خرید ووکامرس — برش بایت‌دقیق میرور cart.html
 * فرگمنت‌ها با temp/gen_p69.py تولید شده‌اند.
 *
 * انحراف‌های مستند از میرور (الزام فرم استاندارد ووکامرس — بدون JS دستی):
 *  ۱) span[data-cart-row-qty] → input number با name="cart[key][qty]" + دکمه update_cart
 *  ۲) دکمه‌های حذف/بازنشانی/افزودن سریع → لینک‌های استاندارد ووکامرس (data-hook حفظ شده)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_HERO = <<<'ESP69H'
<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34"><div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div><div class="light-dot-grid absolute inset-0 opacity-45"></div><div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div></div><div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8"><nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="{ES_HOME_URL}" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="{ES_SHOP_URL}" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">فروشگاه محصولات</a></span><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">سبد خرید</span></span></nav><div class="pt-6"><div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs"><span class="h-2 w-2 rounded-full bg-emerald-600"></span><span>فروشگاه رسمی ووکامرس · خرید مستقیم از کارخانه</span></div><h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]">سبد خرید و لیست پیش‌فاکتور </h1><p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]">اقلام انتخابی خود را بررسی کنید. امکان پرداخت آنلاین از طریق درگاه هوشمند بانک ملت و یا صدور پیش‌فاکتور رسمی سازمانی (ویژه شهرداری‌ها و پیمانکاران) فراهم است.</p></div></div></section>
ESP69H;
$ES_HEAD = <<<'ESP69A'
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16"><div class="mb-10 grid grid-cols-3 gap-3 rounded-2xl border border-emerald-100 bg-white p-4 shadow-2xs sm:gap-6 sm:p-5"><div class="flex items-center gap-3"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-emerald-700 text-xs font-black text-white">۱</span><div><div class="text-xs font-black text-emerald-950 sm:text-sm">۱. بررسی سبد خرید</div><div class="hidden text-[11px] font-medium text-slate-500 sm:block">انتخاب تعداد، متراژ و ویژگی‌های فنی</div></div></div><a href="checkout.html" class="flex items-center gap-3 opacity-75 transition-opacity hover:opacity-100"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-slate-200 bg-slate-50 text-xs font-black text-slate-600">۲</span><div><div class="text-xs font-bold text-slate-700 sm:text-sm">۲. اطلاعات ارسال و نوع فاکتور</div><div class="hidden text-[11px] font-medium text-slate-400 sm:block">خرید آنلاین یا درخواست پیش‌فاکتور رسمی</div></div></a><div class="flex items-center gap-3 opacity-60"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-slate-200 bg-slate-50 text-xs font-black text-slate-500">۳</span><div><div class="text-xs font-bold text-slate-600 sm:text-sm">۳. تایید نهایی و کد پیگیری</div><div class="hidden text-[11px] font-medium text-slate-400 sm:block">ارسال از انبار کارخانه با باربری بیمه</div></div></div></div><div class="grid items-start gap-8 lg:grid-cols-12"><div class="space-y-4 lg:col-span-8"><div class="flex flex-wrap items-center justify-between gap-2 rounded-2xl border border-slate-200/80 bg-white px-5 py-3.5 shadow-2xs"><div class="flex items-center gap-2 text-sm font-black text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag h-4.5 w-4.5 text-emerald-700" aria-hidden="true"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg><span>اقلام موجود در سبد سفارش شما</span><span data-cart-page-count="true" class="rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-extrabold text-emerald-800">{ES_COUNT_LINE}</span></div><a href="{ES_RESET_URL}" data-cart-reset-default="true" class="text-xs font-bold text-emerald-700 hover:underline cursor-pointer">بازنشانی اقلام نمونه کارخانه</a></div><div data-cart-items-container="true" class="space-y-4"><form action="{ES_CART_URL}" method="post" class="woocommerce-cart-form">
ESP69A;
$ES_ROW = <<<'ESP69R'
<div data-cart-row="true" data-cart-slug="{ES_SLUG}" data-cart-title="{ES_TITLE}" data-cart-price="{ES_PRICE_ATTR}" data-cart-qty="{ES_QTY_ATTR}" data-cart-min="{ES_MIN_ATTR}" data-cart-step="{ES_STEP_ATTR}" class="flex flex-col justify-between gap-5 rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs transition-all hover:border-emerald-200 sm:flex-row sm:items-center"><div class="flex items-start gap-4"><a href="{ES_URL}" class="h-22 w-22 shrink-0 overflow-hidden rounded-2xl border border-slate-100 bg-slate-100">{ES_IMG_TAG}</a><div><div class="flex flex-wrap items-center gap-2 text-[11px] font-bold text-slate-400">{ES_META}</div><a href="{ES_URL}" class="mt-1 block text-base font-extrabold text-slate-900 hover:text-emerald-700">{ES_TITLE}</a>{ES_VARIANT}{ES_MIN_CHIP}</div></div><div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-3 sm:flex-col sm:items-end sm:border-t-0 sm:pt-0"><div class="flex items-center gap-2"><div class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 p-1"><button type="button" data-cart-qty-minus="true" aria-label="کاهش تعداد" class="grid h-8 w-8 place-items-center rounded-lg bg-white text-slate-700 shadow-2xs transition-colors hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-minus h-3.5 w-3.5" aria-hidden="true"><path d="M5 12h14"></path></svg></button><input type="number" data-cart-row-qty="true" name="cart[{ES_KEY}][qty]" value="{ES_QTY_ATTR}" min="{ES_MIN_ATTR}" max="" step="{ES_STEP_ATTR}" inputmode="numeric" aria-label="تعداد" class="min-w-12 bg-transparent px-2 text-center font-mono text-sm font-black text-slate-900 tabular-nums outline-none"/><button type="button" data-cart-qty-plus="true" aria-label="افزایش تعداد" class="grid h-8 w-8 place-items-center rounded-lg bg-white text-slate-700 shadow-2xs transition-colors hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-3.5 w-3.5" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg></button></div><a href="{ES_REMOVE_URL}" data-cart-row-remove="true" title="حذف از سبد" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 text-slate-400 transition-colors hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 lucide-trash-2 h-4 w-4" aria-hidden="true"><path d="M10 11v6"></path><path d="M14 11v6"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a></div><div class="text-left"><div class="text-[11px] font-medium text-slate-400">فی واحد: {ES_UNIT}</div><div data-cart-row-total="true" class="text-sm font-black text-emerald-800 tabular-nums sm:text-base">{ES_ROW_TOTAL}</div></div></div></div>
ESP69R;
$ES_MID = <<<'ESP69M'
<div custom="0.08"><div class="mt-8 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-2xs"><div class="mb-4 flex items-center justify-between"><div class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg><span>افزودن سریع تجهیزات مکمل به سفارش</span></div><a href="{ES_SHOP_URL}" class="text-xs font-bold text-emerald-700 hover:underline">مشاهده تمام محصولات ←</a></div><div class="grid gap-3 sm:grid-cols-3">{ES_QA_ITEMS}</div></div></div></div>
ESP69M;
$ES_QA = <<<'ESP69Q'
<div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-3"><div class="min-w-0"><a href="{ES_URL}" class="block truncate text-xs font-extrabold text-slate-800 hover:text-emerald-700">{ES_TITLE}</a><div class="mt-0.5 text-[11px] font-bold text-emerald-700">{ES_PRICE}</div></div><a href="{ES_ADD_URL}" data-quick-add-cart="true" data-product-slug="{ES_SLUG}" data-product-title="{ES_TITLE}" data-product-price="{ES_PRICE_ATTR}" data-product-img="{ES_IMG}" data-product-min="{ES_MIN}" data-product-step="{ES_STEP}" class="shrink-0 rounded-xl bg-emerald-700 px-3 py-1.5 text-[11px] font-extrabold text-white transition-colors hover:bg-emerald-800 cursor-pointer whitespace-nowrap">+ افزودن</a></div>
ESP69Q;
$ES_ASIDE = <<<'ESP69S'
<aside class="lg:col-span-4 lg:sticky lg:top-28"><div class="overflow-hidden rounded-3xl border border-emerald-200/80 bg-white shadow-[0_20px_50px_-24px_rgba(5,150,105,0.16)]"><div class="border-b border-slate-100 bg-emerald-50/60 px-6 py-4"><h2 class="text-sm font-black text-emerald-950">خلاصه صورت‌حساب و شرایط ارسال</h2></div><div class="space-y-4 p-6"><div class="space-y-2.5 text-xs"><div class="flex items-center justify-between text-slate-600"><span>جمع کل اقلام قیمت‌دار:</span><span data-cart-subtotal="true" class="font-extrabold text-slate-900 tabular-nums">{ES_SUBTOTAL}</span></div><div class="flex items-center justify-between text-slate-600"><span>بسته‌بندی صنعتی و بیمه بار:</span><span class="font-bold text-emerald-700">رایگان (کارخانه)</span></div><div class="flex items-center justify-between text-slate-600"><span>شناسه کالا و کد رهگیری GS1:</span><span class="font-bold text-slate-800">دارای کد رسمی سامانه ستاد</span></div></div><div class="border-t border-slate-100 pt-4"><div class="flex items-baseline justify-between"><span class="text-xs font-extrabold text-slate-800">مبلغ قابل پرداخت:</span><span data-cart-grand-total="true" class="text-xl font-black text-emerald-800 tabular-nums">{ES_GRAND}</span></div><p class="mt-1.5 text-[11px] leading-5 text-slate-500">امکان پرداخت آنلاین با کلیه کارت‌های عضو شتاب یا دریافت پیش‌فاکتور رسمی با مهر و امضای شرکت دانش‌بنیان عرفان صنعت اصفهان.</p></div><div class="space-y-2.5 pt-2"><a href="{ES_CHECKOUT_URL}" class="btn-primary w-full justify-center py-3.5 text-sm whitespace-nowrap"><span>ادامه جهت تسویه‌حساب / صدور پیش‌فاکتور</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4 shrink-0" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a><a href="tel:031-91091011" class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 py-3 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:text-emerald-800 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span>مشاوره خرید تلفنی: ۰۳۱ - ۹۱۰۹۱۰۱۱</span></a></div><div class="space-y-2 border-t border-slate-100 pt-4 text-[11.5px] font-semibold text-slate-600"><div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg><span>۲۴ ماه گارانتی تعویض بی‌قید و شرط کارخانه</span></div><div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg><span>۶۰ ماه خدمات پس از فروش و تامین قطعات</span></div><div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg><span>ارسال سریع به تمام شهرهای ایران و عراق</span></div><div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg><span>ارائه فاکتور رسمی مورد تایید سامانه تدارکات دولت</span></div></div></div></div></aside>
ESP69S;
$ES_FOOT = <<<'ESP69F'
</div></section>
ESP69F;
$ES_EMPTY = <<<'ESP69E'
<div data-cart-empty="true" class="rounded-3xl border border-dashed border-slate-300 bg-slate-50/70 px-6 py-16 text-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto h-10 w-10 text-slate-300" aria-hidden="true"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg><p class="mt-4 text-[14.5px] font-extrabold text-slate-700">سبد خرید شما خالی است.</p><p class="mt-1.5 text-[12.5px] text-slate-500">محصولات تولیدی کارخانه را مرور کنید و اقلام سفارش خود را اضافه نمایید.</p><a href="{ES_SHOP_URL}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-bold text-white hover:bg-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg><span>بازگشت به فروشگاه محصولات</span></a></div>
ESP69E;
if (!function_exists('WC')) {
    return; // بدون ووکامرس چیزی رندر نمی‌شود
}
$es_cart = WC()->cart ?? null;
if (!$es_cart) {
    return;
}

do_action('woocommerce_before_cart');

// اعلان‌های ووکامرس با استایل میرور
$es_notices = function_exists('wc_get_notices') ? (array) wc_get_notices() : [];
foreach ($es_notices as $es_type => $es_list) {
    $es_cls = $es_type === 'error'
        ? 'rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700'
        : 'rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-800';
    foreach ((array) $es_list as $es_notice) {
        $es_msg = is_array($es_notice) ? (string) ($es_notice['notice'] ?? '') : (string) $es_notice;
        echo '<div class="woocommerce-' . esc_attr((string) $es_type) . ' ' . $es_cls . '" role="alert">' . esc_html($es_msg) . '</div>';
    }
}
if ($es_notices !== [] && function_exists('wc_clear_notices')) {
    wc_clear_notices();
}

// حالت سبد خالی
// کلیدهای سبد ووکامرس باید حفظ شوند (array_values ممنوع)
$es_items = array_filter((array) $es_cart->get_cart(), 'is_array');
if ($es_items === []) {
    echo strtr($ES_EMPTY, [
        '{ES_SHOP_URL}' => esc_url(es_page_url('shop')),
    ]);
    do_action('woocommerce_after_cart');
    return;
}

// هیرو (breadcrumb + عنوان)
echo strtr($ES_HERO, [
    '{ES_HOME_URL}' => esc_url(home_url('/')),
    '{ES_SHOP_URL}' => esc_url(es_page_url('shop')),
]);

// سربرگ گرید (شمارنده + بازنشانی) + شروع فرم استاندارد
$es_reset_url = function_exists('wc_get_cart_url') ? add_query_arg('empty-cart', '1', wc_get_cart_url()) : home_url('/cart/?empty-cart=1');
echo strtr($ES_HEAD, [
    '{ES_COUNT_LINE}'   => esc_html(es_fa_num((string) $es_cart->get_cart_contents_count()) . ' کالا'),
    '{ES_RESET_URL}'    => esc_url($es_reset_url),
    '{ES_CART_URL}'     => esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')),
]);

// ردیف‌های سبد با هوک‌های استاندارد woocommerce_cart_item_*
foreach ($es_items as $es_key => $es_item) {
    $_product = $es_item['data'] ?? null;
    if (!is_object($_product) && function_exists('wc_get_product')) {
        $_product = wc_get_product((int) ($es_item['product_id'] ?? 0));
    }
    if (!is_object($_product)) {
        continue;
    }
    $es_pid  = (int) ($es_item['product_id'] ?? 0);
    $es_qty  = max(1, (int) ($es_item['quantity'] ?? 1));
    $es_min  = max(1, (int) $_product->get_min_purchase_quantity());
    $es_step = max(1, (int) apply_filters('woocommerce_quantity_input_step', 1, $_product));
    $es_url  = method_exists($_product, 'get_permalink') ? (string) $_product->get_permalink() : (string) get_permalink($es_pid);
    $es_name = apply_filters('woocommerce_cart_item_name', (string) $_product->get_name(), $es_item, $es_key);
    $es_img_tag = apply_filters('woocommerce_cart_item_thumbnail', '', $es_item, $es_key);
    if ($es_img_tag === '') {
        $es_thumb = (int) get_post_meta($es_pid, '_thumbnail_id', true);
        $es_img_url = es_media_url($es_thumb > 0 ? $es_thumb : 'assets/images/light-tunnel.jpg');
        $es_img_tag = '<img src="' . esc_url($es_img_url) . '" alt="' . esc_attr($es_name) . '" class="h-full w-full object-cover"/>';
    }
    $es_price    = apply_filters('woocommerce_cart_item_price', (string) WC()->cart->get_product_price($_product), $es_item, $es_key);
    $es_subtotal = apply_filters('woocommerce_cart_item_subtotal', (string) WC()->cart->get_product_subtotal($_product, $es_qty), $es_item, $es_key);

    // خط متا: دسته · SKU · IP
    $es_cat = '';
    foreach ((array) wp_get_post_terms($es_pid, 'product_cat') as $es_t) {
        if (is_object($es_t) && ($es_t->name ?? '') !== '') {
            $es_cat = (string) $es_t->name;
            break;
        }
    }
    $es_ip = '';
    foreach ((array) wp_get_post_terms($es_pid, 'pa_ip-rating') as $es_t) {
        if (is_object($es_t) && ($es_t->name ?? '') !== '') {
            $es_ip = (string) $es_t->name;
            break;
        }
    }
    $es_sku = (string) $_product->get_sku();
    $es_meta = ($es_cat !== '' ? '<span>' . esc_html($es_cat) . '</span><span>·</span>' : '')
        . '<span dir="ltr" class="font-mono">SKU: ' . esc_html($es_sku) . '</span>'
        . ($es_ip !== '' ? '<span>·</span><span class="text-emerald-700">' . esc_html($es_ip) . '</span>' : '');

    $es_variant = (string) apply_filters('es_cart_item_variant', (string) ($es_item['es_variant'] ?? ''), $es_item, $es_key);
    $es_variant_html = $es_variant !== ''
        ? '<div class="mt-1 text-xs font-semibold text-slate-500">ویژگی انتخابی: <span class="text-slate-700">' . esc_html($es_variant) . '</span></div>'
        : '';
    $es_remove = function_exists('wc_get_cart_remove_url') ? wc_get_cart_remove_url($es_key) : '';

    echo strtr($ES_ROW, [
        '{ES_SLUG}'       => esc_attr((string) ($_product->get_slug() ?? '')),
        '{ES_TITLE}'      => $es_name,
        '{ES_PRICE_ATTR}' => esc_attr((string) ($_product->get_price() ?? '0')),
        '{ES_QTY_ATTR}'   => (string) $es_qty,
        '{ES_MIN_ATTR}'   => (string) $es_min,
        '{ES_STEP_ATTR}'  => (string) $es_step,
        '{ES_URL}'        => esc_url($es_url),
        '{ES_IMG_TAG}'    => $es_img_tag,
        '{ES_META}'       => $es_meta,
        '{ES_VARIANT}'    => $es_variant_html,
        '{ES_MIN_CHIP}'   => '<div class="mt-1.5 inline-block rounded-lg bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-800">' . esc_html('حداقل سفارش: ' . es_fa_num((string) $es_min) . ' · گام افزایش: مضرب ' . es_fa_num((string) $es_step)) . '</div>',
        '{ES_REMOVE_URL}' => esc_url($es_remove),
        '{ES_KEY}'        => esc_attr((string) $es_key),
        '{ES_UNIT}'       => $es_price,
        '{ES_ROW_TOTAL}'  => $es_subtotal,
    ]);
}
echo '</div>';
wp_nonce_field('woocommerce-cart');
echo '<button type="submit" name="update_cart" value="1" class="hidden" aria-label="بروزرسانی سبد خرید" tabindex="-1"></button>';
echo '</form>';

// افزودن سریع مکمل‌ها (cross-sell → fallback جدیدترین محصولات)
$es_cross = array_slice(array_map('intval', (array) $es_cart->get_cross_sells()), 0, 3);
if ($es_cross === []) {
    $es_in_cart = array_map(static fn($row): int => (int) ($row['product_id'] ?? 0), $es_items);
    $es_cross = array_map(static fn($p): int => (int) ($p->ID ?? 0), (array) get_posts([
        'post_type'      => 'product',
        'posts_per_page' => 3,
        'post__not_in'   => $es_in_cart,
    ]));
}
$es_qa = '';
foreach ($es_cross as $es_cid) {
    if ($es_cid < 1) {
        continue;
    }
    $es_cp = function_exists('wc_get_product') ? wc_get_product($es_cid) : null;
    if (!is_object($es_cp)) {
        continue;
    }
    $es_cthumb = (int) get_post_meta($es_cid, '_thumbnail_id', true);
    $es_qa .= strtr($ES_QA, [
        '{ES_URL}'        => esc_url((string) get_permalink($es_cid)),
        '{ES_TITLE}'      => esc_html((string) get_the_title($es_cid)),
        '{ES_PRICE}'      => esc_html((string) $es_cp->get_price_html()),
        '{ES_SLUG}'       => esc_attr((string) ($es_cp->get_slug() ?? '')),
        '{ES_PRICE_ATTR}' => esc_attr((string) ($es_cp->get_price() ?? '0')),
        '{ES_IMG}'        => esc_attr(es_media_url($es_cthumb > 0 ? $es_cthumb : 'assets/images/light-tunnel.jpg')),
        '{ES_MIN}'        => esc_attr((string) max(1, (int) $es_cp->get_min_purchase_quantity())),
        '{ES_STEP}'       => esc_attr((string) max(1, (int) apply_filters('woocommerce_quantity_input_step', 1, $es_cp))),
        '{ES_ADD_URL}'    => esc_url((string) (function_exists('wc_get_cart_url') ? wc_get_cart_url() : '')) . '?add-to-cart=' . absint($es_cid),
    ]);
}
echo strtr($ES_MID, [
    '{ES_QA_ITEMS}' => $es_qa,
    '{ES_SHOP_URL}' => esc_url(es_page_url('shop')),
]);

// خلاصه صورت‌حساب
echo strtr($ES_ASIDE, [
    '{ES_SUBTOTAL}'    => esc_html((string) apply_filters('woocommerce_cart_subtotal', (string) $es_cart->get_cart_subtotal(), false, $es_cart)),
    '{ES_GRAND}'       => esc_html((string) $es_cart->get_total()),
    '{ES_CHECKOUT_URL}' => esc_url((string) (function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '')),
]);
echo $ES_FOOT;
do_action('woocommerce_after_cart');
