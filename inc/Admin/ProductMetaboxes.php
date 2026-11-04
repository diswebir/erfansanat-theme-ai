<?php
/**
 * Native WooCommerce product fields compatible with historical JetEngine data.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

defined('ABSPATH') || exit;

/**
 * Reads and writes the exact legacy product meta keys without normalizing away their shape.
 */
final class ProductMetaboxes
{
    private const NONCE_ACTION = 'es_save_product_meta';
    private const NONCE_NAME = 'es_product_meta_nonce';
    private const META_BOX_ID = 'es-product-details';
    private const PRESENT_NAME = 'es_product_repeaters_present';

    /** Attach registration, editor UI, and save hooks. */
    public static function register(): void
    {
        add_action('init', [self::class, 'register_meta']);
        add_action('add_meta_boxes_product', [self::class, 'add_meta_box']);
        add_action('save_post_product', [self::class, 'save_post'], 10, 3);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
    }

    /**
     * Register legacy product keys as private metadata with edit-capability and sanitize callbacks.
     */
    public static function register_meta(): void
    {
        foreach (self::meta_schema() as $key => $field) {
            register_post_meta(
                'product',
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

    /**
     * Authorize metadata edits with the native product edit capability.
     *
     * @param mixed $allowed Existing authorization result.
     * @param mixed $metaKey Metadata key.
     * @param mixed $postId Product ID.
     */
    public static function authorize_meta($allowed, $metaKey, $postId): bool
    {
        return absint($postId) > 0 && current_user_can('edit_post', absint($postId));
    }

    /** Add the native product data panel. */
    public static function add_meta_box(): void
    {
        add_meta_box(
            self::META_BOX_ID,
            __('اطلاعات تکمیلی محصول عرفان صنعت', 'erfansanat-theme'),
            [self::class, 'render_meta_box'],
            'product',
            'normal',
            'high'
        );
    }

    /**
     * Enqueue the media picker and repeater interactions only on product edit screens.
     *
     * @param string $hook Current admin screen hook.
     */
    public static function enqueue_assets(string $hook): void
    {
        if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || 'product' !== $screen->post_type) {
            return;
        }

        $cssPath = get_theme_file_path('assets/css/admin-product-metabox.css');
        $jsPath = get_theme_file_path('assets/js/admin-product-metabox.js');
        wp_enqueue_media();
        wp_enqueue_style(
            'erfansanat-product-metabox',
            get_theme_file_uri('assets/css/admin-product-metabox.css'),
            [],
            is_file($cssPath) ? (string) filemtime($cssPath) : '1.0.0'
        );
        wp_enqueue_script(
            'erfansanat-product-metabox',
            get_theme_file_uri('assets/js/admin-product-metabox.js'),
            [],
            is_file($jsPath) ? (string) filemtime($jsPath) : '1.0.0',
            true
        );
    }

    /**
     * Return the exact legacy product meta schema.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function meta_schema(): array
    {
        return [
            'gs1_id' => ['label' => __('شناسه GS1 / GTIN', 'erfansanat-theme'), 'type' => 'text', 'group' => 'identifiers'],
            'national_code' => ['label' => __('کد ملی کالا', 'erfansanat-theme'), 'type' => 'text', 'group' => 'identifiers'],
            'hs_id' => ['label' => __('شناسه HS', 'erfansanat-theme'), 'type' => 'text', 'group' => 'identifiers'],
            'isic_id' => ['label' => __('شناسه ISIC', 'erfansanat-theme'), 'type' => 'text', 'group' => 'identifiers'],
            'cpc_id' => ['label' => __('شناسه CPC', 'erfansanat-theme'), 'type' => 'text', 'group' => 'identifiers'],
            'product_id' => ['label' => __('شناسه محصول', 'erfansanat-theme'), 'type' => 'text', 'group' => 'identifiers'],
            'zireonvan' => ['label' => __('زیرعنوان محصول', 'erfansanat-theme'), 'type' => 'text', 'group' => 'content'],
            'english_titl' => ['label' => __('عنوان انگلیسی محصول', 'erfansanat-theme'), 'type' => 'text', 'group' => 'content'],
            'before-sale-c' => ['label' => __('اطلاعیه پیش از خرید', 'erfansanat-theme'), 'type' => 'textarea', 'group' => 'content'],
            'true_sale' => [
                'label' => __('وضعیت خرید آنلاین', 'erfansanat-theme'),
                'type' => 'select',
                'group' => 'content',
                'choices' => ['true' => __('قابل خرید آنلاین', 'erfansanat-theme'), 'false' => __('نیازمند استعلام / تماس', 'erfansanat-theme')],
                'default' => 'false',
            ],
            'product-catalog-file' => ['label' => __('فایل کاتالوگ محصول (URL)', 'erfansanat-theme'), 'type' => 'media', 'group' => 'media'],
            'galerry_produ' => ['label' => __('گالری محصول (کلید legacy galerry_produ)', 'erfansanat-theme'), 'type' => 'gallery', 'group' => 'media'],
            'project-ids' => ['label' => __('پروژه‌های مرتبط', 'erfansanat-theme'), 'type' => 'project_ids', 'group' => 'relations'],
            'usage_countr' => ['label' => __('انواع پروژه / کاربردها', 'erfansanat-theme'), 'type' => 'term_ids', 'group' => 'relations'],
            'product-feature' => [
                'label' => __('مشخصات محصول', 'erfansanat-theme'),
                'type' => 'repeater',
                'group' => 'repeaters',
                'subfields' => ['add-new-feature' => ['label' => __('ویژگی', 'erfansanat-theme'), 'type' => 'text']],
            ],
            'product-feature2_copy' => [
                'label' => __('ویژگی‌های کلیدی دو بخشی', 'erfansanat-theme'),
                'type' => 'repeater',
                'group' => 'repeaters',
                'subfields' => [
                    'add-new-feature' => ['label' => __('عنوان ویژگی', 'erfansanat-theme'), 'type' => 'text'],
                    'vaule_of_variable' => ['label' => __('مقدار ویژگی', 'erfansanat-theme'), 'type' => 'text'],
                ],
            ],
            'usage_product' => [
                'label' => __('کاربردها و موارد مصرف', 'erfansanat-theme'),
                'type' => 'repeater',
                'group' => 'repeaters',
                'subfields' => ['add-new-usage' => ['label' => __('کاربرد', 'erfansanat-theme'), 'type' => 'text']],
            ],
            'faq' => [
                'label' => __('سوالات متداول محصول', 'erfansanat-theme'),
                'type' => 'repeater',
                'group' => 'repeaters',
                'subfields' => [
                    'name_question' => ['label' => __('عنوان سوال', 'erfansanat-theme'), 'type' => 'text'],
                    'answer_question' => ['label' => __('پاسخ', 'erfansanat-theme'), 'type' => 'textarea'],
                ],
            ],
        ];
    }

    /**
     * Render all core, media, relation, and legacy repeater fields.
     *
     * @param \WP_Post $post Product post.
     */
    public static function render_meta_box(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
        $data = self::get_product_data($post->ID);
        $groups = [
            'identifiers' => __('شناسه‌های صنعتی و قانونی', 'erfansanat-theme'),
            'content' => __('عنوان و وضعیت فروش', 'erfansanat-theme'),
            'media' => __('فایل و گالری', 'erfansanat-theme'),
            'relations' => __('روابط محصول', 'erfansanat-theme'),
            'repeaters' => __('مشخصات، کاربردها و پرسش‌های متداول', 'erfansanat-theme'),
        ];
        ?>
        <div class="es-product-metabox" data-product-metabox>
            <p class="es-product-metabox-intro"><?php echo esc_html__('کلیدهای متا با املای legacy حفظ می‌شوند تا اطلاعات JetEngine و قالب قبلی بدون مهاجرت مخرب قابل استفاده باشد.', 'erfansanat-theme'); ?></p>
            <?php foreach ($groups as $group => $title) : ?>
                <section class="es-product-meta-group">
                    <h3><?php echo esc_html($title); ?></h3>
                    <div class="es-product-meta-fields">
                        <?php foreach (self::meta_schema() as $key => $field) : ?>
                            <?php if ($field['group'] === $group) : ?>
                                <?php self::render_field($key, $field, $data[$key] ?? ''); ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Save submitted product data after capability and nonce checks.
     *
     * @param int      $postId Product ID.
     * @param \WP_Post $post Product post.
     * @param bool     $update Whether this is an update.
     */
    public static function save_post(int $postId, \WP_Post $post, bool $update): void
    {
        if (
            wp_is_post_autosave($postId)
            || wp_is_post_revision($postId)
            || 'product' !== $post->post_type
            || !current_user_can('edit_post', $postId)
        ) {
            return;
        }
        $nonce = isset($_POST[self::NONCE_NAME]) ? sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME])) : '';
        if ('' === $nonce || !wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            return;
        }
        if (!isset($_POST['es_product_meta']) || !is_array($_POST['es_product_meta'])) {
            return;
        }

        $payload = wp_unslash($_POST['es_product_meta']);
        if (!empty($_POST[self::PRESENT_NAME]) && is_array($_POST[self::PRESENT_NAME])) {
            foreach ($_POST[self::PRESENT_NAME] as $key => $present) {
                if ('1' === (string) $present && !array_key_exists($key, $payload)) {
                    $payload[$key] = [];
                }
            }
        }
        $clean = self::sanitize_product_payload($payload);
        foreach ($clean as $key => $value) {
            $field = self::meta_schema()[$key];
            $original = get_post_meta($postId, $key, true);
            if (in_array($field['type'], ['repeater', 'project_ids', 'term_ids'], true)) {
                $value = self::encode_legacy_value($value, $original);
            }
            update_post_meta($postId, $key, $value);
        }
    }

