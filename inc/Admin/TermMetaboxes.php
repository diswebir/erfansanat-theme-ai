<?php
/**
 * Native taxonomy metadata for category FAQs, thumbnails, and hreflang data.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

defined('ABSPATH') || exit;

/**
 * Provides safe edit/add form fields for selected taxonomy terms.
 */
final class TermMetaboxes
{
    private const NONCE_NAME = 'es_term_meta_nonce';
    private const NONCE_ACTION_PREFIX = 'es_save_term_meta_';
    private const FAQ_KEY = 'faq';
    private const TAXONOMIES = ['product_cat', 'product_tag', 'product_brand', 'project_type', 'city', 'category', 'post_tag'];

    /** Register term metadata and taxonomy add/edit/save hooks. */
    public static function register(): void
    {
        add_action('init', [self::class, 'register_meta']);
        foreach (self::TAXONOMIES as $taxonomy) {
            add_action(
                $taxonomy . '_add_form_fields',
                static function () use ($taxonomy): void {
                    self::render_add_fields($taxonomy);
                }
            );
            add_action(
                $taxonomy . '_edit_form_fields',
                static function (\WP_Term $term) use ($taxonomy): void {
                    self::render_edit_fields($term, $taxonomy);
                }
            );
            add_action(
                'created_' . $taxonomy,
                static function (int $termId) use ($taxonomy): void {
                    self::save_term_meta($termId, $taxonomy);
                },
                10,
                1
            );
            add_action(
                'edited_' . $taxonomy,
                static function (int $termId) use ($taxonomy): void {
                    self::save_term_meta($termId, $taxonomy);
                },
                10,
                1
            );
        }
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
    }

    /** Register the exact taxonomy meta keys with private REST visibility and safe callbacks. */
    public static function register_meta(): void
    {
        foreach (self::TAXONOMIES as $taxonomy) {
            foreach (self::meta_schema($taxonomy) as $key => $field) {
                register_term_meta(
                    $taxonomy,
                    $key,
                    [
                        'type' => 'string',
                        'single' => true,
                        'show_in_rest' => false,
                        'sanitize_callback' => [self::class, 'sanitize_registered_value'],
                        'auth_callback' => [self::class, 'authorize_meta'],
                    ]
                );
            }
        }
    }

    /** Return the taxonomy's registered meta schema. */
    public static function meta_schema(string $taxonomy = 'product_cat'): array
    {
        $schema = [
            'hrefmeta_en' => ['label' => __('پیوند نسخهٔ انگلیسی', 'erfansanat-theme'), 'type' => 'url'],
            'hrefmeta_ar' => ['label' => __('پیوند نسخهٔ عربی', 'erfansanat-theme'), 'type' => 'url'],
            'hrefmeta_fa' => ['label' => __('پیوند نسخهٔ فارسی', 'erfansanat-theme'), 'type' => 'url'],
            'same_key_hrefl' => ['label' => __('کلید مشترک ترجمه', 'erfansanat-theme'), 'type' => 'text'],
        ];
        if ('product_cat' === $taxonomy) {
            $schema = array_merge(
                [
                    'thumbnail' => ['label' => __('تصویر دسته‌بندی (thumbnail)', 'erfansanat-theme'), 'type' => 'media_id'],
                    'faq' => [
                        'label' => __('پرسش‌های متداول دسته‌بندی', 'erfansanat-theme'),
                        'type' => 'repeater',
                        'subfields' => [
                            'name_question' => ['label' => __('عنوان سوال', 'erfansanat-theme'), 'type' => 'text'],
                            'answer_question' => ['label' => __('پاسخ', 'erfansanat-theme'), 'type' => 'textarea'],
                        ],
                    ],
                ],
                $schema
            );
        }

        return $schema;
    }

    /** Metadata authorization callback. */
    public static function authorize_meta($allowed, $metaKey, $termId): bool
    {
        return absint($termId) > 0 && current_user_can('edit_term', absint($termId));
    }

