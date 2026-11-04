<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Seo;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class HreflangTags implements ServiceInterface
{
    /** @return array<string,string> */
    public static function map(): array { return ['fa' => 'hrefmeta_fa', 'en' => 'hrefmeta_en', 'ar' => 'hrefmeta_ar']; }
    public function register(): void { add_action('wp_head', [$this, 'printTags'], 5); }
    /** @return array<string,string> */
    public static function urlsForCurrent(): array {
        $urls = [];
        if (is_singular()) {
            $id = (int) get_queried_object_id();
            foreach (self::map() as $lang => $key) { $u = (string) get_post_meta($id, $key, true); if ($u !== '') $urls[$lang] = $u; }
        } elseif (is_tax() || is_category() || is_tag()) {
            $term = get_queried_object();
            if ($term instanceof \WP_Term) foreach (self::map() as $lang => $key) { $u = (string) get_term_meta($term->term_id, $key, true); if ($u !== '') $urls[$lang] = $u; }
        }
        return $urls;
    }
    public function printTags(): void {
        $urls = self::urlsForCurrent();
        foreach ($urls as $lang => $url) printf('<link rel="alternate" hreflang="%s" href="%s" />' . "\n", esc_attr($lang), esc_url($url));
        if (isset($urls['fa'])) printf('<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url($urls['fa']));
    }
}
