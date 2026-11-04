<?php
/** SEO plugin compatibility with safe fallback to legacy Rank Math metadata. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Seo;

use ErfanSanat\Theme\Contracts\ServiceInterface;
use WP_Post;

final class SeoAudit implements ServiceInterface
{
    public function register(): void
    {
        add_filter('pre_get_document_title', [$this, 'filterDocumentTitle'], 20);
        add_filter('get_canonical_url', [$this, 'filterCanonicalUrl'], 20, 2);
        add_action('wp_head', [$this, 'printDescription'], 1);
    }

    /** @param mixed $title */
    public function filterDocumentTitle(mixed $title): string
    {
        return $this->documentTitle(is_scalar($title) ? (string) $title : '');
    }

    public function documentTitle(string $currentTitle, int $postId = 0): string
    {
        if ($this->seoPluginActive()) return $currentTitle;
        $postId = $postId > 0 ? $postId : (is_singular() ? (int) get_queried_object_id() : 0);
        if ($postId < 1) return $currentTitle;
        $legacy = $this->legacyMetaValue($postId, 'title');
        return $legacy !== '' ? $legacy : $currentTitle;
    }

    /** @param WP_Post|null $post */
    public function filterCanonicalUrl(string $canonicalUrl, ?WP_Post $post = null): string
    {
        $postId = $post instanceof WP_Post ? (int) $post->ID : (is_singular() ? (int) get_queried_object_id() : 0);
        return $this->canonicalUrl($canonicalUrl, $postId);
    }

    public function canonicalUrl(string $canonicalUrl, int $postId = 0): string
    {
        if ($this->seoPluginActive()) return $canonicalUrl;
        $postId = $postId > 0 ? $postId : (is_singular() ? (int) get_queried_object_id() : 0);
        if ($postId < 1) return $canonicalUrl;
        $legacy = $this->legacyMetaValue($postId, 'canonical_url');
        return $legacy !== '' ? esc_url_raw($legacy) : $canonicalUrl;
    }

    public function printDescription(): void
    {
        if (is_admin() || $this->seoPluginActive()) return;
        $markup = $this->descriptionMarkup(is_singular() ? (int) get_queried_object_id() : 0);
        if ($markup !== '') echo $markup . "\n";
    }

    public function descriptionMarkup(int $postId): string
    {
        if ($this->seoPluginActive() || $postId < 1) return '';
        $description = $this->legacyMetaValue($postId, 'description');
        if ($description === '') return '';
        return '<meta name="description" content="' . esc_attr($description) . '" />';
    }

    public function legacyMetaValue(int $postId, string $field): string
    {
        $keys = match ($field) {
            'title' => ['_rank_math_title', 'rank_math_title'],
            'description' => ['_rank_math_description', 'rank_math_description'],
            'canonical_url' => ['_rank_math_canonical_url', 'rank_math_canonical_url'],
            default => [],
        };
        foreach ($keys as $key) {
            $value = get_post_meta($postId, $key, true);
            if (is_scalar($value) && trim((string) $value) !== '') {
                return $field === 'canonical_url' ? esc_url_raw((string) $value) : sanitize_text_field((string) $value);
            }
        }
        return '';
    }

    public function seoPluginActive(): bool
    {
        return defined('SEOPRESS_VERSION') || class_exists('SEOPress') || defined('RANK_MATH_VERSION') || function_exists('rank_math');
    }
}
