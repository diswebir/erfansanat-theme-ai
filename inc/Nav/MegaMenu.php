<?php
/**
 * مگامنو دسکتاپ — بازسازی ساختار nav میرور با داده داینامیک
 *
 * منبع مارک‌آپ: temp/phase-04-header-footer-navigation/refs/header-ref.html (فرگمنت‌های byte-exact)
 * جایگزینی‌ها فقط در مقادیر href/عنوان/آیکون/شمارنده‌ها هستند — ساختار DOM بدون تغییر.
 *
 * منابع داده:
 *  ۱) منوی وردپرس (main-mega-menu) با کنوانسیون کلاس‌ها:
 *     es-mega-products / es-mega-projects / es-mega-services (آیتم سطح اول)
 *     es-mega-tab + es-icon-{lucide} (تب‌های محصولات)
 *  ۲) fallback تاکسونومی‌محور: product_cat (تب‌ها) + project_type/city (پروژه‌ها) + CPT services
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Nav;

final class MegaMenu {

    /** آیکون‌های تب محصولات (lucide، عین میرور) */
    private const ICONS = [
        'chevron-down' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>',
        'cpu' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cpu h-4 w-4" aria-hidden="true"><path d="M12 20v2"></path><path d="M12 2v2"></path><path d="M17 20v2"></path><path d="M17 2v2"></path><path d="M2 12h2"></path><path d="M2 17h2"></path><path d="M2 7h2"></path><path d="M20 12h2"></path><path d="M20 17h2"></path><path d="M20 7h2"></path><path d="M7 20v2"></path><path d="M7 2v2"></path><rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="8" y="8" width="8" height="8" rx="1"></rect></svg>',
        'chevron-left' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-emerald-700" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>',
        'sparkles' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-4 w-4" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>',
        'trees' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trees h-4 w-4" aria-hidden="true"><path d="M10 10v.2A3 3 0 0 1 8.9 16H5a3 3 0 0 1-1-5.8V10a3 3 0 0 1 6 0Z"></path><path d="M7 16v6"></path><path d="M13 19v3"></path><path d="M12 19h8.3a1 1 0 0 0 .7-1.7L18 14h.3a1 1 0 0 0 .7-1.7L16 9h.2a1 1 0 0 0 .8-1.7L13 3l-1.4 1.5"></path></svg>',
        'waves' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-waves h-4 w-4" aria-hidden="true"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path></svg>',
        'arrow-up-left' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3.5 w-3.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg>',
        'arrow-left' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3 w-3 shrink-0 text-slate-300 transition-transform group-hover/cat:-translate-x-0.5 group-hover/cat:text-emerald-700" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>',
        'shopping-bag' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg>',
        'layers' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layers h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"></path><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"></path><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"></path></svg>',
        'map-pin' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>',
        'file-check2' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check2 lucide-file-check-2 h-4 w-4" aria-hidden="true"><path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="m3 15 2 2 4-4"></path></svg>',
        'award' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-4 w-4" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg>',
        'shield-check' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>',
        'circle-question-mark' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-question-mark h-4 w-4" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><path d="M12 17h.01"></path></svg>',
        'building2' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 lucide-building-2 h-4 w-4" aria-hidden="true"><path d="M10 12h4"></path><path d="M10 8h4"></path><path d="M14 21v-3a2 2 0 0 0-4 0v3"></path><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"></path><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"></path></svg>',
        'phone-call' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>',
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>',
        'menu' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-4.5 w-4.5 text-emerald-700 sm:h-5 sm:w-5" aria-hidden="true"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg>',
        'x' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-4.5 w-4.5" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>',
    ];

    /** ترتیب آیکون‌های fallback تاکسونومی */
    private const FALLBACK_ICONS = ['cpu', 'sparkles', 'trees', 'waves'];

    private const F_HOME = <<<'ESFRAG9Z'
<a href="{ES_HOME_URL}" class="rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap {ES_HOME_CLASS}">صفحه اصلی</a>
ESFRAG9Z;

    private const F_P_HEAD = <<<'ESFRAG9Z'
