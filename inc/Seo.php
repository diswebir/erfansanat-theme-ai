<?php
/**
 * Seo — dynamic hreflang <link> tags from the legacy hrefmeta_* meta keys
 * (with sensible site-level fallbacks), plus JSON-LD output preserved
 * from the static mirror.
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

if (!defined('ABSPATH')) {
    exit;
}

final class Seo
{
    public static function hooks(): void
    {
        add_action('wp_head', [self::class, 'hreflang'], 1);
    }

    /**
     * Echo hreflang links. On singular content use hrefmeta_fa/en/ar meta;
     * otherwise fall back to the site root language URLs.
     */
    public static function hreflang(): void
    {
        $fa = home_url('/');
        $en = home_url('/en/');
        $ar = home_url('/ar/');
        if (is_singular()) {
            $id = get_queried_object_id();
            $mFa = get_post_meta($id, 'hrefmeta_fa', true);
            $mEn = get_post_meta($id, 'hrefmeta_en', true);
            $mAr = get_post_meta($id, 'hrefmeta_ar', true);
            if ($mFa) { $fa = $mFa; }
            if ($mEn) { $en = $mEn; }
            if ($mAr) { $ar = $mAr; }
        }
        printf('<link rel="alternate" hreflang="fa" href="%s" />' . "\n", esc_url($fa));
        printf('<link rel="alternate" hreflang="en" href="%s" />' . "\n", esc_url($en));
        printf('<link rel="alternate" hreflang="ar" href="%s" />' . "\n", esc_url($ar));
        printf('<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url($fa));
    }
}

