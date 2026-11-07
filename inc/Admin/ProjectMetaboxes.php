<?php
/**
 * Native project metadata compatible with the legacy project records.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

defined('ABSPATH') || exit;

/**
 * Manages project galleries, completion dates, and related product IDs.
 */
final class ProjectMetaboxes
{
    private const NONCE_ACTION = 'es_save_project_meta';
    private const NONCE_NAME = 'es_project_meta_nonce';
    private const META_BOX_ID = 'es-project-details';
    private const GALLERY_KEYS = ['second-image', 'three-image', 'foure-image', 'five-image', 'six-image'];

    /** Attach metadata, editor UI, and secure save hooks. */
    public static function register(): void
    {
        add_action('init', [self::class, 'register_meta']);
        add_action('add_meta_boxes_projects', [self::class, 'add_meta_box']);
        add_action('save_post_projects', [self::class, 'save_post'], 10, 3);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
    }

    /** Register the exact legacy project keys as private post metadata. */
    public static function register_meta(): void
    {
        foreach (self::meta_schema() as $key => $field) {
            register_post_meta(
                'projects',
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

    /** Authorize writes using the normal edit_post capability. */
    public static function authorize_meta($allowed, $metaKey, $postId): bool
    {
        return absint($postId) > 0 && current_user_can('edit_post', absint($postId));
    }

    /** Add the project data metabox. */
    public static function add_meta_box(): void
    {
        add_meta_box(
            self::META_BOX_ID,
            __('اطلاعات تکمیلی پروژه', 'erfansanat-theme'),
            [self::class, 'render_meta_box'],
            'projects',
            'normal',
            'high'
        );
    }

    /** Enqueue assets only on project editor screens. */
    public static function enqueue_assets(string $hook): void
    {
        if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || 'projects' !== $screen->post_type) {
            return;
        }

        $css = get_theme_file_path('assets/css/admin-legacy-meta.css');
        $js = get_theme_file_path('assets/js/admin-legacy-meta.js');
        wp_enqueue_media();
        wp_enqueue_style('erfansanat-project-metabox', get_theme_file_uri('assets/css/admin-legacy-meta.css'), [], is_file($css) ? (string) filemtime($css) : '1.0.0');
        wp_enqueue_script('erfansanat-project-metabox', get_theme_file_uri('assets/js/admin-legacy-meta.js'), [], is_file($js) ? (string) filemtime($js) : '1.0.0', true);
    }

    /**
     * Exact legacy project meta schema plus a modern gallery synchronized with the old image keys.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function meta_schema(): array
    {
        return [
            'second-image' => ['label' => __('تصویر دوم', 'erfansanat-theme'), 'type' => 'media_id', 'order' => 0],
            'three-image' => ['label' => __('تصویر سوم', 'erfansanat-theme'), 'type' => 'media_id', 'order' => 1],
            'foure-image' => ['label' => __('تصویر چهارم (کلید legacy foure-image)', 'erfansanat-theme'), 'type' => 'media_id', 'order' => 2],
            'five-image' => ['label' => __('تصویر پنجم', 'erfansanat-theme'), 'type' => 'media_id', 'order' => 3],
            'six-image' => ['label' => __('تصویر ششم', 'erfansanat-theme'), 'type' => 'media_id', 'order' => 4],
            '_es_project_gallery' => ['label' => __('گالری جدید پروژه', 'erfansanat-theme'), 'type' => 'gallery', 'order' => 5],
            'finish-pro-time' => ['label' => __('تاریخ تکمیل پروژه', 'erfansanat-theme'), 'type' => 'text', 'order' => 6],
            'mortabet_mahsole_2' => ['label' => __('محصولات استفاده‌شده در پروژه', 'erfansanat-theme'), 'type' => 'product_ids', 'order' => 7],
        ];
    }

    /** Render all legacy gallery inputs and related product selectors. */
    public static function render_meta_box(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
        $data = self::get_project_data($post->ID);
        ?>
        <div class="es-legacy-meta" data-legacy-meta>
            <p class="es-legacy-meta-intro"><?php echo esc_html__('گالری جدید با کلیدهای قدیمی پروژه همگام می‌شود؛ املای تاریخی foure-image و mortabet_mahsole_2 حفظ شده است.', 'erfansanat-theme'); ?></p>
            <section class="es-legacy-meta-group">
                <h3><?php echo esc_html__('گالری تصاویر پروژه', 'erfansanat-theme'); ?></h3>
                <div class="es-legacy-gallery-preview" data-legacy-gallery-preview><?php foreach ($data['_es_project_gallery'] as $imageId) : ?><span class="es-legacy-gallery-chip"><?php echo esc_html('#' . (string) $imageId); ?></span><?php endforeach; ?></div>
                <input type="hidden" name="es_project_meta[_es_project_gallery]" value="<?php echo esc_attr(implode(',', $data['_es_project_gallery'])); ?>" data-legacy-gallery-value />
                <button type="button" class="button es-legacy-select-media" data-media-mode="gallery"><?php echo esc_html__('انتخاب تصاویر گالری', 'erfansanat-theme'); ?></button>
                <div class="es-legacy-image-fields">
                    <?php foreach (self::GALLERY_KEYS as $key) : ?>
                        <?php self::render_media_id_field($key, self::meta_schema()[$key], $data[$key] ?? 0); ?>
                    <?php endforeach; ?>
                </div>
            </section>
            <section class="es-legacy-meta-group">
                <h3><?php echo esc_html__('تاریخ تکمیل و محصولات مرتبط', 'erfansanat-theme'); ?></h3>
                <div class="es-legacy-meta-fields">
                    <?php self::render_text_field('finish-pro-time', self::meta_schema()['finish-pro-time'], $data['finish-pro-time'] ?? ''); ?>
                    <?php self::render_product_select('mortabet_mahsole_2', self::meta_schema()['mortabet_mahsole_2'], $data['mortabet_mahsole_2'] ?? []); ?>
                </div>
            </section>
        </div>
        <?php
    }

    /** Save sanitized project metadata and synchronize modern gallery IDs to the legacy image keys. */
    public static function save_post(int $postId, \WP_Post $post, bool $update): void
    {
        if (
            wp_is_post_autosave($postId)
            || wp_is_post_revision($postId)
            || 'projects' !== $post->post_type
            || !current_user_can('edit_post', $postId)
        ) {
            return;
        }
        $rawNonce = isset($_POST[self::NONCE_NAME]) ? wp_unslash($_POST[self::NONCE_NAME]) : '';
        $nonce = is_string($rawNonce) ? sanitize_text_field($rawNonce) : '';
        if ('' === $nonce || !wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            return;
        }
        if (!isset($_POST['es_project_meta']) || !is_array($_POST['es_project_meta'])) {
            return;
        }

        $clean = self::sanitize_project_payload(wp_unslash($_POST['es_project_meta']));
        foreach ($clean as $key => $value) {
            if ('mortabet_mahsole_2' === $key) {
                $value = self::encode_legacy_value($value, get_post_meta($postId, $key, true));
            }
            update_post_meta($postId, $key, $value);
        }
        if (array_key_exists('_es_project_gallery', $clean)) {
            foreach (self::GALLERY_KEYS as $index => $key) {
                update_post_meta($postId, $key, absint($clean['_es_project_gallery'][$index] ?? 0));
            }
        }
    }

    /** Sanitize project payload against only the known legacy keys. */
    public static function sanitize_project_payload($payload): array
    {
        if (!is_array($payload)) {
            return [];
        }
        $schema = self::meta_schema();
        $clean = [];
        foreach ($schema as $key => $field) {
            if (!array_key_exists($key, $payload)) {
                continue;
            }
            $value = $payload[$key];
            if ('media_id' === $field['type']) {
                $clean[$key] = absint($value);
            } elseif ('gallery' === $field['type']) {
                $clean[$key] = self::sanitize_id_list($value);
            } elseif ('product_ids' === $field['type']) {
                $clean[$key] = self::sanitize_id_list($value);
            } else {
                $clean[$key] = sanitize_text_field(is_scalar($value) ? (string) $value : '');
            }
        }

        return $clean;
    }

    /** Sanitize values written through the registered meta API. */
    public static function sanitize_registered_value($value, $metaKey = '')
    {
        if (!is_string($metaKey) || !isset(self::meta_schema()[$metaKey])) {
            return $value;
        }
        return self::sanitize_project_payload([$metaKey => $value])[$metaKey] ?? '';
    }

    /** Read project metadata while honoring all old gallery keys. */
    public static function get_project_data(int $postId): array
    {
        $data = [];
        foreach (self::meta_schema() as $key => $field) {
            $data[$key] = get_post_meta($postId, $key, true);
        }
        $data['_es_project_gallery'] = self::get_project_gallery_ids($postId);
        $data['mortabet_mahsole_2'] = self::decode_legacy_value($data['mortabet_mahsole_2'] ?? '');

        return $data;
    }

    /** Read modern gallery IDs first, then exact legacy image keys. */
    public static function get_project_gallery_ids(int $postId): array
    {
        $modern = self::sanitize_id_list(get_post_meta($postId, '_es_project_gallery', true));
        if ($modern) {
            return $modern;
        }
        $legacy = [];
        foreach (self::GALLERY_KEYS as $key) {
            $imageId = absint(get_post_meta($postId, $key, true));
            if ($imageId) {
                $legacy[] = $imageId;
            }
        }

        return array_values(array_unique($legacy));
    }

    /** Decode JSON, serialized, or native array values without changing item keys. */
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

    /** Encode related IDs using the original array/JSON/serialized representation. */
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

    private static function sanitize_id_list($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            } else {
                $unserialized = maybe_unserialize($value);
                $value = is_array($unserialized) ? $unserialized : preg_split('/[\s,]+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);
            }
        }
        if (!is_array($value)) {
            $value = [];
        }
        $ids = [];
        foreach ($value as $item) {
            $id = absint($item);
            if ($id) {
                $ids[] = $id;
            }
        }
        return array_values(array_unique($ids));
    }

    private static function render_media_id_field(string $key, array $field, $value): void
    {
        $id = 'es-project-' . sanitize_html_class(str_replace('-', '_', $key));
        ?>
        <div class="es-legacy-meta-field">
            <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($field['label']); ?></label>
            <div class="es-legacy-media-control">
                <input id="<?php echo esc_attr($id); ?>" type="number" min="0" name="<?php echo esc_attr('es_project_meta[' . $key . ']'); ?>" value="<?php echo esc_attr((string) absint($value)); ?>" />
                <button type="button" class="button es-legacy-select-media" data-media-mode="single" data-target="<?php echo esc_attr($id); ?>"><?php echo esc_html__('انتخاب تصویر', 'erfansanat-theme'); ?></button>
            </div>
        </div>
        <?php
    }

    private static function render_text_field(string $key, array $field, $value): void
    {
        $id = 'es-project-' . sanitize_html_class(str_replace('-', '_', $key));
        ?>
        <div class="es-legacy-meta-field">
            <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($field['label']); ?></label>
            <input id="<?php echo esc_attr($id); ?>" type="text" name="<?php echo esc_attr('es_project_meta[' . $key . ']'); ?>" value="<?php echo esc_attr((string) $value); ?>" />
        </div>
        <?php
    }

    private static function render_product_select(string $key, array $field, array $selected): void
    {
        $products = get_posts(['post_type' => 'product', 'post_status' => ['publish', 'draft', 'private'], 'numberposts' => 300, 'orderby' => 'title', 'order' => 'ASC']);
        ?>
        <div class="es-legacy-meta-field">
            <label><?php echo esc_html($field['label']); ?></label>
            <input type="hidden" name="<?php echo esc_attr('es_project_meta[' . $key . ']'); ?>" value="__none__" />
            <select multiple size="6" name="<?php echo esc_attr('es_project_meta[' . $key . '][]'); ?>">
                <?php foreach ($products as $product) : ?>
                    <option value="<?php echo esc_attr((string) $product->ID); ?>" <?php selected(in_array((int) $product->ID, array_map('absint', $selected), true)); ?>><?php echo esc_html($product->post_title); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
    }
}
