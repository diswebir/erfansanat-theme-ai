<?php
/**
 * ProductMetaboxes — native replacement for the JetEngine product meta.
 *
 * Manages EVERY legacy meta key with its exact (historic) spelling so
 * existing live data keeps working after JetEngine is disabled.
 * Repeaters are stored in the legacy {"item-0": {...}} format and are
 * read compatibly whether stored serialized or as JSON.
 *
 * @package ErfanSanat\Theme\Admin
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

if (!defined('ABSPATH')) {
    exit;
}

final class ProductMetaboxes
{
    public const NONCE = 'erfansanat_product_meta_nonce';

    /** Text/ID fields with Quick Edit support. */
    public const TEXT_FIELDS = [
        'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id',
        'zireonvan', 'english_titl', 'before-sale-c',
    ];

    /** Repeaters: key => subfields. */
    public const REPEATERS = [
        'product-feature'       => ['add-new-feature'],
        'product-feature2_copy' => ['add-new-feature', 'vaule_of_variable'],
        'usage_product'         => ['add-new-usage'],
        'faq'                   => ['name_question', 'answer_question'],
    ];

    public static function hooks(): void
    {
        add_action('add_meta_boxes', [self::class, 'metabox']);
        add_action('save_post_product', [self::class, 'save'], 10, 2);
        add_action('quick_edit_custom_box', [self::class, 'quick_edit'], 10, 2);
        add_action('bulk_edit_custom_box', [self::class, 'quick_edit'], 10, 2);
        add_action('wp_ajax_erfansanat_product_quick_save', [self::class, 'ajax_quick_save']);
    }

    public static function metabox(): void
    {
        add_meta_box(
            'erfansanat_product_info',
            __('اطلاعات محصول (عرفان صنعت)', 'erfansanat'),
            [self::class, 'render'],
            'product',
            'normal',
            'high'
        );
    }

    /* ---------------- READ (legacy-compatible) ---------------- */

    /**
     * Read a repeater in legacy {"item-N": {...}} format, accepting both
     * serialized arrays and JSON strings.
     */
    public static function read_repeater(int $post_id, string $key): array
    {
        $raw = get_post_meta($post_id, $key, true);
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : maybe_unserialize($raw);
        }
        if (!is_array($raw)) {
            return [];
        }
        // Normalize to a list of row arrays.
        $rows = [];
        foreach ($raw as $k => $v) {
            if (is_array($v)) {
                $rows[] = $v;
            }
        }
        return $rows;
    }

    /**
     * Gallery: primary legacy key `galerry_produ` (CSV of IDs), with
     * fallbacks to WooCommerce standard and older keys.
     */
    public static function read_gallery(int $post_id): array
    {
        $ids = [];
        $raw = get_post_meta($post_id, 'galerry_produ', true);
        if (is_string($raw) && $raw !== '') {
            $ids = array_filter(array_map('absint', explode(',', $raw)));
        }
        if (!$ids) {
            $wc = get_post_meta($post_id, '_product_image_gallery', true);
            if (is_string($wc) && $wc !== '') {
                $ids = array_filter(array_map('absint', explode(',', $wc)));
            }
        }
        if (!$ids) {
            foreach (['gallery-filed', 'image-2', 'image-3', 'image-4', 'image-5', 'image-6'] as $k) {
                $v = get_post_meta($post_id, $k, true);
                if ($v) {
                    $ids[] = absint(is_array($v) ? ($v['id'] ?? 0) : $v);
                }
            }
        }
        return array_values(array_filter($ids));
    }

    /* ---------------- WRITE (legacy format) ---------------- */

    public static function save(int $post_id, \WP_Post $post): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        if (!isset($_POST[self::NONCE]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE])), 'erfansanat_product_meta')) {
            return;
        }
        $in = isset($_POST['es_product']) && is_array($_POST['es_product']) ? wp_unslash($_POST['es_product']) : [];

        foreach (self::TEXT_FIELDS as $key) {
            if (array_key_exists($key, $in)) {
                update_post_meta($post_id, $key, sanitize_text_field((string) $in[$key]));
            }
        }
        if (array_key_exists('true_sale', $in)) {
            update_post_meta($post_id, 'true_sale', $in['true_sale'] === 'true' ? 'true' : 'false');
        }
        if (array_key_exists('product-catalog-file', $in)) {
            update_post_meta($post_id, 'product-catalog-file', esc_url_raw((string) $in['product-catalog-file']));
        }
        if (array_key_exists('galerry_produ', $in)) {
            $ids = array_filter(array_map('absint', (array) $in['galerry_produ']));
            update_post_meta($post_id, 'galerry_produ', implode(',', $ids));
        }
        if (array_key_exists('project-ids', $in)) {
            $ids = array_filter(array_map('absint', (array) $in['project-ids']));
            update_post_meta($post_id, 'project-ids', wp_json_encode(array_values($ids)));
        }
        if (array_key_exists('usage_countr', $in)) {
            $ids = array_filter(array_map('absint', (array) $in['usage_countr']));
            update_post_meta($post_id, 'usage_countr', wp_json_encode(array_values($ids)));
        }
        foreach (self::REPEATERS as $key => $subfields) {
            if (!array_key_exists($key, $in) || !is_array($in[$key])) {
                continue;
            }
            $rows = [];
            $i = 0;
            foreach ($in[$key] as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $clean = [];
                foreach ($subfields as $sf) {
                    $clean[$sf] = sanitize_text_field((string) ($row[$sf] ?? ''));
                }
                if (implode('', $clean) === '') {
                    continue;
                }
                $rows['item-' . $i++] = $clean; // legacy {"item-0": {...}} format
            }
            update_post_meta($post_id, $key, $rows);
        }
    }

    /* ---------------- QUICK EDIT ---------------- */

    public static function quick_edit(string $column, string $post_type): void
    {
        if ($post_type !== 'product' || $column !== 'erfansanat_ids') {
            return;
        }
        wp_nonce_field('erfansanat_product_meta', self::NONCE);
        echo '<fieldset class="inline-edit-col-right"><div class="inline-edit-col">';
        foreach (['gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id'] as $key) {
            printf(
                '<label><span class="title">%s</span><input type="text" name="es_product[%s]" value=""></label>',
                esc_html($key),
                esc_attr($key)
            );
        }
        echo '</div></fieldset>';
    }

    public static function ajax_quick_save(): void
    {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'forbidden'], 403);
        }
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'erfansanat_product_meta')) {
            wp_send_json_error(['message' => 'bad nonce'], 403);
        }
        $post_id = absint($_POST['post_id'] ?? 0);
        $in = isset($_POST['es_product']) && is_array($_POST['es_product']) ? wp_unslash($_POST['es_product']) : [];
        foreach (self::TEXT_FIELDS as $key) {
            if (array_key_exists($key, $in)) {
                update_post_meta($post_id, $key, sanitize_text_field((string) $in[$key]));
            }
        }
        wp_send_json_success(['message' => 'ok']);
    }

    /* ---------------- RENDER ---------------- */

    public static function render(\WP_Post $post): void
    {
        wp_nonce_field('erfansanat_product_meta', self::NONCE);
        echo '<div class="es-product-tabs">';

        echo '<h3>' . esc_html__('شناسه‌های صنعتی و قانونی', 'erfansanat') . '</h3><table class="form-table">';
        foreach (self::TEXT_FIELDS as $key) {
            printf(
                '<tr><th><label>%s</label></th><td><input type="text" name="es_product[%s]" value="%s" class="regular-text"></td></tr>',
                esc_html($key),
                esc_attr($key),
                esc_attr((string) get_post_meta($post->ID, $key, true))
            );
        }
        echo '</table>';

        // Status + file + gallery
        $true_sale = get_post_meta($post->ID, 'true_sale', true);
        echo '<p><label><input type="checkbox" name="es_product[true_sale]" value="true" ' . checked($true_sale, 'true', false) . '> ' . esc_html__('قابل خرید آنلاین (true_sale)', 'erfansanat') . '</label></p>';
        printf('<p><label>%s</label><br><input type="text" name="es_product[product-catalog-file]" value="%s" class="regular-text" placeholder="https://..."></p>', esc_html__('فایل کاتالوگ', 'erfansanat'), esc_attr((string) get_post_meta($post->ID, 'product-catalog-file', true)));
        printf('<p><label>%s</label><br><input type="text" name="es_product[galerry_produ][]" value="%s" class="regular-text" placeholder="1234,5678"></p>', esc_html__('گالری (galerry_produ — CSV of image IDs)', 'erfansanat'), esc_attr(implode(',', self::read_gallery($post->ID))));

        // Relations
        $pids = get_post_meta($post->ID, 'project-ids', true);
        $pids = is_string($pids) ? (json_decode($pids, true) ?: []) : (array) $pids;
        printf('<p><label>%s</label><br><input type="text" name="es_product[project-ids][]" value="%s" class="regular-text"></p>', esc_html__('پروژه‌های مرتبط (project-ids)', 'erfansanat'), esc_attr(implode(',', array_map('strval', (array) $pids))));
        $uc = get_post_meta($post->ID, 'usage_countr', true);
        $uc = is_string($uc) ? (json_decode($uc, true) ?: []) : (array) $uc;
        printf('<p><label>%s</label><br><input type="text" name="es_product[usage_countr][]" value="%s" class="regular-text"></p>', esc_html__('کاربردها — term IDs of project_type (usage_countr)', 'erfansanat'), esc_attr(implode(',', array_map('strval', (array) $uc))));

        // Repeaters
        foreach (self::REPEATERS as $key => $subfields) {
            echo '<h3>' . esc_html($key) . '</h3><table class="form-table es-repeater" data-key="' . esc_attr($key) . '">';
            foreach (self::read_repeater($post->ID, $key) as $row) {
                echo '<tr>';
                foreach ($subfields as $sf) {
                    printf('<td><input type="text" name="es_product[%s][][%s]" value="%s" placeholder="%s"></td>', esc_attr($key), esc_attr($sf), esc_attr((string) ($row[$sf] ?? '')), esc_attr($sf));
                }
                echo '</tr>';
            }
            echo '<tr>';
            foreach ($subfields as $sf) {
                printf('<td><input type="text" name="es_product[%s][][%s]" value="" placeholder="%s"></td>', esc_attr($key), esc_attr($sf), esc_attr($sf));
            }
            echo '</tr></table>';
        }
        echo '</div>';
    }
}
