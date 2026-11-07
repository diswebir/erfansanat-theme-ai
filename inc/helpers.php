<?php
/**
 * Global template helpers — loaded on EVERY request (front-end + admin).
 * These MUST be global-namespace functions so templates can call them.
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('es_option')) {
    /**
     * Cached theme-option reader with defaults fallback.
     * es_option('footer.email', $default)
     */
    function es_option(string $key, $default = null)
    {
        static $cache = null;
        if ($cache === null) {
            $opt = get_option(\ErfanSanat\Theme\Admin\ThemeOptions::OPTION, []);
            $cache = array_replace_recursive(\ErfanSanat\Theme\Admin\ThemeOptions::defaults(), is_array($opt) ? $opt : []);
        }
        $cur = $cache;
        foreach (explode('.', $key) as $seg) {
            if (!is_array($cur) || !array_key_exists($seg, $cur)) {
                return $default;
            }
            $cur = $cur[$seg];
        }
        return $cur;
    }
}

if (!function_exists('es_sections')) {
    /**
     * Ordered, enabled sections for a page (SectionManager).
     *
     * @return array<int, array{id:string, order:int, fields:array}>
     */
    function es_sections(int $post_id): array
    {
        $stored = get_post_meta($post_id, \ErfanSanat\Theme\Admin\SectionManager::META, true);
        $stored = is_array($stored) ? $stored : [];
        $post   = get_post($post_id);
        $sections = $post ? \ErfanSanat\Theme\Admin\SectionManager::sections_for($post) : [];

        $out = [];
        $i = 0;
        foreach ($sections as $sid => $label) {
            $i++;
            if (!($stored[$sid]['enabled'] ?? true)) {
                continue;
            }
            $out[] = [
                'id'     => $sid,
                'order'  => $stored[$sid]['order'] ?? $i,
                'fields' => is_array($stored[$sid]['fields'] ?? null) ? $stored[$sid]['fields'] : [],
            ];
        }
        usort($out, fn($a, $b) => $a['order'] <=> $b['order']);
        return $out;
    }
}

if (!function_exists('es_hreflang_tags')) {
    /**
     * Template tag used by header.php — echoes hreflang <link> tags.
     */

    /**
     * Map a legacy static-mirror path (e.g. "product/x.html", "shop.html")
     * to the correct WordPress URL. Falls back to home_url('/'.$path).
     */
    function es_static_url(string $path): string
    {
        $path = trim($path, "/ \t");
        $path = preg_replace('#^(\.\./)+#', '', $path);
        if ($path === '') { return home_url('/'); }
        $base = preg_replace('/\.html$/i', '', $path);

        // top-level static pages -> WP pages (by slug or page template)
        $topMap = [
            'index' => fn() => home_url('/'),
            'shop' => fn() => (string) (function_exists('get_post_type_archive_link') ? get_post_type_archive_link('product') : false) ?: home_url('/shop/'),
            'projects' => fn() => (string) get_post_type_archive_link('projects') ?: home_url('/projects/'),
            'blog' => fn() => (string) get_permalink((int) get_option('page_for_posts')) ?: home_url('/blog/'),
            'cart' => fn() => function_exists('wc_get_page_id') ? ((string) get_permalink(wc_get_page_id('cart')) ?: home_url('/cart/')) : home_url('/cart/'),
            'checkout' => fn() => function_exists('wc_get_page_id') ? ((string) get_permalink(wc_get_page_id('checkout')) ?: home_url('/checkout/')) : home_url('/checkout/'),
            'about' => fn() => es_page_url('about'),
            'contact' => fn() => es_page_url('contact'),
            'faq' => fn() => es_page_url('faq'),
            'services' => fn() => es_page_url('services'),
            'recruitment' => fn() => es_page_url('recruitment'),
            'refund_returns' => fn() => es_page_url('refund-returns'),
            'licenses-and-honors' => fn() => es_page_url('licenses-and-honors'),
        ];
        $parts = explode('/', $base);
        $top = $parts[0];
        if (count($parts) === 1 && isset($topMap[$top])) {
            return ($topMap[$top])();
        }
        // taxonomy archives
        if (count($parts) === 2 && $parts[0] === 'product_cat') {
            $link = get_term_link($parts[1], 'product_cat');
            return is_wp_error($link) ? home_url('/' . $path) : $link;
        }
        if (count($parts) === 2 && $parts[0] === 'product_tag') {
            $link = get_term_link($parts[1], 'product_tag');
            return is_wp_error($link) ? home_url('/' . $path) : $link;
        }
        if (count($parts) === 2 && $parts[0] === 'project_type') {
            $link = get_term_link($parts[1], 'project_type');
            return is_wp_error($link) ? home_url('/' . $path) : $link;
        }
        if (count($parts) === 2 && $parts[0] === 'city') {
            $link = get_term_link($parts[1], 'city');
            return is_wp_error($link) ? home_url('/' . $path) : $link;
        }
        // blog single posts
        if (count($parts) === 2 && $parts[0] === 'blog') {
            $p = get_page_by_path($parts[1], OBJECT, 'post');
            return $p ? ((string) get_permalink($p) ?: home_url('/' . $path)) : home_url('/blog/' . $parts[1] . '/');
        }
        // service detail pages
        if (count($parts) === 2 && $parts[0] === 'services') {
            $p = get_page_by_path($parts[1], OBJECT, 'page');
            return $p ? ((string) get_permalink($p) ?: home_url('/' . $path)) : home_url('/services/' . $parts[1] . '/');
        }
        // single content by slug
        if (count($parts) === 2 && $parts[0] === 'product') {
            $p = get_page_by_path($parts[1], OBJECT, 'product');
            return $p ? ((string) get_permalink($p) ?: home_url('/' . $path)) : home_url('/' . $path);
        }
        if (count($parts) === 2 && $parts[0] === 'projects') {
            $p = get_page_by_path($parts[1], OBJECT, 'projects');
            return $p ? ((string) get_permalink($p) ?: home_url('/' . $path)) : home_url('/' . $path);
        }
        return home_url('/' . $path);
    }

    /** URL of a static page by its WP slug (tries several slug candidates). */
    function es_page_url(string $slug): string
    {
        $candidates = [$slug, str_replace('-', '_', $slug)];
        foreach ($candidates as $c) {
            $page = get_page_by_path($c);
            if ($page) { return (string) get_permalink($page) ?: home_url('/' . $c . '/'); }
        }
        // try page template match (page-<slug>.php)
        $q = new \WP_Query(['post_type' => 'page', 'posts_per_page' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'page-' . $slug . '.php']);
        if ($q->have_posts()) { return (string) get_permalink($q->posts[0]) ?: home_url('/' . $slug . '/'); }
        return home_url('/' . $slug . '/');
    }

    function es_hreflang_tags(): void
    {
        \ErfanSanat\Theme\Seo::hreflang();
    }
}

/**
 * WebP with JPG fallback <picture> element.
 */
function es_picture(string $src, string $alt = '', string $class = ''): void
{
    $webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $src);
    $hasWebp = $webp !== $src && is_file(ERFANSANAT_THEME_DIR . substr($webp, strlen(ERFANSANAT_THEME_URI)));
    if ($hasWebp) {
        printf('<picture><source srcset="%s" type="image/webp" /><img src="%s" alt="%s" loading="lazy" class="%s" /></picture>',
            esc_url($webp), esc_url($src), esc_attr($alt), esc_attr($class));
    } else {
        printf('<img src="%s" alt="%s" loading="lazy" class="%s" />', esc_url($src), esc_attr($alt), esc_attr($class));
    }
}