    /**
     * Sanitize submitted fields strictly against the legacy schema.
     *
     * @param mixed $payload Product form values.
     * @return array<string, mixed>
     */
    public static function sanitize_product_payload($payload): array
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
            switch ($field['type']) {
                case 'textarea':
                    $clean[$key] = sanitize_textarea_field(is_scalar($value) ? (string) $value : '');
                    break;
                case 'media':
                    $clean[$key] = esc_url_raw(is_scalar($value) ? (string) $value : '');
                    break;
                case 'select':
                    $value = is_scalar($value) ? (string) $value : '';
                    $clean[$key] = array_key_exists($value, $field['choices']) ? $value : (string) $field['default'];
                    break;
                case 'gallery':
                    $clean[$key] = implode(',', self::sanitize_id_list($value));
                    break;
                case 'project_ids':
                case 'term_ids':
                    $clean[$key] = self::sanitize_id_list($value);
                    break;
                case 'repeater':
                    $clean[$key] = self::sanitize_repeater($key, $value);
                    break;
                default:
                    $clean[$key] = sanitize_text_field(is_scalar($value) ? (string) $value : '');
                    break;
            }
        }

        return $clean;
    }

    /**
     * Sanitize values entering the registered metadata API while retaining their storage format.
     *
     * @param mixed $value Incoming value.
     * @param mixed $metaKey Exact meta key.
     * @return mixed
     */
    public static function sanitize_registered_value($value, $metaKey = '')
    {
        if (!is_string($metaKey) || !isset(self::meta_schema()[$metaKey])) {
            return $value;
        }
        $field = self::meta_schema()[$metaKey];
        if (in_array($field['type'], ['repeater', 'project_ids', 'term_ids'], true)) {
            $clean = self::sanitize_product_payload([$metaKey => $value])[$metaKey] ?? [];
            return self::encode_legacy_value($clean, $value);
        }

        return self::sanitize_product_payload([$metaKey => $value])[$metaKey] ?? '';
    }

    /**
     * Decode native arrays, PHP serialization, or JSON to an array without changing keys.
     *
     * @param mixed $value Stored value.
     * @return array<string|int, mixed>
     */
    public static function decode_legacy_value($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value) || '' === $value) {
            return [];
        }
        $unserialized = maybe_unserialize($value);
        if (is_array($unserialized)) {
            return $unserialized;
        }
        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Encode a sanitized value using the existing legacy array/JSON/serialized representation.
     *
     * @param mixed $value New sanitized value.
     * @param mixed $original Existing stored value used to infer the representation.
     * @return mixed
     */
    public static function encode_legacy_value($value, $original)
    {
        $originalDecoded = self::decode_legacy_value($original);
        $format = 'array';
        if (is_string($original)) {
            $json = json_decode($original, true);
            if (is_array($json)) {
                $format = 'json';
            } elseif (is_array(maybe_unserialize($original))) {
                $format = 'serialized';
            }
        }
        $value = is_array($value) ? $value : [];
        $value = self::preserve_repeater_keys($value, $originalDecoded);

        if ('json' === $format) {
            return wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        if ('serialized' === $format) {
            return maybe_serialize($value);
        }

        return $value;
    }

    /**
     * Return all product meta values, decoding legacy repeaters for native template use.
     *
     * @param int $postId Product ID.
     * @return array<string, mixed>
     */
    public static function get_product_data(int $postId): array
    {
        $data = [];
        foreach (self::meta_schema() as $key => $field) {
            $value = get_post_meta($postId, $key, true);
            if ('repeater' === $field['type']) {
                $value = self::decode_legacy_value($value);
            } elseif (in_array($field['type'], ['project_ids', 'term_ids'], true)) {
                $value = self::sanitize_id_list($value);
            }
            $data[$key] = '' === $value || null === $value ? ($field['default'] ?? $value) : $value;
        }

        $data['galerry_produ_ids'] = self::get_product_gallery_ids($postId);

        return $data;
    }

    /**
     * Read the product gallery using the exact legacy key first, then WooCommerce and old field fallbacks.
     *
     * @param int $postId Product ID.
     * @return array<int, int>
     */
    public static function get_product_gallery_ids(int $postId): array
    {
        foreach (['galerry_produ', '_product_image_gallery', 'gallery-filed'] as $key) {
            $ids = self::sanitize_id_list(get_post_meta($postId, $key, true));
            if ($ids) {
                return $ids;
            }
        }

        $legacyIds = [];
        foreach (['image-2', 'image-3', 'image-4', 'image-5', 'image-6'] as $key) {
            $id = absint(get_post_meta($postId, $key, true));
            if ($id) {
                $legacyIds[] = $id;
            }
        }

        return array_values(array_unique($legacyIds));
    }

    /**
     * Sanitize and preserve the keyed structure of a repeater, including JetEngine item-N keys.
     *
     * @param string $metaKey Legacy repeater key.
     * @param mixed  $value Submitted repeater rows.
     * @return array<string|int, array<string, string>>
     */
    private static function sanitize_repeater(string $metaKey, $value): array
    {
        $rows = self::decode_legacy_value($value);
        $subfields = self::meta_schema()[$metaKey]['subfields'] ?? [];
        $clean = [];
        foreach (array_slice($rows, 0, 100, true) as $rowKey => $row) {
            if (!is_array($row)) {
                continue;
            }
            foreach ($subfields as $key => $definition) {
                $fieldValue = $row[$key] ?? '';
                $fieldType = $definition['type'] ?? 'text';
                $clean[$rowKey][$key] = 'textarea' === $fieldType
                    ? sanitize_textarea_field(is_scalar($fieldValue) ? (string) $fieldValue : '')
                    : sanitize_text_field(is_scalar($fieldValue) ? (string) $fieldValue : '');
            }
        }

        return $clean;
    }

    /** Preserve legacy `item-N` keys when a new UI row is submitted with numeric indices. */
    private static function preserve_repeater_keys(array $rows, array $original): array
    {
        $hasLegacyKeys = false;
        foreach (array_keys($original) as $key) {
            if (is_string($key) && preg_match('/^item-\d+$/', $key)) {
                $hasLegacyKeys = true;
                break;
            }
        }
        if (!$hasLegacyKeys) {
            return $rows;
        }

        $preserved = [];
        foreach (array_values($rows) as $index => $row) {
            $preserved['item-' . $index] = $row;
        }

        return $preserved;
    }

    /**
     * Sanitize an arbitrary list of IDs or comma-separated ID values.
     *
     * @param mixed $value Values from native meta or an editor request.
     * @return array<int, int>
     */
    private static function sanitize_id_list($value): array
    {
        if (is_string($value)) {
            $unserialized = maybe_unserialize($value);
            if (is_array($unserialized)) {
                $value = $unserialized;
            } else {
                $decoded = json_decode($value, true);
                $value = is_array($decoded) ? $decoded : preg_split('/[\s,]+/', trim($value), -1, PREG_SPLIT_NO_EMPTY);
            }
        }
        if (!is_array($value)) {
            $value = [];
        }
        $ids = [];
        foreach ($value as $item) {
            if (is_array($item)) {
                $item = $item['id'] ?? $item['ID'] ?? '';
            }
            $id = absint($item);
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    /** Render a single field according to its schema. */
    private static function render_field(string $key, array $field, $value): void
    {
        $type = $field['type'];
        $name = 'es_product_meta[' . $key . ']';
        $id = 'es-product-meta-' . sanitize_html_class(str_replace('-', '_', $key));
        ?>
        <div class="es-product-meta-field es-product-meta-field-<?php echo esc_attr($type); ?>">
            <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($field['label']); ?></label>
            <?php if ('textarea' === $type) : ?>
                <textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" rows="4"><?php echo esc_textarea((string) $value); ?></textarea>
            <?php elseif ('select' === $type) : ?>
                <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>">
                    <?php foreach ($field['choices'] as $choice => $label) : ?>
                        <option value="<?php echo esc_attr((string) $choice); ?>" <?php selected((string) $value, (string) $choice); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ('media' === $type) : ?>
                <div class="es-product-media-control">
                    <input id="<?php echo esc_attr($id); ?>" type="url" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr((string) $value); ?>" />
                    <button type="button" class="button es-product-select-media" data-media-mode="single" data-target="<?php echo esc_attr($id); ?>"><?php echo esc_html__('انتخاب فایل', 'erfansanat-theme'); ?></button>
                </div>
            <?php elseif ('gallery' === $type) : ?>
                <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr(implode(',', self::get_product_gallery_ids_from_value($value))); ?>" data-product-gallery-value />
                <div class="es-product-gallery-preview" data-product-gallery-preview></div>
                <button type="button" class="button es-product-select-media" data-media-mode="gallery" data-target="<?php echo esc_attr($id); ?>"><?php echo esc_html__('مدیریت گالری تصاویر', 'erfansanat-theme'); ?></button>
            <?php elseif ('project_ids' === $type) : ?>
                <?php self::render_project_select($name, (array) $value); ?>
            <?php elseif ('term_ids' === $type) : ?>
                <?php self::render_project_type_select($name, (array) $value); ?>
            <?php elseif ('repeater' === $type) : ?>
                <?php self::render_repeater($key, $field, self::decode_legacy_value($value), $name, $id); ?>
            <?php else : ?>
                <input id="<?php echo esc_attr($id); ?>" type="text" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr((string) $value); ?>" />
            <?php endif; ?>
        </div>
        <?php
    }

    /** Convert any product gallery representation to attachment IDs. */
    private static function get_product_gallery_ids_from_value($value): array
    {
        return self::sanitize_id_list($value);
    }

    /** Render product relationship choices without requiring a page builder plugin. */
    private static function render_project_select(string $name, array $selected): void
    {
        $projects = get_posts(
            [
                'post_type' => 'projects',
                'post_status' => ['publish', 'draft', 'private'],
                'numberposts' => 300,
                'orderby' => 'title',
                'order' => 'ASC',
                'suppress_filters' => true,
            ]
        );
        ?>
        <input type="hidden" name="<?php echo esc_attr($name); ?>" value="__none__" />
        <select multiple size="6" name="<?php echo esc_attr($name . '[]'); ?>">
            <?php foreach ($projects as $project) : ?>
                <option value="<?php echo esc_attr((string) $project->ID); ?>" <?php selected(in_array((int) $project->ID, array_map('absint', $selected), true)); ?>><?php echo esc_html($project->post_title); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /** Render `project_type` term choices used by the legacy usage_countr relation. */
    private static function render_project_type_select(string $name, array $selected): void
    {
        $terms = taxonomy_exists('project_type') ? get_terms(['taxonomy' => 'project_type', 'hide_empty' => false]) : [];
        ?>
        <input type="hidden" name="<?php echo esc_attr($name); ?>" value="__none__" />
        <select multiple size="6" name="<?php echo esc_attr($name . '[]'); ?>">
            <?php if (!is_wp_error($terms)) : ?>
                <?php foreach ($terms as $term) : ?>
                    <option value="<?php echo esc_attr((string) $term->term_id); ?>" <?php selected(in_array((int) $term->term_id, array_map('absint', $selected), true)); ?>><?php echo esc_html($term->name); ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <?php
    }

    /** Render repeater rows with all legacy subfield names preserved. */
    private static function render_repeater(string $key, array $field, array $rows, string $name, string $id): void
    {
        ?>
        <div class="es-product-repeater" data-product-repeater data-next-index="<?php echo esc_attr((string) self::next_repeater_index($rows)); ?>">
            <input type="hidden" name="<?php echo esc_attr(self::PRESENT_NAME . '[' . $key . ']'); ?>" value="1" />
            <div data-product-repeater-rows>
                <?php foreach ($rows as $rowIndex => $row) : ?>
                    <?php self::render_repeater_row($field, is_array($row) ? $row : [], $name, $id, (string) $rowIndex); ?>
                <?php endforeach; ?>
            </div>
            <template data-product-repeater-template>
                <?php self::render_repeater_row($field, [], $name, $id, '__ROW__'); ?>
            </template>
            <button type="button" class="button es-product-repeater-add"><?php echo esc_html__('افزودن ردیف', 'erfansanat-theme'); ?></button>
        </div>
        <?php
    }

    /** Return the next unused numeric suffix for existing item-N or numeric row keys. */
    private static function next_repeater_index(array $rows): int
    {
        $max = -1;
        foreach (array_keys($rows) as $key) {
            if (preg_match('/(?:item-)?(\\d+)$/', (string) $key, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return $max + 1;
    }

    /** Render one legacy repeater row. */
    private static function render_repeater_row(array $field, array $row, string $name, string $id, string $rowKey): void
    {
        ?>
        <div class="es-product-repeater-row" data-product-repeater-row>
            <div class="es-product-repeater-heading"><strong><?php echo esc_html__('ردیف', 'erfansanat-theme'); ?></strong><button type="button" class="button-link-delete es-product-repeater-remove"><?php echo esc_html__('حذف', 'erfansanat-theme'); ?></button></div>
            <div class="es-product-repeater-fields">
                <?php foreach (($field['subfields'] ?? []) as $subKey => $subfield) : ?>
                    <div class="es-product-meta-field">
                        <label for="<?php echo esc_attr($id . '-' . $rowKey . '-' . $subKey); ?>"><?php echo esc_html($subfield['label']); ?></label>
                        <?php if ('textarea' === $subfield['type']) : ?>
                            <textarea id="<?php echo esc_attr($id . '-' . $rowKey . '-' . $subKey); ?>" name="<?php echo esc_attr($name . '[' . $rowKey . '][' . $subKey . ']'); ?>" rows="3"><?php echo esc_textarea((string) ($row[$subKey] ?? '')); ?></textarea>
                        <?php else : ?>
                            <input id="<?php echo esc_attr($id . '-' . $rowKey . '-' . $subKey); ?>" type="text" name="<?php echo esc_attr($name . '[' . $rowKey . '][' . $subKey . ']'); ?>" value="<?php echo esc_attr((string) ($row[$subKey] ?? '')); ?>" />
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
