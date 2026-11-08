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
        add_action('wp_head', [self::class, 'jsonld'], 8);
    }

    /**
     * JSON-LD structured data: Organization on every page, Product on
     * single products, BlogPosting on single posts.
     */
    public static function jsonld(): void
    {
        $graph = [];
        $org = [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
            'logo' => ERFANSANAT_THEME_URI . 'assets/images/logo.png',
            'telephone' => '031-91091011',
            'email' => 'info@erfansanat.com',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Isfahan',
                'addressCountry' => 'IR',
            ],
        ];
        $graph[] = $org;

        if (is_singular('product')) {
            $id = get_queried_object_id();
            $brand = get_post_meta($id, 'pa_brand', true);
            $prod = [
                '@type' => 'Product',
                'name' => get_the_title($id),
                'description' => wp_strip_all_tags(get_post_field('post_excerpt', $id) ?: get_post_field('post_content', $id)),
                'sku' => get_post_meta($id, 'product_id', true) ?: (string) $id,
                'brand' => ['@type' => 'Brand', 'name' => $brand ?: get_bloginfo('name')],
                'offers' => [
                    '@type' => 'Offer',
                    'url' => get_permalink($id),
                    'priceCurrency' => 'IRR',
                    'availability' => get_post_meta($id, 'true_sale', true) === 'true'
                        ? 'https://schema.org/InStock' : 'https://schema.org/PreOrder',
                ],
            ];
            $img = get_post_meta($id, 'galerry_produ', true);
            if ($img) { $prod['image'] = wp_get_attachment_image_url((int) $img, 'large') ?: $img; }
            $graph[] = $prod;
        } elseif (is_singular('post')) {
            $id = get_queried_object_id();
            $graph[] = [
                '@type' => 'BlogPosting',
                'headline' => get_the_title($id),
                'datePublished' => get_the_date('c', $id),
                'dateModified' => get_the_modified_date('c', $id),
                'author' => ['@type' => 'Organization', 'name' => get_bloginfo('name')],
                'publisher' => $org,
            ];
        }

        echo '<script type="application/ld+json">' . wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph]) . '</script>' . "\n";
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

