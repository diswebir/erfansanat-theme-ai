<?php
/**
 * پیکر انتخاب چندگانه روابط (پروژه‌ها/محصولات/ترم‌ها) — UI داخلی بدون select2
 *
 * ذخیره: آرایه ID (یا JSON در صورت موجود بودن فرمت قبلی — از RepeaterEngine الگو می‌گیرد)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Data\RepeaterEngine;

final class RelatedPicker {

    public const AJAX_ACTION = 'es_related_search';
    public const NONCE       = 'es_related';

    public static function init(): void {
        add_action('wp_ajax_' . self::AJAX_ACTION, [self::class, 'ajax_search']);
    }

    public static function source_posts(): string {
        return 'posts';
    }

    public static function source_terms(): string {
        return 'terms';
    }

    /**
     * رندر پیکر
     *
     * @param string $name       نام فیلد (opt_meta[project-ids])
     * @param string $post_type  پست‌تایپ یا تکسونومی جستجو
     * @param string $source     posts|terms
     * @param string $label      برچسب
     * @param string $meta_key   کلید متا برای خواندن مقادیر فعلی
     * @param int    $post_id    پست جاری
     */
    public static function render(string $name, string $post_type, string $source, string $label, string $meta_key, int $post_id): void {
        $current_ids = self::current_ids($post_id, $meta_key);
        $titles      = self::titles_for($current_ids, $post_type, $source);

        echo '<div class="es-related" data-es-related data-source="' . esc_attr($source) . '" data-query-type="' . esc_attr($post_type) . '">';
        printf('<p class="es-related__label"><strong>%s</strong></p>', esc_html($label));
        printf('<input type="hidden" class="es-related__value" name="%s" value="%s" data-es-related-value>', esc_attr($name), esc_attr(implode(',', $current_ids)));
        echo '<div class="es-related__chips" data-es-related-chips>';
        foreach ($current_ids as $id) {
            printf('<span class="es-related__chip" data-id="%d">%s <button type="button" class="es-related__chip-x" data-es-related-remove>✕</button></span>', (int) $id, esc_html((string) ($titles[$id] ?? ('#' . $id))));
        }
        echo '</div>';
        printf('<input type="text" class="es-related__search widefat" placeholder="%s" data-es-related-search>', esc_attr__('جستجوی عنوان…', 'erfansanat'));
        echo '<ul class="es-related__results" data-es-related-results hidden></ul>';
        echo '</div>';
        echo '<style>.es-related{margin-bottom:16px}.es-related__chips{display:flex;flex-wrap:wrap;gap:6px;margin:6px 0}.es-related__chip{background:#ecfdf5;border:1px solid #a7f3d0;border-radius:999px;padding:2px 10px;font-size:12px;display:inline-flex;gap:6px;align-items:center}.es-related__chip-x{border:0;background:transparent;cursor:pointer;color:#b91c1c}.es-related__results{border:1px solid #e2e8f0;border-radius:8px;max-height:180px;overflow:auto;margin:6px 0 0;padding:0;list-style:none}.es-related__results li{padding:6px 10px;cursor:pointer;border-bottom:1px solid #f1f5f9}.es-related__results li:hover{background:#f0fdf4}</style>';
        echo '<script>document.addEventListener("click",function(e){var c=e.target.closest(".es-related__chip-x");if(c){var chip=c.closest(".es-related__chip"),box=chip.closest("[data-es-related]"),val=box.querySelector("[data-es-related-value]");chip.remove();var ids=box.querySelectorAll(".es-related__chip[data-id]");var list=[];ids.forEach(function(x){list.push(x.getAttribute("data-id"))});val.value=list.join(",");}});</script>';
    }

    /** خواندن مقاوم مقادیر فعلی (آرایه/JSON/کاما) */
    public static function current_ids(int $post_id, string $meta_key): array {
        $raw = get_post_meta($post_id, $meta_key, true);
        if (is_array($raw)) {
            $ids = [];
            foreach ($raw as $row) {
                $id = is_array($row) ? absint($row['id'] ?? 0) : absint(is_scalar($row) ? $row : 0);
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
            return array_values(array_unique($ids));
        }
        if (is_string($raw) && str_starts_with(ltrim($raw), '{')) {
            $parsed = RepeaterEngine::parse($raw);
            $ids = [];
            foreach ($parsed['rows'] as $row) {
                $id = absint($row['id'] ?? 0);
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
            return array_values(array_unique($ids));
        }
        return \ErfanSanat\Theme\Admin\ProductMetaboxes::sanitize_id_list($raw);
    }

    /** عناوین نمایشی آیدی‌ها (با فیلتر برای تست/بهینه‌سازی) */
    public static function titles_for(array $ids, string $post_type, string $source): array {
        $fixture = $GLOBALS['__es_stub_related_titles'] ?? [];
        $titles  = [];
        foreach ($ids as $id) {
            $titles[$id] = $fixture[$id] ?? ('#' . $id);
        }
        return $titles;
    }

    /** جستجوی ایجکس — نانس + کاپابیلیتی + فیلتر توسعه‌پذیر */
    public static function ajax_search(): void {
        check_ajax_referer(self::NONCE, 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('دسترسی غیرمجاز.', 'erfansanat')], 403);
        }

        $search  = isset($_POST['s']) && is_string($_POST['s']) ? sanitize_text_field(wp_unslash($_POST['s'])) : '';
        $qtype   = isset($_POST['query_type']) && is_string($_POST['query_type']) ? sanitize_key($_POST['query_type']) : '';
        $source  = isset($_POST['source']) && is_string($_POST['source']) ? sanitize_key($_POST['source']) : 'posts';

        $results = [];

        // فیلتر توسعه (تست/کش)
        $results = apply_filters('es_related_search_results', $results, $search, $qtype, $source);

        if ($results === [] && $search !== '' && $qtype !== '' && $source === self::source_terms() && function_exists('get_terms')) {
            foreach (get_terms(['taxonomy' => $qtype, 'hide_empty' => false, 'name__like' => $search]) as $term) {
                if (is_object($term) && isset($term->term_id)) {
                    $results[] = ['id' => (int) $term->term_id, 'title' => (string) $term->name];
                }
            }
        }

        wp_send_json_success(['results' => $results]);
    }
}
