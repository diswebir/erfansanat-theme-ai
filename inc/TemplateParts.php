<?php
/**
 * Shared header/footer data and URL helpers.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme;

defined('ABSPATH') || exit;

/**
 * Renders common theme shell fragments and legacy language alternates.
 */
final class TemplateParts
{
    /** Register the theme's hreflang fallback before SEO plugin output. */
    public static function register(): void
    {
        add_action('wp_head', [self::class, 'render_hreflang'], 5);
    }

    /**
     * Convert mirror-relative links and images to canonical WordPress URLs.
     *
     * @param string $url Original static-mirror URL.
     * @param bool   $image Whether the URL points to a theme image asset.
     */
    public static function resolve_static_url(string $url, bool $image = false): string
    {
        $url = trim($url);
        if ('' === $url || '#' === $url || 0 === strpos($url, '#')) {
            return $url;
        }

        $parts = wp_parse_url($url);
        if (false === $parts) {
            return '';
        }
        if (!empty($parts['scheme']) || 0 === strpos($url, '//')) {
            return esc_url($url);
        }

        $path = isset($parts['path']) ? rawurldecode((string) $parts['path']) : '';
        $path = ltrim($path, './');
        if ('' === $path) {
            $resolved = home_url('/');
        } elseif ($image || 0 === strpos($path, 'images/')) {
            $imagePath = 0 === strpos($path, 'images/') ? substr($path, strlen('images/')) : $path;
            $resolved = get_theme_file_uri('assets/images/' . ltrim($imagePath, '/'));
        } else {
            $canonicalPath = $path;
            if ('index.html' === $canonicalPath) {
                $canonicalPath = '';
            } elseif (in_array($canonicalPath, ['shop.html', 'products.html'], true)) {
                $resolved = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '';
                if (!$resolved) {
                    $resolved = home_url('/shop/');
                }
                return self::append_url_suffix($resolved, $parts);
            } elseif ('cart.html' === $canonicalPath && function_exists('wc_get_cart_url')) {
                return self::append_url_suffix(wc_get_cart_url(), $parts);
            } elseif ('checkout.html' === $canonicalPath && function_exists('wc_get_checkout_url')) {
                return self::append_url_suffix(wc_get_checkout_url(), $parts);
            } elseif (0 === strpos($canonicalPath, 'products/category-')) {
                $canonicalPath = 'product_cat/' . substr($canonicalPath, strlen('products/category-'));
            } elseif (0 === strpos($canonicalPath, 'products/')) {
                $canonicalPath = 'product/' . substr($canonicalPath, strlen('products/'));
            }

            if (preg_match('/\.html$/i', $canonicalPath)) {
                $canonicalPath = substr($canonicalPath, 0, -5);
            }
            $resolved = home_url('/' . trim($canonicalPath, '/') . ('' === trim($canonicalPath, '/') ? '' : '/'));
        }

        return self::append_url_suffix($resolved, $parts);
    }

