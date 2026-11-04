<?php
/**
 * WordPress menu locations and responsive menu renderers.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Navigation;

use ErfanSanat\Theme\TemplateParts;

defined('ABSPATH') || exit;

/** Registers theme menu locations and renders desktop/mobile navigation. */
final class Navigation
{
    /** Register the four legacy menu locations retained by the theme. */
    public static function register_menus(): void
    {
        register_nav_menus(
            [
                'main-mega-menu' => __('منوی اصلی و مگامنو', 'erfansanat-theme'),
                'mobile-menu' => __('منوی موبایل', 'erfansanat-theme'),
                'tolidat-sherkat' => __('تولیدات شرکت', 'erfansanat-theme'),
                'landing' => __('منوی صفحات فرود', 'erfansanat-theme'),
            ]
        );
    }

    /** Whether a named theme location currently has an assigned menu. */
    public static function has_location(string $location): bool
    {
        return in_array($location, array_keys(get_registered_nav_menus()), true) && has_nav_menu($location);
    }

    /** Render desktop mega navigation, or the exact static-mirror fallback. */
    public static function render_desktop(string $fallback = ''): string
    {
        if (!self::has_location('main-mega-menu')) {
            return $fallback;
        }
        $items = self::menu_items('main-mega-menu', 'desktop');
        if ('' === trim($items)) {
            return $fallback;
        }

        return '<nav aria-label="' . esc_attr__('منوی اصلی', 'erfansanat-theme') . '" class="hidden items-center gap-1 lg:flex">' . $items . '</nav>';
    }

    /** Render the responsive off-canvas menu with a separate or main-menu source. */
    public static function render_mobile(string $fallback = ''): string
    {
        $location = self::has_location('mobile-menu') ? 'mobile-menu' : (self::has_location('main-mega-menu') ? 'main-mega-menu' : '');
        if ('' === $location) {
            return $fallback;
        }
        $items = self::menu_items($location, 'mobile');
        if ('' === trim($items)) {
            return $fallback;
        }

        $siteName = (string) es_option('branding.site_name', get_bloginfo('name'));
        $tagline = (string) es_option('branding.site_tagline', get_bloginfo('description'));
        $logo = (string) es_option('branding.logo_url', get_theme_file_uri('assets/images/ese-logo.svg'));
        $phone = (string) es_option('contact.phone', '031-91091011');
        $brand = $logo
            ? '<img src="' . esc_url($logo) . '" alt="" class="h-7 w-7 object-contain" />'
            : '<span class="text-xs font-black text-emerald-800">ESE</span>';

        return '<div data-mobile-menu="true" class="fixed inset-0 z-[90] hidden lg:hidden" aria-hidden="true">'
            . '<div data-mobile-backdrop="true" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>'
            . '<aside data-mobile-sidebar="true" aria-label="' . esc_attr__('منوی موبایل', 'erfansanat-theme') . '" class="fixed inset-y-0 right-0 z-[95] flex h-dvh max-h-dvh w-[88vw] max-w-[380px] flex-col border-l border-emerald-100 bg-white shadow-2xl">'
            . '<div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-4 py-3.5"><a href="' . esc_url(home_url('/')) . '" class="flex items-center gap-2.5"><div class="grid h-9 w-9 place-items-center rounded-xl border border-emerald-200 bg-emerald-50">' . $brand . '</div><div><div class="text-sm font-black text-slate-900">' . esc_html($siteName) . '</div><div class="text-[10px] font-semibold text-emerald-700">' . esc_html($tagline) . '</div></div></a><button type="button" data-mobile-close="true" aria-label="' . esc_attr__('بستن منو', 'erfansanat-theme') . '" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 cursor-pointer"><span aria-hidden="true">×</span></button></div>'
            . '<div class="shrink-0 border-b border-slate-100 px-4 py-2.5"><button type="button" data-search-modal-open="true" class="flex w-full items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-bold text-slate-600 hover:border-emerald-300 cursor-pointer"><span aria-hidden="true">⌕</span><span>' . esc_html__('جستجو در محصولات و پروژه‌ها...', 'erfansanat-theme') . '</span></button></div>'
            . '<div class="flex-1 space-y-2.5 overflow-y-auto overscroll-contain px-3.5 py-3.5">' . $items . '</div>'
            . '<div class="shrink-0 space-y-2 border-t border-slate-100 bg-slate-50 p-3.5"><button type="button" data-cart-modal-open="true" class="flex w-full items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-white py-2.5 text-xs font-black text-emerald-900 shadow-2xs cursor-pointer">' . esc_html__('مشاهده سبد خرید و پیش‌فاکتور', 'erfansanat-theme') . '</button><a href="' . esc_url(TemplateParts::contact_href($phone, 'tel')) . '" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 py-2.5 text-xs font-black text-white shadow-sm">' . esc_html($phone) . '</a></div>'
            . '</aside></div>';
    }

    /** Render product or landing location markup; static pages can pass a fallback. */
    public static function render_location(string $location, string $context = 'products', string $fallback = ''): string
    {
        if (!self::has_location($location)) {
            return $fallback;
        }
        $items = self::menu_items($location, $context);
        if ('' === trim($items)) {
            return $fallback;
        }
        if ('landing' === $context) {
            return '<nav data-landing-nav="true" aria-label="' . esc_attr__('ناوبری صفحه', 'erfansanat-theme') . '" class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur"><ul class="mx-auto flex max-w-[1300px] flex-wrap items-center gap-2 px-4 py-2">' . $items . '</ul></nav>';
        }

        return '<ul class="space-y-2.5 text-[13px] text-slate-600">' . $items . '</ul>';
    }

    /** Render the landing location only on the known static landing pages. */
    public static function render_landing(): string
    {
        if (!is_page(['arya', 'smartelement']) || !self::has_location('landing')) {
            return '';
        }
        return self::render_location('landing', 'landing');
    }

    /** Capture wp_nav_menu output with the requested custom walker. */
    private static function menu_items(string $location, string $context): string
    {
        $items = wp_nav_menu(
            [
                'theme_location' => $location,
                'container' => false,
                'items_wrap' => '%3$s',
                'fallback_cb' => false,
                'echo' => false,
                'depth' => 0,
                'walker' => new Walker($context),
            ]
        );
        return is_string($items) ? $items : '';
    }
}
