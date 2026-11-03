<?php
/**
 * LegacyMetaBoxes — project meta, product_cat term meta (faq/thumbnail),
 * Hreflang meta on all object types, and the view counter. All keys use
 * their exact legacy spelling.
 *
 * @package ErfanSanat\Theme\Admin
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

if (!defined('ABSPATH')) {
    exit;
}

final class LegacyMetaBoxes
{
    public const NONCE = 'erfansanat_legacy_meta_nonce';

    public const PROJECT_IMAGE_KEYS = ['second-image', 'three-image', 'foure-image', 'five-image', 'six-image'];

    public static function hooks(): void
    {
        add_action('add_meta_boxes', [self::class, 'metaboxes']);
        add_action('save_post', [self::class, 'save_post_meta'], 10, 2);

        // product_cat term meta
        add_action('product_cat_add_form_fields', [self::class, 'term_form']);
        add_action('product_cat_edit_form_fields', [self::class, 'term_form']);
        add_action('created_product_cat', [self::class, 'save_term_meta']);
        add_action('edited_product_cat', [self::class, 'save_term_meta']);

        // sortable same_key_hrefl column
        add_filter('manage_edit-post_columns', [self::class, 'hreflang_column']);
        add_filter('manage_edit-page_columns', [self::class, 'hreflang_column']);
        add_filter('manage_edit-product_columns', [self::class, 'hreflang_column']);
        add_filter('manage_edit-projects_columns', [self::class, 'hreflang_column']);
        add_action('manage_posts_custom_column', [self::class, 'hreflang_column_content'], 10, 2);
        add_filter('manage_posts_sortable_columns', [self::class, 'sortable_columns']);
        add_action('pre_get_posts', [self::class, 'sort_by_hreflang']);

        // view counter
        add_action('wp', [self::class, 'count_view']);
    }

    public static function metaboxes(): void
    {
        // Projects gallery + info
        add_meta_box('erfansanat_project_info', __('اطلاعات پروژه', 'erfansanat'), [self::class, 'render_project'], 'projects', 'normal', 'high');
        // Hreflang on all main types
        foreach (['post', 'page', 'product', 'projects'] as $screen) {
            add_meta_box('erfansanat_hreflang', __('ترجمه‌ها / Hreflang', 'erfansanat'), [self::class, 'render_hreflang'], $screen, 'side', 'default');
        }
    }

    public static function save_post_meta(int $post_id, \WP_Post $post): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!in_array($post->post_type, ['post', 'page', 'product', 'projects'], true)) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        if (!isset($_POST[self::NONCE]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE])), 'erfansanat_legacy_meta')) {
            return;
        }
        $in = isset($_POST['es_legacy']) && is_array($_POST['es_legacy']) ? wp_unslash($_POST['es_legacy']) : [];

        // Project images + fields
        if ($post->post_type === 'projects') {
            foreach (self::PROJECT_IMAGE_KEYS as $key) {
                if (array_key_exists($key, $in)) {
                    update_post_meta($post_id, $key, absint($in[$key]));
                }
            }
            if (array_key_exists('finish-pro-time', $in)) {
                update_post_meta($post_id, 'finish-pro-time', sanitize_text_field((string) $in['finish-pro-time']));
            }
            if (array_key_exists('mortabet_mahsole_2', $in)) {
                $ids = array_filter(array_map('absint', (array) $in['mortabet_mahsole_2']));
                update_post_meta($post_id, 'mortabet_mahsole_2', wp_json_encode(array_values($ids)));
            }
        }

        // Hreflang
        foreach (['hrefmeta_fa', 'hrefmeta_en', 'hrefmeta_ar', 'same_key_hrefl'] as $key) {
            if (array_key_exists($key, $in)) {
                update_post_meta($post_id, $key, sanitize_text_field((string) $in[$key]));
            }
        }

        // View counter editable
        if (array_key_exists('number2-viewc', $in)) {
            update_post_meta($post_id, 'number2-viewc', absint($in['number2-viewc']));
        }
    }

    /* ---------------- product_cat term meta ---------------- */

    public static function term_form($term): void
    {
        $term_id = is_object($term) ? $term->term_id : 0;
        $thumb = get_term_meta($term_id, 'thumbnail', true);
        $faq = get_term_meta($term_id, 'faq', true);
        $faq = is_string($faq) ? (json_decode($faq, true) ?: maybe_unserialize($faq)) : (array) $faq;
        echo '<h3>' . esc_html__('تصویر بندانگشتی دسته', 'erfansanat') . '</h3>';
        printf('<input type="number" name="es_term[thumbnail]" value="%d" class="small-text"> <span class="description">%s</span>', esc_attr((string) $thumb), esc_html__('ID تصویر مدیا', 'erfansanat'));
        echo '<h3>' . esc_html__('سوالات متداول دسته (faq)', 'erfansanat') . '</h3><table class="form-table">';
        $rows = is_array($faq) ? $faq : [];
        foreach ($rows as $row) {
            if (!is_array($row)) { continue; }
            printf('<tr><td><input name="es_term[faq][][name_question]" value="%s" placeholder="name_question"></td><td><input name="es_term[faq][][answer_question]" value="%s" placeholder="answer_question"></td></tr>', esc_attr((string)($row['name_question'] ?? '')), esc_attr((string)($row['answer_question'] ?? '')));
        }
        echo '<tr><td><input name="es_term[faq][][name_question]" placeholder="name_question"></td><td><input name="es_term[faq][][answer_question]" placeholder="answer_question"></td></tr></table>';
    }

    public static function save_term_meta(int $term_id): void
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }
        $in = isset($_POST['es_term']) && is_array($_POST['es_term']) ? wp_unslash($_POST['es_term']) : [];
        if (array_key_exists('thumbnail', $in)) {
            update_term_meta($term_id, 'thumbnail', absint($in['thumbnail']));
        }
        if (array_key_exists('faq', $in) && is_array($in['faq'])) {
            $rows = [];
            $i = 0;
            foreach ($in['faq'] as $row) {
                $q = sanitize_text_field((string)($row['name_question'] ?? ''));
                $a = sanitize_text_field((string)($row['answer_question'] ?? ''));
                if ($q === '' && $a === '') { continue; }
                $rows['item-' . $i++] = ['name_question' => $q, 'answer_question' => $a];
            }
            update_term_meta($term_id, 'faq', $rows);
        }
    }

    /* ---------------- sortable hreflang column ---------------- */

    public static function hreflang_column(array $cols): array
    {
        $cols['same_key_hrefl'] = __('کلید Hreflang', 'erfansanat');
        return $cols;
    }

    public static function hreflang_column_content(string $col, int $post_id): void
    {
        if ($col === 'same_key_hrefl') {
            echo esc_html((string) get_post_meta($post_id, 'same_key_hrefl', true));
        }
    }

    public static function sortable_columns(array $cols): array
    {
        $cols['same_key_hrefl'] = 'same_key_hrefl';
        return $cols;
    }

    public static function sort_by_hreflang(\WP_Query $q): void
    {
        if (is_admin() && $q->is_main_query() && $q->get('orderby') === 'same_key_hrefl') {
            $q->set('meta_key', 'same_key_hrefl');
            $q->set('orderby', 'meta_value');
        }
    }

    /* ---------------- view counter ---------------- */

    public static function count_view(): void
    {
        if (!is_singular(['post', 'projects', 'product'])) {
            return;
        }
        $post_id = get_queried_object_id();
        if (!$post_id || is_preview()) {
            return;
        }
        // Do not count admins/editors repeatedly in the same session.
        if (current_user_can('edit_post', $post_id)) {
            return;
        }
        $count = get_post_meta($post_id, 'number2-viewc', true);
        if ($count === '' || $count === false) {
            $count = get_post_meta($post_id, 'number-viewc', true); // legacy fallback
        }
        update_post_meta($post_id, 'number2-viewc', absint($count) + 1);
    }

    /* ---------------- render ---------------- */

    public static function render_project(\WP_Post $post): void
    {
        wp_nonce_field('erfansanat_legacy_meta', self::NONCE);
        echo '<table class="form-table">';
        foreach (self::PROJECT_IMAGE_KEYS as $key) {
            printf('<tr><th><label>%s</label></th><td><input type="number" name="es_legacy[%s]" value="%d" class="small-text"> <span class="description">%s</span></td></tr>', esc_html($key), esc_attr($key), esc_attr((int) get_post_meta($post->ID, $key, true)), esc_html__('ID تصویر مدیا', 'erfansanat'));
        }
        printf('<tr><th><label>%s</label></th><td><input type="text" name="es_legacy[finish-pro-time]" value="%s" placeholder="1404/01/01"></td></tr>', esc_html__('finish-pro-time'), esc_attr((string) get_post_meta($post->ID, 'finish-pro-time', true)));
        $m = get_post_meta($post->ID, 'mortabet_mahsole_2', true);
        $m = is_string($m) ? (json_decode($m, true) ?: []) : (array) $m;
        printf('<tr><th><label>%s</label></th><td><input type="text" name="es_legacy[mortabet_mahsole_2][]" value="%s" class="regular-text"></td></tr>', esc_html__('mortabet_mahsole_2 (product IDs)'), esc_attr(implode(',', array_map('strval', (array) $m))));
        echo '</table>';
        self::render_hreflang_inner($post->ID);
    }

    public static function render_hreflang(\WP_Post $post): void
    {
        wp_nonce_field('erfansanat_legacy_meta', self::NONCE);
        self::render_hreflang_inner($post->ID);
    }

    private static function render_hreflang_inner(int $post_id): void
    {
        foreach (['hrefmeta_fa' => 'fa', 'hrefmeta_en' => 'en', 'hrefmeta_ar' => 'ar', 'same_key_hrefl' => 'کلید مشترک'] as $key => $label) {
            printf('<p><label>%s</label><br><input type="text" name="es_legacy[%s]" value="%s" class="regular-text"></p>', esc_html($label), esc_attr($key), esc_attr((string) get_post_meta($post_id, $key, true)));
        }
        printf('<p><label>%s</label><br><input type="number" name="es_legacy[number2-viewc]" value="%d" class="small-text"></p>', esc_html__('تعداد بازدید (number2-viewc)'), esc_attr((int) get_post_meta($post_id, 'number2-viewc', true)));
    }
}
