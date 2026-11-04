<?php
/**
 * واکر/سازنده منوی موبایل — بازسازی ساختار سایدبار میرور با داده داینامیک
 *
 * منبع مارک‌آپ: temp/phase-04-header-footer-navigation/refs/mobile-sidebar-ref.html (byte-exact)
 * منابع داده: همان منابع MegaMenu (product_tabs/project_links/service_items) + es_contact_args
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Nav;

final class MobileWalker {

    private const F_HEAD = <<<'ESFRAG7K'
<div data-mobile-menu="true" class="fixed inset-0 z-[90] lg:hidden hidden" aria-hidden="true"><div data-mobile-backdrop="true" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div><aside data-mobile-sidebar="true" aria-label="منوی موبایل" class="fixed inset-y-0 right-0 z-[95] flex h-dvh max-h-dvh w-[88vw] max-w-[380px] flex-col border-l border-emerald-100 bg-white shadow-2xl"><div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-4 py-3.5"><a href="{ES_HOME_URL}" class="flex items-center gap-2.5"><?php if (es_logo_url() !== ''): ?><img src="<?php echo esc_url(es_logo_url()); ?>" alt="<?php echo esc_attr(es_logo_alt()); ?>" class="h-9 w-9 rounded-xl object-contain"><?php else: ?><div class="grid h-9 w-9 place-items-center rounded-xl border border-emerald-200 bg-emerald-50 text-xs font-black text-emerald-800">ESE</div><?php endif; ?><div><div class="text-sm font-black text-slate-900">{ES_BLOGNAME}</div><div class="text-[10px] font-semibold text-emerald-700">شرکت دانش‌بنیان نورپردازی شهری</div></div></a><button type="button" data-mobile-close="true" aria-label="بستن منو" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-4.5 w-4.5" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button></div><div class="shrink-0 border-b border-slate-100 px-4 py-2.5">
ESFRAG7K;

    private const F_ZONE = <<<'ESFRAG7K'
<button type="button" data-search-modal-open="true" class="flex w-full items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-bold text-slate-600 hover:border-emerald-300 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-emerald-600" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><span>جستجو در محصولات و پروژه‌ها...</span></button></div><div class="flex-1 space-y-2.5 overflow-y-auto overscroll-contain px-3.5 py-3.5"><a href="{ES_HOME_URL}" class="flex items-center justify-between rounded-xl border px-3.5 py-3 text-xs font-black transition-colors border-emerald-200 bg-emerald-50 text-emerald-900"><span>صفحه اصلی</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 text-emerald-700" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a>
ESFRAG7K;

    private const F_MP_HEAD = <<<'ESFRAG7K'
<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white"><button type="button" data-mobile-acc-btn="products" aria-expanded="false" class="flex w-full items-center justify-between px-3.5 py-3 text-right cursor-pointer hover:bg-slate-50"><span class="text-xs font-black text-slate-900">محصولات تولیدی ({ES_TOTAL} دسته)</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 text-slate-500 transition-transform duration-200" aria-hidden="true" data-mobile-acc-icon="true"><path d="m6 9 6 6 6-6"></path></svg></button><div data-mobile-acc-panel="products" class="hidden flex-col gap-2.5 border-t border-slate-100 bg-slate-50/50 p-2.5"><div class="grid grid-cols-2 gap-1">
ESFRAG7K;

    private const F_MP_TAB_ACTIVE = <<<'ESFRAG7K'
<button type="button" data-mobile-prod-tab="{ES_I}" class=" truncate rounded-lg border px-2.5 py-1.5 text-center text-[11px] font-extrabold transition-all cursor-pointer {ES_TAB_CLASS}">{ES_TITLE}</button>
ESFRAG7K;

    private const F_MP_TAB_IDLE = <<<'ESFRAG7K'
<button type="button" data-mobile-prod-tab="{ES_I}" class=" truncate rounded-lg border px-2.5 py-1.5 text-center text-[11px] font-extrabold transition-all cursor-pointer {ES_TAB_CLASS}">{ES_TITLE}</button>
ESFRAG7K;

    private const F_MP_MID = <<<'ESFRAG7K'
</div>
ESFRAG7K;

    private const F_MP_PANEL_PFX = <<<'ESFRAG7K'
<div data-mobile-prod-panel="{ES_I}" class="{ES_PANEL_CLASS}">
ESFRAG7K;

    private const F_MP_PANEL_SFX = <<<'ESFRAG7K'
</div>
ESFRAG7K;

    private const F_MCAT = <<<'ESFRAG7K'
<a href="{ES_HREF}" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-[11.5px] font-bold text-slate-700 hover:text-emerald-800"><span class="truncate">{ES_LABEL}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a>
ESFRAG7K;

    private const F_MP_TAIL = <<<'ESFRAG7K'
<a href="{ES_SHOP}" class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-700 py-2 text-xs font-black text-white"><span>مشاهده کاتالوگ کامل محصولات</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a></div></div>
ESFRAG7K;

    private const F_ACC_PROJ = <<<'ESFRAG7K'
<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white"><button type="button" data-mobile-acc-btn="projects" aria-expanded="false" class="flex w-full items-center justify-between px-3.5 py-3 text-right cursor-pointer hover:bg-slate-50"><span class="text-xs font-black text-slate-900">پروژه‌های نورپردازی شهری</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 text-slate-500 transition-transform duration-200" aria-hidden="true" data-mobile-acc-icon="true"><path d="m6 9 6 6 6-6"></path></svg></button><div data-mobile-acc-panel="projects" class="hidden flex-col gap-2.5 border-t border-slate-100 bg-slate-50/50 p-2.5"><div class="grid grid-cols-2 gap-1 rounded-lg bg-slate-200/70 p-1"><button type="button" data-mobile-proj-tab="type" class="rounded-md py-1.5 text-[11px] font-black transition-all cursor-pointer bg-white text-emerald-800 shadow-2xs">بر اساس نوع سازه</button><button type="button" data-mobile-proj-tab="city" class="rounded-md py-1.5 text-[11px] font-black transition-all cursor-pointer text-slate-600">بر اساس شهر اجرا</button></div><div data-mobile-proj-panel="type" class="grid grid-cols-2 gap-1">{ES_LINKS_TYPE}</div><div data-mobile-proj-panel="city" class="hidden">{ES_LINKS_CITY}</div><a href="{ES_ALL_URL}" class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-emerald-200 bg-white py-2 text-xs font-black text-emerald-800"><span>همه پروژه‌ها</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a></div></div>
ESFRAG7K;

    private const F_MT = <<<'ESFRAG7K'
<a href="{ES_HREF}" class="truncate rounded-lg bg-white px-2.5 py-2 text-[11px] font-bold text-slate-700 hover:text-emerald-800">{ES_LABEL}</a>
ESFRAG7K;

    private const F_PC = <<<'ESFRAG7K'
<a href="{ES_HREF}" class="truncate rounded-lg bg-white px-2.5 py-2 text-[11px] font-bold text-slate-700 hover:text-emerald-800">{ES_LABEL}</a>
ESFRAG7K;

    private const F_ACC_SERV = <<<'ESFRAG7K'
<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white"><button type="button" data-mobile-acc-btn="services" aria-expanded="false" class="flex w-full items-center justify-between px-3.5 py-3 text-right cursor-pointer hover:bg-slate-50"><span class="text-xs font-black text-slate-900">خدمات مهندسی و اجرایی</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 text-slate-500 transition-transform duration-200" aria-hidden="true" data-mobile-acc-icon="true"><path d="m6 9 6 6 6-6"></path></svg></button><div data-mobile-acc-panel="services" class="hidden flex-col gap-1 border-t border-slate-100 bg-slate-50/50 p-2">{ES_ITEMS}</div></div>
ESFRAG7K;

    private const F_MS = <<<'ESFRAG7K'
<a href="{ES_HREF}" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:text-emerald-800"><span class="truncate">{ES_LABEL}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a>
ESFRAG7K;

    private const F_ACC_CORP = <<<'ESFRAG7K'
<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white"><button type="button" data-mobile-acc-btn="corporate" aria-expanded="false" class="flex w-full items-center justify-between px-3.5 py-3 text-right cursor-pointer hover:bg-slate-50"><span class="text-xs font-black text-slate-900">درباره شرکت و گواهینامه‌ها</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 text-slate-500 transition-transform duration-200" aria-hidden="true" data-mobile-acc-icon="true"><path d="m6 9 6 6 6-6"></path></svg></button><div data-mobile-acc-panel="corporate" class="hidden flex-col gap-1 border-t border-slate-100 bg-slate-50/50 p-2"><a href="{ES_URL_ABOUT}" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:text-emerald-800"><span class="truncate">درباره کارخانه عرفان صنعت</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a><a href="{ES_URL_LICENSES}" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:text-emerald-800"><span class="truncate">مجوزها و افتخارات رسمی</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a><a href="{ES_URL_REFUND}" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:text-emerald-800"><span class="truncate">شرایط گارانتی ۲۴ ماهه</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a><a href="{ES_URL_FAQ}" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:text-emerald-800"><span class="truncate">سوالات متداول (FAQ)</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a><a href="{ES_URL_RECRUIT}" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:text-emerald-800"><span class="truncate">فرصت‌های شغلی و استخدام</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a><a href="{ES_URL_CONTACT}" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:text-emerald-800"><span class="truncate">تماس با کارخانه و دفتر مرکزی</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a></div></div>
ESFRAG7K;

    private const F_TWOUP = <<<'ESFRAG7K'
<div class="grid grid-cols-2 gap-2 pt-1"><a href="{ES_BLOG_URL}" class="flex items-center justify-between rounded-2xl border border-slate-200/80 bg-slate-50 px-3.5 py-2.5 text-xs font-black text-slate-800 hover:bg-emerald-50"><span>وبلاگ تخصصی</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 text-slate-400" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a><a href="{ES_CONTACT_URL}" class="flex items-center justify-between rounded-2xl border border-slate-200/80 bg-slate-50 px-3.5 py-2.5 text-xs font-black text-slate-800 hover:bg-emerald-50"><span>تماس با ما</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 text-slate-400" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a></div></div>
ESFRAG7K;

    private const F_TAILBAR = <<<'ESFRAG7K'
<div class="shrink-0 space-y-2 border-t border-slate-100 bg-slate-50 p-3.5"><button type="button" data-cart-modal-open="true" class="flex w-full items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-white py-2.5 text-xs font-black text-emerald-900 shadow-2xs cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg><span>مشاهده سبد خرید و پیش‌فاکتور</span></button><a href="{ES_TEL_URL}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 py-2.5 text-xs font-black text-white shadow-sm"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span>تماس با کارخانه: {ES_TEL_DISPLAY}</span></a></div></aside></div>
ESFRAG7K;


    /** خروجی کامل سایدبار موبایل (شامل backdrop و wrapper) */
    public static function output(): void {
        echo self::render();
    }

    public static function render(): string {
        $tabs = MegaMenu::product_tabs();
        $contact = \ErfanSanat\Theme\Admin\OptionsOutput::contact_args();
        $top_terms = get_terms(['taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => false]);
        $top_terms = is_array($top_terms) ? $top_terms : [];

        $out = strtr(self::F_HEAD, [
            '{ES_HOME_URL}'  => esc_url(home_url('/')),
            '{ES_BLOGNAME}'  => esc_html(get_bloginfo('name') !== '' ? get_bloginfo('name') : 'عرفان صنعت اصفهان'),
        ]);
        $out .= str_replace('{ES_HOME_URL}', esc_url(home_url('/')), self::F_ZONE);

        // ── آکاردئون محصولات ──
        $out .= str_replace('{ES_TOTAL}', es_fa_num(count($top_terms)), self::F_MP_HEAD);
        $cls_active = 'border-emerald-600 bg-emerald-700 text-white';
        $cls_idle   = 'border-slate-200 bg-white text-slate-700';
        foreach ($tabs as $i => $tab) {
            $tpl = $i === 0 ? self::F_MP_TAB_ACTIVE : self::F_MP_TAB_IDLE;
            $out .= strtr($tpl, [
                '{ES_I}'        => (string) $i,
                '{ES_TITLE}'    => esc_html((string) $tab['title']),
                '{ES_TAB_CLASS}' => $i === 0 ? $cls_active : $cls_idle,
            ]);
        }
        $out .= self::F_MP_MID;
        foreach ($tabs as $i => $tab) {
            $cats = '';
            foreach ($tab['cats'] as $cat) {
                $cats .= strtr(self::F_MCAT, [
                    '{ES_HREF}'  => esc_url((string) $cat['href']),
                    '{ES_LABEL}' => esc_html((string) $cat['label']),
                ]);
            }
            $out .= strtr(self::F_MP_PANEL_PFX, [
                '{ES_I}'           => (string) $i,
                '{ES_PANEL_CLASS}' => $i === 0 ? 'space-y-1 block' : 'hidden',
            ]) . $cats . self::F_MP_PANEL_SFX;
        }
        $out .= str_replace('{ES_SHOP}', esc_url(es_page_url('shop')), self::F_MP_TAIL);

        // ── آکاردئون پروژه‌ها ──
        $type_links = '';
        foreach (MegaMenu::project_links('project_type') as $link) {
            $type_links .= strtr(self::F_MT, ['{ES_HREF}' => esc_url((string) $link['href']), '{ES_LABEL}' => esc_html((string) $link['label'])]);
        }
        $city_links = '';
        foreach (MegaMenu::project_links('city') as $link) {
            $city_links .= strtr(self::F_PC, ['{ES_HREF}' => esc_url((string) $link['href']), '{ES_LABEL}' => esc_html((string) $link['label'])]);
        }
        $out .= strtr(self::F_ACC_PROJ, [
            '{ES_LINKS_TYPE}' => $type_links,
            '{ES_LINKS_CITY}' => $city_links,
            '{ES_ALL_URL}'    => esc_url(es_page_url('projects')),
        ]);

        // ── آکاردئون خدمات ──
        $serv_items = '';
        foreach (MegaMenu::service_items(6) as $item) {
            $serv_items .= strtr(self::F_MS, ['{ES_HREF}' => esc_url((string) $item['href']), '{ES_LABEL}' => esc_html((string) $item['label'])]);
        }
        $out .= str_replace('{ES_ITEMS}', $serv_items, self::F_ACC_SERV);

        // ── آکاردئون corporate ──
        $out .= strtr(self::F_ACC_CORP, [
            '{ES_URL_ABOUT}'    => esc_url(es_page_url('about')),
            '{ES_URL_LICENSES}' => esc_url(es_page_url('licenses-and-honors')),
            '{ES_URL_REFUND}'   => esc_url(es_page_url('refund_returns')),
            '{ES_URL_FAQ}'      => esc_url(es_page_url('faq')),
            '{ES_URL_RECRUIT}'  => esc_url(es_page_url('recruitment')),
            '{ES_URL_CONTACT}'  => esc_url(es_page_url('contact')),
        ]);

        // ── دوکارت وبلاگ/تماس ──
        $out .= strtr(self::F_TWOUP, [
            '{ES_BLOG_URL}'    => esc_url(es_page_url('blog')),
            '{ES_CONTACT_URL}' => esc_url(es_page_url('contact')),
        ]);

        // ── نوار پایانی: سبد + تلفن ──
        $display = preg_replace('/\s*\([^)]*\)\s*$/u', '', (string) ($contact['phone_display'] ?? ''));
        $out .= strtr(self::F_TAILBAR, [
            '{ES_TEL_URL}'     => 'tel:' . rawurlencode((string) ($contact['phone'] ?? '')),
            '{ES_TEL_DISPLAY}' => esc_html(is_string($display) && $display !== '' ? $display : (string) ($contact['phone_display'] ?? '')),
        ]);
        return $out;
    }
}