    /** Enqueue media and repeater scripts only on supported taxonomy screens. */
    public static function enqueue_assets(string $hook): void
    {
        if (!in_array($hook, ['edit-tags.php', 'term.php'], true)) {
            return;
        }
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || !in_array($screen->taxonomy, self::TAXONOMIES, true)) {
            return;
        }
        $css = get_theme_file_path('assets/css/admin-legacy-meta.css');
        $js = get_theme_file_path('assets/js/admin-legacy-meta.js');
        wp_enqueue_media();
        wp_enqueue_style('erfansanat-term-metabox', get_theme_file_uri('assets/css/admin-legacy-meta.css'), [], is_file($css) ? (string) filemtime($css) : '1.0.0');
        wp_enqueue_script('erfansanat-term-metabox', get_theme_file_uri('assets/js/admin-legacy-meta.js'), [], is_file($js) ? (string) filemtime($js) : '1.0.0', true);
    }

    /** Render add-term fields for a taxonomy. */
    public static function render_add_fields(string $taxonomy): void
    {
        self::render_fields($taxonomy, 0, [], false);
    }

    /** Render edit-term fields for a taxonomy. */
    public static function render_edit_fields(\WP_Term $term, string $taxonomy = ''): void
    {
        $taxonomy = '' !== $taxonomy ? $taxonomy : $term->taxonomy;
        $values = [];
        foreach (self::meta_schema($taxonomy) as $key => $field) {
            $values[$key] = get_term_meta($term->term_id, $key, true);
        }
        self::render_fields($taxonomy, (int) $term->term_id, $values, true);
    }

    /** Save nonce-protected, capability-checked term metadata. */
    public static function save_term_meta(int $termId, string $taxonomy): void
    {
        $nonce = isset($_POST[self::NONCE_NAME]) ? sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME])) : '';
        if ('' === $nonce || !wp_verify_nonce($nonce, self::NONCE_ACTION_PREFIX . $taxonomy)) {
            return;
        }
        $taxonomyObject = get_taxonomy($taxonomy);
        $capability = $taxonomyObject && isset($taxonomyObject->cap->edit_terms) ? $taxonomyObject->cap->edit_terms : 'manage_categories';
        if (!current_user_can($capability) || !isset($_POST['es_term_meta']) || !is_array($_POST['es_term_meta'])) {
            return;
        }

        $payload = wp_unslash($_POST['es_term_meta']);
        if (!empty($_POST['es_term_repeaters_present']) && is_array($_POST['es_term_repeaters_present'])) {
            foreach ($_POST['es_term_repeaters_present'] as $key => $present) {
                if ('1' === (string) $present && !array_key_exists($key, $payload)) {
                    $payload[$key] = [];
                }
            }
        }
        $clean = self::sanitize_term_payload($taxonomy, $payload);
        foreach ($clean as $key => $value) {
            if ('repeater' === (self::meta_schema($taxonomy)[$key]['type'] ?? '')) {
                $value = self::encode_legacy_value($value, get_term_meta($termId, $key, true));
            }
            update_term_meta($termId, $key, $value);
        }
    }

    /** Sanitize a term payload against the exact schema for its taxonomy. */
    public static function sanitize_term_payload(string $taxonomy, $payload): array
    {
        if (!is_array($payload)) {
            return [];
        }
        $clean = [];
        foreach (self::meta_schema($taxonomy) as $key => $field) {
            if (!array_key_exists($key, $payload)) {
                continue;
            }
            $value = $payload[$key];
            if ('media_id' === $field['type']) {
                $clean[$key] = absint($value);
            } elseif ('repeater' === $field['type']) {
                $clean[$key] = self::sanitize_repeater($field, $value);
            } elseif ('url' === $field['type']) {
                $clean[$key] = esc_url_raw(is_scalar($value) ? (string) $value : '');
            } else {
                $clean[$key] = sanitize_text_field(is_scalar($value) ? (string) $value : '');
            }
        }

        return $clean;
    }

    /** Registered metadata sanitizer; preserves native arrays and JSON/serialized repeaters. */
    public static function sanitize_registered_value($value, $metaKey = '')
    {
        if (!is_string($metaKey)) {
            return $value;
        }
        foreach (self::TAXONOMIES as $taxonomy) {
            $schema = self::meta_schema($taxonomy);
            if (!isset($schema[$metaKey])) {
                continue;
            }
            $clean = self::sanitize_term_payload($taxonomy, [$metaKey => $value])[$metaKey] ?? '';
            if ('repeater' === ($schema[$metaKey]['type'] ?? '')) {
                return self::encode_legacy_value($clean, $value);
            }
            return $clean;
        }

        return $value;
    }

    /** Decode legacy term repeaters without changing item keys. */
    public static function decode_legacy_value($value): array
    {
        if (class_exists(ProductMetaboxes::class)) {
            return ProductMetaboxes::decode_legacy_value($value);
        }
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value)) {
            return [];
        }
        $unserialized = maybe_unserialize($value);
        if (is_array($unserialized)) {
            return $unserialized;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    /** Encode with the source representation for FAQ repeaters. */
    public static function encode_legacy_value(array $value, $original)
    {
        if (class_exists(ProductMetaboxes::class)) {
            return ProductMetaboxes::encode_legacy_value($value, $original);
        }
        if (is_string($original) && is_array(json_decode($original, true))) {
            return wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        if (is_string($original) && is_array(maybe_unserialize($original))) {
            return maybe_serialize($value);
        }
        return $value;
    }

    /** Render escaped add/edit forms for a term. */
    private static function render_fields(string $taxonomy, int $termId, array $values, bool $editing): void
    {
        $schema = self::meta_schema($taxonomy);
        $nonceAction = self::NONCE_ACTION_PREFIX . $taxonomy;
        if ($editing) {
            ?>
            <tr class="form-field"><th scope="row"><label><?php echo esc_html__('تنظیمات عرفان صنعت', 'erfansanat-theme'); ?></label></th><td>
            <?php wp_nonce_field($nonceAction, self::NONCE_NAME); ?>
            </td></tr>
            <?php
        } else {
            wp_nonce_field($nonceAction, self::NONCE_NAME);
        }

        foreach ($schema as $key => $field) {
            $value = $values[$key] ?? '';
            if ('repeater' === $field['type']) {
                $value = self::decode_legacy_value($value);
            }
            if ($editing) {
                echo '<tr class="form-field es-term-meta-field es-term-meta-field-' . esc_attr($field['type']) . '"><th scope="row"><label for="' . esc_attr('es-term-meta-' . $key) . '">' . esc_html($field['label']) . '</label></th><td>';
            } else {
                echo '<div class="form-field es-term-meta-field es-term-meta-field-' . esc_attr($field['type']) . '"><label for="' . esc_attr('es-term-meta-' . $key) . '">' . esc_html($field['label']) . '</label>';
            }
            self::render_field($key, $field, $value);
            if ($editing) {
                echo '</td></tr>';
            } else {
                echo '</div>';
            }
        }
    }

    /** Render one term meta field. */
    private static function render_field(string $key, array $field, $value): void
    {
        $name = 'es_term_meta[' . $key . ']';
        $id = 'es-term-meta-' . sanitize_html_class(str_replace('_', '-', $key));
        if ('media_id' === $field['type']) {
            ?>
            <div class="es-term-media-control">
                <input id="<?php echo esc_attr($id); ?>" type="number" min="0" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr((string) absint($value)); ?>" />
                <button type="button" class="button es-legacy-select-media" data-media-mode="single" data-target="<?php echo esc_attr($id); ?>"><?php echo esc_html__('انتخاب تصویر', 'erfansanat-theme'); ?></button>
            </div>
            <?php
            return;
        }
        if ('repeater' === $field['type']) {
            self::render_repeater($key, $field, is_array($value) ? $value : [], $name, $id);
            return;
        }
        $type = 'url' === $field['type'] ? 'url' : 'text';
        ?>
        <input id="<?php echo esc_attr($id); ?>" type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr((string) $value); ?>" />
        <?php
    }

    /** Render a native editable FAQ repeater. */
    private static function render_repeater(string $key, array $field, array $rows, string $name, string $id): void
    {
        ?>
        <div class="es-term-repeater" data-legacy-repeater data-next-index="<?php echo esc_attr((string) self::next_repeater_index($rows)); ?>">
            <input type="hidden" name="es_term_repeaters_present[<?php echo esc_attr($key); ?>]" value="1" />
            <div data-legacy-repeater-rows>
                <?php foreach ($rows as $rowKey => $row) : ?>
                    <?php self::render_repeater_row($field, is_array($row) ? $row : [], $name, $id, (string) $rowKey); ?>
                <?php endforeach; ?>
            </div>
            <template data-legacy-repeater-template>
                <?php self::render_repeater_row($field, [], $name, $id, '__ROW__'); ?>
            </template>
            <button type="button" class="button es-legacy-repeater-add"><?php echo esc_html__('افزودن سوال', 'erfansanat-theme'); ?></button>
        </div>
        <?php
    }

    /** Render one FAQ row using exact legacy subfield names. */
    private static function render_repeater_row(array $field, array $row, string $name, string $id, string $rowKey): void
    {
        ?>
        <div class="es-term-repeater-row" data-legacy-repeater-row>
            <div class="es-term-repeater-heading"><strong><?php echo esc_html__('پرسش و پاسخ', 'erfansanat-theme'); ?></strong><button type="button" class="button-link-delete es-legacy-repeater-remove"><?php echo esc_html__('حذف', 'erfansanat-theme'); ?></button></div>
            <?php foreach (($field['subfields'] ?? []) as $subKey => $subfield) : ?>
                <?php $subName = $name . '[' . $rowKey . '][' . $subKey . ']'; $subId = $id . '-' . $rowKey . '-' . $subKey; ?>
                <div class="es-term-repeater-subfield">
                    <label for="<?php echo esc_attr($subId); ?>"><?php echo esc_html($subfield['label']); ?></label>
                    <?php if ('textarea' === $subfield['type']) : ?>
                        <textarea id="<?php echo esc_attr($subId); ?>" name="<?php echo esc_attr($subName); ?>" rows="3"><?php echo esc_textarea((string) ($row[$subKey] ?? '')); ?></textarea>
                    <?php else : ?>
                        <input id="<?php echo esc_attr($subId); ?>" type="text" name="<?php echo esc_attr($subName); ?>" value="<?php echo esc_attr((string) ($row[$subKey] ?? '')); ?>" />
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /** Sanitize repeater text while retaining item-N or numeric keys. */
    private static function sanitize_repeater(array $field, $value): array
    {
        $rows = self::decode_legacy_value($value);
        $clean = [];
        foreach (array_slice($rows, 0, 100, true) as $rowKey => $row) {
            if (!is_array($row)) {
                continue;
            }
            foreach ($field['subfields'] as $subKey => $subfield) {
                $raw = $row[$subKey] ?? '';
                $clean[$rowKey][$subKey] = 'textarea' === $subfield['type']
                    ? sanitize_textarea_field(is_scalar($raw) ? (string) $raw : '')
                    : sanitize_text_field(is_scalar($raw) ? (string) $raw : '');
            }
        }
        return $clean;
    }

    private static function next_repeater_index(array $rows): int
    {
        $max = -1;
        foreach (array_keys($rows) as $key) {
            if (preg_match('/(?:item-)?(\d+)$/', (string) $key, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }
        return $max + 1;
    }
}
