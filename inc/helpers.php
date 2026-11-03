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
    function es_hreflang_tags(): void
    {
        \ErfanSanat\Theme\Seo::hreflang();
    }
}