    /** Render language alternates from legacy metadata unless SEOPress owns them. */
    public static function render_hreflang(): void
    {
        if (self::seopress_is_active()) {
            return;
        }

        $object = get_queried_object();
        if ($object instanceof \WP_Term) {
            echo self::hreflang_markup((int) $object->term_id, $object->taxonomy); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated with escaped URLs/attributes.
            return;
        }
        $postId = (int) get_queried_object_id();
        if ($postId > 0 && get_post($postId)) {
            echo self::hreflang_markup($postId, 'post'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated with escaped URLs/attributes.
        }
    }

    /**
     * Build unique language alternate links for a post or taxonomy term.
     *
     * @param int    $objectId Post or term ID.
     * @param string $objectType `post` or the taxonomy name.
     */
    public static function hreflang_markup(int $objectId, string $objectType = 'post'): string
    {
        if ($objectId < 1 || self::seopress_is_active()) {
            return '';
        }
        $isTerm = 'post' !== $objectType;
        if ($isTerm) {
            $values = [
                'fa' => get_term_meta($objectId, 'hrefmeta_fa', true),
                'en' => get_term_meta($objectId, 'hrefmeta_en', true),
                'ar' => get_term_meta($objectId, 'hrefmeta_ar', true),
            ];
            $current = get_term_link($objectId, $objectType);
        } else {
            $values = [
                'fa' => get_post_meta($objectId, 'hrefmeta_fa', true),
                'en' => get_post_meta($objectId, 'hrefmeta_en', true),
                'ar' => get_post_meta($objectId, 'hrefmeta_ar', true),
            ];
            $current = get_permalink($objectId);
        }
        if (is_wp_error($current) || !is_string($current)) {
            $current = home_url('/');
        }

        $hasExplicitAlternate = false;
        foreach ($values as $value) {
            if (is_string($value) && '' !== trim($value)) {
                $hasExplicitAlternate = true;
                break;
            }
        }
        if (!$hasExplicitAlternate) {
            return '';
        }
        if (!is_string($values['fa']) || '' === trim($values['fa'])) {
            $values['fa'] = $current;
        }

        $links = [];
        foreach (['fa', 'en', 'ar'] as $language) {
            $url = isset($values[$language]) && is_string($values[$language]) ? esc_url($values[$language]) : '';
            if ('' === $url) {
                continue;
            }
            $links[$language] = '<link rel="alternate" hreflang="' . esc_attr($language) . '" href="' . $url . '" />';
        }

        return $links ? implode("\n", array_unique($links)) . "\n" : '';
    }

    /**
     * Replace header tokens and rewrite static-mirror URLs before output.
     *
     * @param string $markup Static mirror's complete header/mobile/cart shell.
     */
    public static function render_header_markup(string $markup, string $desktopFallback = '', string $mobileFallback = ''): void
    {
        $brandName = (string) es_option('branding.site_name', get_bloginfo('name'));
        $tagline = (string) es_option('branding.site_tagline', get_bloginfo('description'));
        $markup = str_replace('%%ESE_DESKTOP_NAV%%', Navigation\Navigation::render_desktop($desktopFallback), $markup);
        $markup = str_replace('%%ESE_MOBILE_MENU%%', Navigation\Navigation::render_mobile($mobileFallback), $markup);
        $markup = str_replace('%%ESE_LANDING_NAV%%', Navigation\Navigation::render_landing(), $markup);
        $markup = str_replace('%%ESE_TOPBAR%%', self::render_topbar(), $markup);
        $markup = str_replace('%%ESE_BRAND%%', self::render_brand($brandName, $tagline), $markup);
        $markup = str_replace('%%ESE_ANNOUNCEMENT%%', self::render_announcement(), $markup);
        if (!es_option('header.sticky', true)) {
            $markup = preg_replace('/class="fixed inset-x-0 top-0/', 'class="absolute inset-x-0 top-0', $markup, 1);
        }
        $markup = str_replace(
            ['عرفان صنعت اصفهان', 'نورپردازی شهری و المان‌های نوری'],
            [esc_html($brandName), esc_html($tagline)],
            $markup
        );

        $replacements = [
            'دسته‌بندی تولیدات (۱۵ گروه)' => esc_html((string) es_option('header.mega_menu_title', 'دسته‌بندی تولیدات (۱۵ گروه)')),
            'مشاهده محصولات تخصصی نورپردازی شهری و صنعتی' => esc_html((string) es_option('header.mega_menu_description', 'مشاهده محصولات تخصصی نورپردازی شهری و صنعتی')),
            'مشاهده همه محصولات' => esc_html((string) es_option('header.mega_menu_cta_label', 'مشاهده همه محصولات')),
        ];
        $markup = str_replace(array_keys($replacements), array_values($replacements), $markup);
        $ctaUrl = (string) es_option('header.mega_menu_cta_url', '/shop/');
        $markup = str_replace('href="products.html"', 'href="' . esc_url(self::resolve_static_url($ctaUrl)) . '"', $markup);

        $markup = preg_replace_callback(
            '/\b(href|src)="([^"]*)"/i',
            static function (array $matches): string {
                $image = 'src' === strtolower($matches[1]);
                return $matches[1] . '="' . esc_url(self::resolve_static_url(html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8'), $image)) . '"';
            },
            $markup
        );

        $count = 0;
        if (function_exists('WC') && WC() && isset(WC()->cart)) {
            $count = (int) WC()->cart->get_cart_contents_count();
        }
        $markup = preg_replace_callback(
            '/(<span\b[^>]*data-header-cart-badge="true"[^>]*>).*?(<\/span>)/s',
            static function (array $matches) use ($count): string {
                return $matches[1] . esc_html(number_format_i18n($count)) . $matches[2];
            },
            $markup,
            1
        );
        echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- extracted static HTML; interpolated content and URLs are escaped above.
    }

    /** Parse a newline list of `label | URL` links, with safe fallbacks. */
    public static function parse_links(string $input, array $fallback = []): array
    {
        $links = [];
        foreach (preg_split('/\r\n|\r|\n/', $input) ?: [] as $line) {
            $parts = array_map('trim', explode('|', $line, 2));
            if (2 !== count($parts) || '' === $parts[0] || '' === $parts[1]) {
                continue;
            }
            $links[] = ['label' => sanitize_text_field($parts[0]), 'url' => $parts[1]];
        }

        return $links ?: $fallback;
    }

    /** Parse newline-separated plain-text items. */
    public static function parse_lines(string $input, array $fallback = []): array
    {
        $items = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $input) ?: []), 'strlen'));
        return $items ?: $fallback;
    }

    /** Convert option links into a URL safe for tel: and mailto: values. */
    public static function contact_href(string $value, string $scheme): string
    {
        $value = trim($value);
        if ('mailto' === $scheme) {
            return 'mailto:' . sanitize_email($value);
        }
        $number = preg_replace('/[^0-9+]/', '', $value);
        return 'tel:' . $number;
    }

    /** Render the editable topbar and its configured links. */
    private static function render_topbar(): string
    {
        if (!es_option('header.topbar_enabled', true)) {
            return '';
        }
        $siteName = (string) es_option('branding.site_name', 'عرفان صنعت اصفهان');
        $message = (string) es_option('header.topbar_message', 'گواهینامه ISO 9001:2015 و کدهای رسمی سامانه ستاد و GS1');
        $defaults = [
            ['label' => 'مجوزها و افتخارات', 'url' => '/licenses-and-honors/'],
            ['label' => 'گارانتی ۲۴ ماهه', 'url' => '/refund_returns/'],
            ['label' => 'استخدام', 'url' => '/recruitment/'],
        ];
        $links = self::parse_links((string) es_option('header.topbar_links', ''), $defaults);
        $primaryLink = [
            'label' => (string) es_option('header.topbar_link_label', $defaults[0]['label']),
            'url' => (string) es_option('header.topbar_link_url', $defaults[0]['url']),
        ];
        if ('' !== trim($primaryLink['label']) && '' !== trim($primaryLink['url'])) {
            array_unshift($links, $primaryLink);
            if (count($links) > 1 && $links[1]['label'] === $defaults[0]['label'] && $links[1]['url'] === $defaults[0]['url']) {
                unset($links[1]);
                $links = array_values($links);
            }
        }
        $linkMarkup = [];
        foreach ($links as $index => $link) {
            if ($index > 0) {
                $linkMarkup[] = '<span class="text-slate-300">·</span>';
            }
            $linkMarkup[] = '<a href="' . esc_url(self::resolve_static_url($link['url'])) . '" class="font-bold text-slate-600 transition-colors hover:text-emerald-700">' . esc_html($link['label']) . '</a>';
        }

        return '<div class="hidden border-b border-emerald-100/70 bg-emerald-50/55 text-[11px] text-slate-600 lg:block"><div class="mx-auto flex max-w-[1300px] items-center justify-between px-5 py-1.5 lg:px-8"><div class="flex items-center gap-2.5 font-medium"><span class="font-extrabold text-emerald-800">' . esc_html($siteName) . ' (ESE)</span><span class="text-slate-300">·</span><span>' . esc_html($message) . '</span></div><div class="flex items-center gap-3.5">' . implode('', $linkMarkup) . '</div></div></div>';
    }

    /** Render the logo/name link with its configured media fallback. */
    private static function render_brand(string $siteName, string $tagline): string
    {
        $logo = (string) es_option('branding.logo_url', get_theme_file_uri('assets/images/ese-logo.svg'));
        $mark = $logo
            ? '<img src="' . esc_url($logo) . '" alt="" class="h-7 w-7 object-contain sm:h-8 sm:w-8" />'
            : '<span class="text-xs font-black tracking-tighter text-emerald-800 sm:text-sm">ESE</span>';

        return '<a href="' . esc_url(home_url('/')) . '" class="group flex shrink-0 items-center gap-2.5 whitespace-nowrap"><div class="grid h-9 w-9 place-items-center rounded-xl border border-emerald-200 bg-emerald-50/90 transition-colors group-hover:bg-emerald-700 group-hover:text-white sm:h-10 sm:w-10">' . $mark . '</div><div class="leading-tight"><div class="text-[14px] font-black tracking-tight text-slate-900 sm:text-[15px]">' . esc_html($siteName) . '</div><div class="text-[10px] font-semibold text-slate-500">' . esc_html($tagline) . '</div></div></a>';
    }

    /** Render an optional editable announcement after the header. */
    private static function render_announcement(): string
    {
        if (!es_option('header.announcement_enabled', false)) {
            return '';
        }
        $text = trim((string) es_option('header.announcement_text', ''));
        if ('' === $text) {
            return '';
        }
        $url = trim((string) es_option('header.announcement_url', ''));
        $content = esc_html($text);
        if ('' !== $url) {
            $content = '<a href="' . esc_url(self::resolve_static_url($url)) . '" class="font-bold underline underline-offset-2">' . $content . '</a>';
        }
        return '<div data-header-announcement="true" class="relative z-40 bg-emerald-800 px-4 py-2 text-center text-xs text-white">' . $content . '</div>';
    }

    /** Handle query and fragment suffixes when rewriting relative URLs. */
    private static function append_url_suffix(string $url, array $parts): string
    {
        if (isset($parts['query']) && '' !== $parts['query']) {
            $url .= '?' . $parts['query'];
        }
        if (isset($parts['fragment']) && '' !== $parts['fragment']) {
            $url .= '#' . $parts['fragment'];
        }
        return esc_url($url);
    }

    /** Whether a supported SEOPress build owns hreflang output for this request. */
    private static function seopress_is_active(): bool
    {
        return defined('SEOPRESS_VERSION') || function_exists('seopress_init') || class_exists('SEOPress');
    }
}