<div class="group static" data-mega-menu-products="true"><button type="button" data-desktop-mega-trigger="products" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700"><span>محصولات</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg></button><div data-desktop-mega-panel="products" class="absolute inset-x-4 top-full z-50 mx-auto max-w-[780px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"><div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><div class="grid grid-cols-12"><div class="col-span-5 border-l border-slate-100 bg-slate-50/70 p-3.5"><div class="mb-2 px-2 text-[10.5px] font-extrabold text-slate-400">دسته‌بندی تولیدات ({ES_TOTAL} گروه)</div><div class="space-y-1">
ESFRAG9Z;

    private const F_TAB_ACTIVE = <<<'ESFRAG9Z'
<button type="button" data-mega-prod-tab="{ES_I}" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all cursor-pointer bg-white text-emerald-950 shadow-2xs ring-1 ring-emerald-200"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg transition-colors bg-emerald-700 text-white">{ES_ICON}</span><span class="min-w-0 flex-1 truncate text-xs font-extrabold">{ES_TITLE}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-emerald-700" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg></button>
ESFRAG9Z;

    private const F_TAB_IDLE = <<<'ESFRAG9Z'
<button type="button" data-mega-prod-tab="{ES_I}" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all cursor-pointer text-slate-700 hover:bg-white/70"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg transition-colors bg-emerald-50 text-emerald-700">{ES_ICON}</span><span class="min-w-0 flex-1 truncate text-xs font-extrabold">{ES_TITLE}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg></button>
ESFRAG9Z;

    private const F_P_MID = <<<'ESFRAG9Z'
</div></div><div class="col-span-7 flex flex-col justify-between p-4">
ESFRAG9Z;

    private const F_PANEL = <<<'ESFRAG9Z'
<div data-mega-prod-panel="{ES_I}" class="{ES_PANEL_CLASS}"><div class="mb-3 flex items-center justify-between border-b border-slate-100 pb-2.5"><a href="{ES_VIEW_ALL}" class="text-xs font-black text-slate-900 hover:text-emerald-700">{ES_TITLE}</a><a href="{ES_VIEW_ALL}" class="inline-flex items-center gap-1 text-[11px] font-extrabold text-emerald-700 hover:text-emerald-800"><span>مشاهده همه</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3.5 w-3.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div><div class="grid grid-cols-2 gap-1.5">{ES_CATS}</div></div>
ESFRAG9Z;

    private const F_CAT = <<<'ESFRAG9Z'
<a href="{ES_HREF}" class="group/cat flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-950"><span class="truncate">{ES_LABEL}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3 w-3 shrink-0 text-slate-300 transition-transform group-hover/cat:-translate-x-0.5 group-hover/cat:text-emerald-700" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a>
ESFRAG9Z;

    private const F_P_BOTTOM = <<<'ESFRAG9Z'
<div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-[11px]"><a href="{ES_SHOP_Q}" class="inline-flex items-center gap-1.5 font-extrabold text-emerald-800 hover:text-emerald-950"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg><span>فروشگاه آنلاین (ارسال سریع)</span></a><a href="{ES_SHOP}" class="inline-flex items-center gap-1 rounded-lg bg-emerald-700 px-3 py-1.5 font-extrabold text-white hover:bg-emerald-800"><span>کاتالوگ کامل ({ES_PRODUCTS_TOTAL} محصول)</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3 w-3" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a></div>
ESFRAG9Z;

    private const F_P_TAIL = <<<'ESFRAG9Z'
</div></div></div></div></div>
ESFRAG9Z;

    private const F_PR_HEAD = <<<'ESFRAG9Z'
<div class="group static" data-mega-menu-projects="true"><button type="button" data-desktop-mega-trigger="projects" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700"><span>پروژه‌ها</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg></button><div data-desktop-mega-panel="projects" class="absolute inset-x-4 top-full z-50 mx-auto max-w-[740px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"><div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><div class="grid grid-cols-12"><div class="col-span-4 flex flex-col justify-between border-l border-slate-100 bg-slate-50/70 p-3.5"><div><div class="mb-2 px-1 text-[10.5px] font-extrabold text-slate-400">تفکیک پروژه‌ها</div><div class="space-y-1">
ESFRAG9Z;

    private const F_PR_TAB_TYPE = <<<'ESFRAG9Z'
<button type="button" data-mega-proj-tab="type" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all cursor-pointer bg-white text-emerald-950 shadow-2xs ring-1 ring-emerald-200"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layers h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"></path><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"></path><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"></path></svg><span class="flex-1 text-xs font-extrabold">بر اساس نوع سازه</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg></button>
ESFRAG9Z;

    private const F_PR_TAB_CITY = <<<'ESFRAG9Z'
<button type="button" data-mega-proj-tab="city" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all cursor-pointer text-slate-700 hover:bg-white/70"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg><span class="flex-1 text-xs font-extrabold">بر اساس شهر اجرا</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
ESFRAG9Z;

    private const F_PR_MID = <<<'ESFRAG9Z'
</button></div></div><a href="{ES_ALL_URL}" class="mt-3 flex items-center justify-center gap-1.5 rounded-xl bg-emerald-700 px-3 py-2 text-[11.5px] font-extrabold text-white hover:bg-emerald-800"><span>همه پروژه‌ها</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a></div><div class="col-span-8 p-4">
ESFRAG9Z;

    private const F_PR_PANEL_TYPE = <<<'ESFRAG9Z'
<div data-mega-proj-panel="type" class="block"><div class="mb-2.5 text-xs font-black text-slate-900">آرشیو پروژه‌ها بر اساس نوع سازه نوری</div><div class="grid grid-cols-2 gap-1.5">{ES_LINKS}</div></div>
ESFRAG9Z;

    private const F_PR_PANEL_CITY = <<<'ESFRAG9Z'
<div data-mega-proj-panel="city" class="hidden"><div class="mb-2.5 text-xs font-black text-slate-900">آرشیو پروژه‌ها بر اساس شهر محل اجرا</div><div class="grid grid-cols-3 gap-1.5">{ES_LINKS}
ESFRAG9Z;

    private const F_PT = <<<'ESFRAG9Z'
<a href="{ES_HREF}" class="group/pt flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-950"><span class="truncate">{ES_LABEL}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3 w-3 shrink-0 text-slate-300 group-hover/pt:text-emerald-700" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a>
ESFRAG9Z;

    private const F_PC = <<<'ESFRAG9Z'
<a href="{ES_HREF}" class="group/pc flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-950"><span class="truncate">{ES_LABEL}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3 w-3 shrink-0 text-slate-300 group-hover/pc:text-emerald-700" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a>
ESFRAG9Z;

    private const F_PR_TAIL = <<<'ESFRAG9Z'
</div></div></div></div></div></div></div>
ESFRAG9Z;

    private const F_SV_HEAD = <<<'ESFRAG9Z'
<div class="group relative"><button type="button" data-desktop-mega-trigger="services" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700"><span>خدمات</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg></button><div data-desktop-mega-panel="services" class="absolute right-0 top-full z-50 w-[340px] pt-2.5 transition-all duration-200 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"><div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-2 shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><div class="mb-1 flex items-center justify-between px-2.5 py-1.5"><span class="text-[11px] font-black text-slate-400">خدمات مهندسی و اجرایی</span><a href="{ES_ALL_URL}" class="text-[11px] font-extrabold text-emerald-700 hover:underline">همه خدمات ←</a></div><div class="space-y-1">
ESFRAG9Z;

    private const F_SV = <<<'ESFRAG9Z'
<a href="{ES_HREF}" class="group/srv flex items-center justify-between rounded-xl px-3 py-2 text-xs font-extrabold text-slate-700 transition-colors hover:bg-emerald-50/70 hover:text-emerald-900"><span class="truncate">{ES_LABEL}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 shrink-0 text-slate-300 group-hover/srv:text-emerald-700" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a>
ESFRAG9Z;

    private const F_SV_TAIL = <<<'ESFRAG9Z'
</div></div></div></div>
ESFRAG9Z;

    private const F_CORP = <<<'ESFRAG9Z'
<div class="group static" data-mega-menu-corporate="true"><button type="button" data-desktop-mega-trigger="corporate" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700"><span>درباره شرکت</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg></button><div data-desktop-mega-panel="corporate" class="absolute inset-x-4 top-full z-50 mx-auto max-w-[740px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"><div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><div class="grid grid-cols-12"><div class="col-span-5 flex flex-col justify-between border-l border-slate-100 bg-gradient-to-b from-emerald-50/75 via-emerald-50/30 to-slate-50/60 p-5"><div><div class="inline-flex items-center gap-2 rounded-xl border border-emerald-200/80 bg-white px-3 py-1 text-[10.5px] font-black text-emerald-800 shadow-2xs"><span class="h-2 w-2 rounded-full bg-emerald-600"></span><span>تاسیس ۱۳۸۰ · شرکت دانش‌بنیان</span></div><div class="mt-3.5 text-sm font-black text-slate-900">شرکت دانش‌بنیان عرفان صنعت اصفهان</div><p class="mt-1.5 text-[11.5px] leading-6 font-medium text-slate-600">طراح و تولیدکننده تخصصی المان‌های نوری شهری، درختان هوشمند، ریسه‌های ضدآب IP68 و سازه‌های خورشیدی در ایران.</p><div class="mt-4 grid grid-cols-2 gap-2"><div class="rounded-xl border border-emerald-100 bg-white/90 px-2.5 py-2 text-center"><div class="text-xs font-black text-emerald-800 tabular-nums">+۸۵۰</div><div class="text-[10px] font-bold text-slate-500">پروژه شهری</div></div><div class="rounded-xl border border-emerald-100 bg-white/90 px-2.5 py-2 text-center"><div class="text-xs font-black text-emerald-800 tabular-nums">۲۴ ماه</div><div class="text-[10px] font-bold text-slate-500">گارانتی تعویض</div></div></div></div><div class="mt-4 border-t border-emerald-100/80 pt-3"><a href="{ES_URL_ABOUT}" class="inline-flex w-full items-center justify-between rounded-xl bg-emerald-700 px-3.5 py-2.5 text-xs font-extrabold text-white transition-colors hover:bg-emerald-800"><span>معرفی کامل کارخانه و خطوط تولید</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a></div></div><div class="col-span-7 p-4"><div class="mb-2.5 flex items-center justify-between border-b border-slate-100 px-1 pb-2"><span class="text-[11px] font-black text-slate-400">بخش‌های سازمانی، مجوزها و پشتیبانی</span><span class="font-mono text-[10px] font-bold text-emerald-700">ISO 9001:2015 · GS1</span></div><div class="grid grid-cols-2 gap-2"><a href="{ES_URL_ABOUT}" class="group/corp flex items-start gap-2.5 rounded-2xl border p-2.5 transition-all border-slate-100 bg-slate-50/50 text-slate-800 hover:border-emerald-200 hover:bg-white hover:shadow-2xs"><span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 transition-colors group-hover/corp:bg-emerald-700 group-hover/corp:text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check2 lucide-file-check-2 h-4 w-4" aria-hidden="true"><path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="m3 15 2 2 4-4"></path></svg></span><div class="min-w-0 flex-1"><div class="truncate text-xs font-extrabold text-slate-900 group-hover/corp:text-emerald-800">درباره کارخانه عرفان صنعت</div><div class="mt-0.5 line-clamp-1 text-[10px] font-medium text-slate-500">بیش از ۲۵ سال تجربه دانش‌بنیان در صنعت روشنایی</div></div></a><a href="{ES_URL_LICENSES}" class="group/corp flex items-start gap-2.5 rounded-2xl border p-2.5 transition-all border-slate-100 bg-slate-50/50 text-slate-800 hover:border-emerald-200 hover:bg-white hover:shadow-2xs"><span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 transition-colors group-hover/corp:bg-emerald-700 group-hover/corp:text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-4 w-4" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg></span><div class="min-w-0 flex-1"><div class="truncate text-xs font-extrabold text-slate-900 group-hover/corp:text-emerald-800">مجوزها و افتخارات رسمی</div><div class="mt-0.5 line-clamp-1 text-[10px] font-medium text-slate-500">گواهینامه ISO 9001:2015، کد ستاد و GS1</div></div></a><a href="{ES_URL_REFUND}" class="group/corp flex items-start gap-2.5 rounded-2xl border p-2.5 transition-all border-slate-100 bg-slate-50/50 text-slate-800 hover:border-emerald-200 hover:bg-white hover:shadow-2xs"><span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 transition-colors group-hover/corp:bg-emerald-700 group-hover/corp:text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><div class="min-w-0 flex-1"><div class="truncate text-xs font-extrabold text-slate-900 group-hover/corp:text-emerald-800">شرایط گارانتی ۲۴ ماهه</div><div class="mt-0.5 line-clamp-1 text-[10px] font-medium text-slate-500">ضمانت تعویض کارخانه و ۵ سال خدمات پس از فروش</div></div></a><a href="{ES_URL_FAQ}" class="group/corp flex items-start gap-2.5 rounded-2xl border p-2.5 transition-all border-slate-100 bg-slate-50/50 text-slate-800 hover:border-emerald-200 hover:bg-white hover:shadow-2xs"><span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 transition-colors group-hover/corp:bg-emerald-700 group-hover/corp:text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-question-mark h-4 w-4" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><path d="M12 17h.01"></path></svg></span><div class="min-w-0 flex-1"><div class="truncate text-xs font-extrabold text-slate-900 group-hover/corp:text-emerald-800">سوالات متداول (FAQ)</div><div class="mt-0.5 line-clamp-1 text-[10px] font-medium text-slate-500">پاسخ به پرسش‌های فنی، خرید آنلاین و نصب</div></div></a><a href="{ES_URL_RECRUIT}" class="group/corp flex items-start gap-2.5 rounded-2xl border p-2.5 transition-all border-slate-100 bg-slate-50/50 text-slate-800 hover:border-emerald-200 hover:bg-white hover:shadow-2xs"><span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 transition-colors group-hover/corp:bg-emerald-700 group-hover/corp:text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 lucide-building-2 h-4 w-4" aria-hidden="true"><path d="M10 12h4"></path><path d="M10 8h4"></path><path d="M14 21v-3a2 2 0 0 0-4 0v3"></path><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"></path><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"></path></svg></span><div class="min-w-0 flex-1"><div class="truncate text-xs font-extrabold text-slate-900 group-hover/corp:text-emerald-800">فرصت‌های شغلی و استخدام</div><div class="mt-0.5 line-clamp-1 text-[10px] font-medium text-slate-500">جذب نیروی متخصص در واحدهای فنی و فروش</div></div></a><a href="{ES_URL_CONTACT}" class="group/corp flex items-start gap-2.5 rounded-2xl border p-2.5 transition-all border-slate-100 bg-slate-50/50 text-slate-800 hover:border-emerald-200 hover:bg-white hover:shadow-2xs"><span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 transition-colors group-hover/corp:bg-emerald-700 group-hover/corp:text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg></span><div class="min-w-0 flex-1"><div class="truncate text-xs font-extrabold text-slate-900 group-hover/corp:text-emerald-800">تماس با کارخانه و دفتر مرکزی</div><div class="mt-0.5 line-clamp-1 text-[10px] font-medium text-slate-500">مشاوره رایگان مهندسی و استعلام قیمت روز</div></div></a></div></div></div></div></div></div>
ESFRAG9Z;

    private const F_BLOG = <<<'ESFRAG9Z'
<a href="{ES_BLOG_URL}" class="rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700">وبلاگ</a><a href="{ES_CONTACT_URL}" class="rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700">تماس با ما</a>
ESFRAG9Z;


    /** آیکون امن */
    public static function icon(string $name): string {
        return self::ICONS[$name] ?? self::ICONS['layers'];
    }

    /** خروجی کامل ناو دسکتاپ (داخل <nav> میرور) */
    public static function output(): void {
        echo self::home_link();
        echo self::products_group();
        echo self::projects_group();
        echo self::services_group();
        echo self::corporate_group();
        echo self::blog_contact();
    }

    private static function home_link(): string {
        $active = function_exists('is_front_page') && is_front_page();
        return strtr(self::F_HOME, [
            '{ES_HOME_URL}'   => esc_url(home_url('/')),
            '{ES_HOME_CLASS}' => $active ? 'bg-emerald-50 text-emerald-800' : 'text-slate-700 hover:bg-slate-50 hover:text-emerald-700',
        ]);
    }

    private static function blog_contact(): string {
        return strtr(self::F_BLOG, [
            '{ES_BLOG_URL}'    => esc_url(es_page_url('blog')),
            '{ES_CONTACT_URL}' => esc_url(es_page_url('contact')),
        ]);
    }

    /**
     * تب‌های مگامنو محصولات — منو → fallback تاکسونومی (فیلتر es_mega_products_tabs)
     *
     * @return list<array{title:string,icon:string,view_all:string,cats:list<array{label:string,href:string}>}>
     */
    public static function product_tabs(): array {
        $custom = apply_filters('es_mega_products_tabs', null);
        if (is_array($custom) && $custom !== []) {
            return $custom;
        }
        // ۱) منوی وردپرس: آیتم es-mega-products → فرزندان es-mega-tab (+ es-icon-*)
        $items = wp_get_nav_menu_items('main-mega-menu');
        if (is_array($items)) {
            $root_id = null;
            foreach ($items as $item) {
                if (in_array('es-mega-products', (array) ($item->classes ?? []), true)) {
                    $root_id = (int) ($item->db_id ?? 0);
                    break;
                }
            }
            if ($root_id !== null) {
                $tabs = [];
                foreach ($items as $item) {
                    $classes = (array) ($item->classes ?? []);
                    if ((int) ($item->menu_item_parent ?? 0) !== $root_id || !in_array('es-mega-tab', $classes, true)) {
                        continue;
                    }
                    $icon = 'layers';
                    foreach ($classes as $class) {
                        if (is_string($class) && str_starts_with($class, 'es-icon-')) {
                            $icon = substr($class, 8);
                            break;
                        }
                    }
                    $tabs[] = ['db_id' => (int) ($item->db_id ?? 0), 'title' => (string) $item->title, 'icon' => $icon, 'view_all' => (string) $item->url, 'cats' => []];
                }
                foreach ($items as $item) {
                    foreach ($tabs as $i => $tab) {
                        if ((int) ($item->menu_item_parent ?? 0) === (int) $tab['db_id']) {
                            $tabs[$i]['cats'][] = ['label' => (string) $item->title, 'href' => (string) $item->url];
                        }
                    }
                }
                if ($tabs !== []) {
                    return array_map(static fn(array $t): array => array_diff_key($t, ['db_id' => 0]), $tabs);
                }
            }
        }
        // ۲) fallback تاکسونومی: ۴ دسته سطح‌اول product_cat
        $top = get_terms(['taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => false]);
        $top = array_values(is_array($top) ? $top : []);
        $tabs = [];
        foreach (array_slice($top, 0, 4) as $i => $term) {
            $children = get_terms(['taxonomy' => 'product_cat', 'parent' => (int) $term->term_id, 'hide_empty' => false]);
            $children = array_values(is_array($children) ? $children : []);
            if ($children === []) {
                $children = [$term];
            }
            $cats = [];
            foreach ($children as $child) {
                $cats[] = ['label' => (string) $child->name, 'href' => (string) get_term_link($child)];
            }
            $tabs[] = [
                'title'    => (string) $term->name,
                'icon'     => self::FALLBACK_ICONS[$i] ?? 'layers',
                'view_all' => (string) get_term_link($term),
                'cats'     => $cats,
            ];
        }
        return $tabs;
    }

    /** لینک‌های آرشیو پروژه (project_type یا city) */
    public static function project_links(string $taxonomy, int $limit = 0): array {
        $terms = get_terms(['taxonomy' => $taxonomy, 'parent' => 0, 'hide_empty' => false]);
        $terms = is_array($terms) ? $terms : [];
        $links = [];
        foreach ($terms as $term) {
            $links[] = ['label' => (string) $term->name, 'href' => (string) get_term_link($term)];
        }
        return $limit > 0 ? array_slice($links, 0, $limit) : $links;
    }

    /** آیتم‌های مگامنو خدمات — منو → آخرین CPT services */
    public static function service_items(int $limit = 5): array {
        $items = wp_get_nav_menu_items('main-mega-menu');
        if (is_array($items)) {
            $root_id = null;
            foreach ($items as $item) {
                if (in_array('es-mega-services', (array) ($item->classes ?? []), true)) {
                    $root_id = (int) ($item->db_id ?? 0);
                    break;
                }
            }
            if ($root_id !== null) {
                $out = [];
                foreach ($items as $item) {
                    if ((int) ($item->menu_item_parent ?? 0) === $root_id) {
                        $out[] = ['label' => (string) $item->title, 'href' => (string) $item->url];
                    }
                }
                if ($out !== []) {
                    return array_slice($out, 0, $limit);
                }
            }
        }
        $posts = get_posts(['post_type' => 'services', 'numberposts' => $limit]);
        $out   = [];
        foreach ($posts as $post) {
            $out[] = ['label' => (string) ($post->post_title ?? ''), 'href' => (string) get_permalink($post)];
        }
        return $out;
    }

    // ─────────────────── رندر گروه‌ها ───────────────────

    private static function products_group(): string {
        $tabs = self::product_tabs();
        if ($tabs === []) {
            // fallback نهایی (بدون دسته/منو): لینک آرشیو اصلی — هدر هرگز خالی نمی‌ماند (Task 4.4 گام ۴)
            $tabs = [[
                'title'    => 'فروشگاه محصولات',
                'icon'     => 'layers',
                'view_all' => es_page_url('shop'),
                'cats'     => [
                    ['label' => 'کاتالوگ کامل محصولات', 'href' => es_page_url('shop')],
                    ['label' => 'همه پروژه‌ها', 'href' => es_page_url('projects')],
                ],
            ]];
        }
        $total_terms = get_terms(['taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => false]);
        $total_terms = is_array($total_terms) ? $total_terms : [];
        $out = str_replace('{ES_TOTAL}', es_fa_num(count($total_terms)), self::F_P_HEAD);
        foreach ($tabs as $i => $tab) {
            $tpl = $i === 0 ? self::F_TAB_ACTIVE : self::F_TAB_IDLE;
            $out .= strtr($tpl, [
                '{ES_I}'     => (string) $i,
                '{ES_TITLE}' => esc_html((string) $tab['title']),
                '{ES_ICON}'  => self::icon((string) $tab['icon']),
            ]);
        }
        $out .= self::F_P_MID;
        foreach ($tabs as $i => $tab) {
            $cats_html = '';
            foreach ($tab['cats'] as $cat) {
                $cats_html .= strtr(self::F_CAT, [
                    '{ES_HREF}'  => esc_url((string) $cat['href']),
                    '{ES_LABEL}' => esc_html((string) $cat['label']),
                ]);
            }
            $out .= strtr(self::F_PANEL, [
                '{ES_I}'           => (string) $i,
                '{ES_PANEL_CLASS}' => $i === 0 ? 'block' : 'hidden',
                '{ES_TITLE}'       => esc_html((string) $tab['title']),
                '{ES_VIEW_ALL}'    => esc_url((string) $tab['view_all']),
                '{ES_CATS}'        => $cats_html,
            ]);
        }
        $out .= strtr(self::F_P_BOTTOM, [
            '{ES_SHOP_Q}'         => esc_url(es_page_url('shop') . '?purchasable=true'),
            '{ES_SHOP}'           => esc_url(es_page_url('shop')),
            '{ES_PRODUCTS_TOTAL}' => es_fa_num((int) wp_count_posts('product')->publish),
        ]);
        $out .= self::F_P_TAIL;
        return $out;
    }

    private static function projects_group(): string {
        $out = self::F_PR_HEAD;
        $out .= self::F_PR_TAB_TYPE;
        $out .= self::F_PR_TAB_CITY;
        $out .= str_replace('{ES_ALL_URL}', esc_url(es_page_url('projects')), self::F_PR_MID);
        $type_links = '';
        foreach (self::project_links('project_type') as $link) {
            $type_links .= strtr(self::F_PT, ['{ES_HREF}' => esc_url((string) $link['href']), '{ES_LABEL}' => esc_html((string) $link['label'])]);
        }
        $out .= str_replace('{ES_LINKS}', $type_links, self::F_PR_PANEL_TYPE);
        $city_links = '';
        foreach (self::project_links('city') as $link) {
            $city_links .= strtr(self::F_PC, ['{ES_HREF}' => esc_url((string) $link['href']), '{ES_LABEL}' => esc_html((string) $link['label'])]);
        }
        $out .= str_replace('{ES_LINKS}', $city_links, self::F_PR_PANEL_CITY);
        $out .= self::F_PR_TAIL;
        return $out;
    }

    private static function services_group(): string {
        $items = self::service_items();
        if ($items === []) {
            // fallback نهایی: لینک آرشیو خدمات — ساختار هدر حفظ می‌شود
            $items = [['label' => 'همه خدمات نورپردازی', 'href' => es_page_url('services')]];
        }
        $out = str_replace('{ES_ALL_URL}', esc_url(es_page_url('services')), self::F_SV_HEAD);
        foreach ($items as $item) {
            $out .= strtr(self::F_SV, ['{ES_HREF}' => esc_url((string) $item['href']), '{ES_LABEL}' => esc_html((string) $item['label'])]);
        }
        $out .= self::F_SV_TAIL;
        return $out;
    }

    private static function corporate_group(): string {
        return strtr(self::F_CORP, [
            '{ES_URL_ABOUT}'    => esc_url(es_page_url('about')),
            '{ES_URL_LICENSES}' => esc_url(es_page_url('licenses-and-honors')),
            '{ES_URL_REFUND}'   => esc_url(es_page_url('refund_returns')),
            '{ES_URL_FAQ}'      => esc_url(es_page_url('faq')),
            '{ES_URL_RECRUIT}'  => esc_url(es_page_url('recruitment')),
            '{ES_URL_CONTACT}'  => esc_url(es_page_url('contact')),
        ]);
    }
}
