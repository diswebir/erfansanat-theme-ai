<?php
/**
 * متاهرفلنگ — کلیدهای تاریخی hrefmeta_fa/en/ar + same_key_hrefl روی پست‌ها و ترم‌ها
 * خروجی head با x-default و dedupe.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

final class HreflangMeta {

    public const NONCE_ACTION = 'es_hreflang_meta';
    public const NONCE_FIELD  = 'es_hreflang_meta_nonce';
    public const PREFIX       = 'opt_href';

    /** کلیدهای زبان به ترتیب میرور */
    public const LANG_KEYS = ['hrefmeta_fa', 'hrefmeta_en', 'hrefmeta_ar'];
    public const X_KEY     = 'same_key_hrefl';

    /** @var array<string,string> hreflangهایی که در همین رندر چاپ شده‌اند (dedupe) */
    private static array $emitted = [];

    public static function init(): void {
        add_action('add_meta_boxes', [self::class, 'register_metabox']);
        add_action('save_post', [self::class, 'save'], 10, 2);
        add_action('wp_head', [self::class, 'render_head'], 5);

        foreach (self::taxonomies() as $tax) {
            add_action("{$tax}_edit_form_fields", [self::class, 'term_fields_edit'], 10, 2);
            add_action("{$tax}_add_form_fields", [self::class, 'term_fields_add'], 10);
            add_action("edited_{$tax}", [self::class, 'save_term'], 10, 2);
            add_action("created_{$tax}", [self::class, 'save_term'], 10, 2);
        }
    }

    /** پست‌تایپ‌های هدف (فیلتر توسعه) */
    public static function post_types(): array {
        $types = ['post', 'page', 'product', 'projects', 'services'];
        return (array) apply_filters('es_hreflang_post_types', $types);
    }

    /** تاکسونومی‌های هدف */
    public static function taxonomies(): array {
        return (array) apply_filters('es_hreflang_taxonomies', ['product_cat', 'city', 'project_type']);
    }

    public static function register_metabox(): void {
        foreach (self::post_types() as $type) {
            add_meta_box(
                'es-hreflang-meta',
                __('لینک‌های hreflang (عرفان صنعت)', 'erfansanat'),
                [self::class, 'render'],
                $type,
                'normal',
                'default'
            );
        }
    }

    /** اسکیم فیلدها */
    public static function fields(): array {
        $fields = [];
        foreach (self::LANG_KEYS as $key) {
            $fields[$key] = ['type' => 'url', 'label' => strtoupper(substr($key, -2))];
        }
        $fields[self::X_KEY] = ['type' => 'url', 'label' => __('x-default (same_key_hrefl)', 'erfansanat')];
        return $fields;
    }

    public static function render(object $post): void {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_FIELD);
        $post_id = (int) $post->ID;

        echo '<div class="es-hreflang" data-es-hreflang>';
        echo '<p class="description">' . esc_html__('آدرس نسخه‌ی زبانی همین صفحه (کلیدهای تاریخی دیتابیس).', 'erfansanat') . '</p>';
        foreach (self::fields() as $key => $field) {
            $value = (string) get_post_meta($post_id, $key, true);
            printf(
                '<p class="es-hreflang__row"><label>%1$s<br><input type="url" class="widefat" dir="ltr" name="%4$s[%2$s]" value="%3$s"></label></p>',
                esc_html((string) $field['label']),
                esc_attr((string) $key),
                esc_attr($value),
                esc_attr(self::PREFIX)
            );
        }
        echo '</div>';
    }

    public static function save(int $post_id, object $post): void {
        if (!isset($_POST[self::NONCE_FIELD])) {
            return;
        }
        $nonce = is_string($_POST[self::NONCE_FIELD]) ? $_POST[self::NONCE_FIELD] : '';
        if (!wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (function_exists('wp_is_post_autosave') && wp_is_post_autosave($post_id)) {
            return;
        }
        if (function_exists('wp_is_post_revision') && wp_is_post_revision($post_id)) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        $post_type = is_object($post) ? (string) ($post->post_type ?? '') : '';
        if (!in_array($post_type, self::post_types(), true)) {
            return;
        }

        $meta = isset($_POST[self::PREFIX]) && is_array($_POST[self::PREFIX]) ? wp_unslash($_POST[self::PREFIX]) : [];
        foreach (self::fields() as $key => $field) {
            if (!array_key_exists((string) $key, $meta)) {
                continue;
            }
            $raw = is_scalar($meta[$key]) ? trim((string) $meta[$key]) : '';
            update_post_meta($post_id, (string) $key, esc_url_raw($raw));
        }

        do_action('es_hreflang_meta_saved', $post_id, $meta);
    }

    /** فرم ترم (ویرایش) */
    public static function term_fields_edit(object $term): void {
        echo '<tr class="form-field es-hreflang-term"><th colspan="2"><h3>' . esc_html__('لینک‌های hreflang (عرفان صنعت)', 'erfansanat') . '</h3></th></tr>';
        foreach (self::fields() as $key => $field) {
            $value = (string) get_term_meta((int) $term->term_id, $key, true);
            printf(
                '<tr class="form-field es-hreflang-term"><th><label for="%1$s">%2$s</label></th><td><input type="url" id="%1$s" name="%4$s[%1$s]" value="%3$s" dir="ltr" class="regular-text"></td></tr>',
                esc_attr((string) $key),
                esc_html((string) $field['label']),
                esc_attr($value),
                esc_attr(self::PREFIX)
            );
        }
    }

    /** فرم ترم (افزودن) */
    public static function term_fields_add(): void {
        echo '<div class="form-field es-hreflang-term"><h3>' . esc_html__('لینک‌های hreflang (عرفان صنعت)', 'erfansanat') . '</h3>';
        foreach (self::fields() as $key => $field) {
            printf(
                '<p><label>%1$s<br><input type="url" name="%3$s[%2$s]" value="" dir="ltr" class="regular-text"></label></p>',
                esc_html((string) $field['label']),
                esc_attr((string) $key),
                esc_attr(self::PREFIX)
            );
        }
        echo '</div>';
    }

    public static function save_term(int $term_id, int $tt_id = 0): void {
        if (!current_user_can('manage_product_terms') && !current_user_can('manage_terms')) {
            return;
        }
        $meta = isset($_POST[self::PREFIX]) && is_array($_POST[self::PREFIX]) ? wp_unslash($_POST[self::PREFIX]) : [];
        if ($meta === []) {
            return;
        }
        foreach (self::fields() as $key => $field) {
            if (!array_key_exists((string) $key, $meta)) {
                continue;
            }
            $raw = is_scalar($meta[$key]) ? trim((string) $meta[$key]) : '';
            update_term_meta($term_id, (string) $key, esc_url_raw($raw));
        }
    }

    /** خروجی head برای شیء جاری (پست یا ترم) */
    public static function render_head(): void {
        // هماهنگی با افزونه hreflangpch: در صورت فعال‌بودن فیلتر، قالب خروجی نمی‌دهد (جلوگیری از تکرار)
        if (apply_filters('es_disable_builtin_hreflang', false)) {
            return;
        }
        $links = self::current_links();
        foreach ($links as $hreflang => $url) {
            if ($url === '' || isset(self::$emitted[$hreflang])) {
                continue;
            }
            self::$emitted[$hreflang] = $url;
            printf('<link rel="alternate" hreflang="%s" href="%s">' . "\n", esc_attr($hreflang), esc_url($url));
        }
    }

    /**
     * لینک‌های شیء جاری — از متای پست یا ترم؛ x-default → same_key_hrefl وگرنه fa
     *
     * @return array<string,string> hreflang => url
     */
    public static function current_links(): array {
        $values = self::current_values();
        if ($values === []) {
            return [];
        }

        $out   = [];
        $xdflt = (string) ($values[self::X_KEY] ?? '');
        foreach (['fa', 'en', 'ar'] as $i => $lang) {
            $url = (string) ($values[self::LANG_KEYS[$i]] ?? '');
            if ($url === '') {
                continue;
            }
            $out[$lang] = $url;
        }
        // x-default: same_key_hrefl وگرنه آدرس fa
        if ($xdflt !== '') {
            $out['x-default'] = $xdflt;
        } elseif (isset($out['fa'])) {
            $out['x-default'] = $out['fa'];
        }

        // fa و x-default با URL یکسان هر دو معتبرند (دستورالعمل‌های مجزا) — dedupe فقط per-hreflang
        return $out;
    }

    /** مقادیر خام شیء جاری */
    private static function current_values(): array {
        global $wp_query;

        $values = [];
        if (function_exists('is_singular') && is_singular()) {
            $post_id = (int) get_queried_object_id();
            if ($post_id > 0) {
                foreach (array_keys(self::fields()) as $key) {
                    $values[$key] = (string) get_post_meta($post_id, $key, true);
                }
            }
            return $values;
        }

        if (function_exists('is_tax') || function_exists('is_category')) {
            $is_term = (function_exists('is_tax') && is_tax()) || (function_exists('is_category') && is_category()) || (function_exists('is_tag') && is_tag());
            if ($is_term) {
                $term = null;
                if (isset($wp_query) && is_object($wp_query) && isset($wp_query->queried_object)) {
                    $term = $wp_query->queried_object;
                } elseif (function_exists('get_queried_object')) {
                    $term = get_queried_object();
                }
                $term_id = is_object($term) && isset($term->term_id) ? (int) $term->term_id : 0;
                if ($term_id > 0) {
                    foreach (array_keys(self::fields()) as $key) {
                        $values[$key] = (string) get_term_meta($term_id, $key, true);
                    }
                }
            }
        }

        return $values;
    }
}
