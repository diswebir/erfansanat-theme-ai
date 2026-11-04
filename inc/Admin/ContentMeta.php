<?php
/**
 * Shared post hreflang and legacy view-count metadata.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

defined('ABSPATH') || exit;

/**
 * Adds a small native metadata panel to existing content types.
 */
final class ContentMeta
{
    private const NONCE_ACTION = 'es_save_content_meta';
    private const NONCE_NAME = 'es_content_meta_nonce';
    private const META_BOX_ID = 'es-content-compatibility-meta';
    private const POST_TYPES = ['post', 'page', 'product', 'projects'];

    /** Register legacy post meta and its editor UI on the supported content types. */
    public static function register(): void
    {
        add_action('init', [self::class, 'register_meta']);
        foreach (self::POST_TYPES as $postType) {
            add_action('add_meta_boxes_' . $postType, [self::class, 'add_meta_box']);
            add_action('save_post_' . $postType, [self::class, 'save_post'], 10, 3);
        }
    }

    /** Return exact shared content meta fields. */
    public static function meta_schema(): array
    {
        return [
            'hrefmeta_en' => ['label' => __('پیوند نسخهٔ انگلیسی', 'erfansanat-theme'), 'type' => 'url'],
            'hrefmeta_ar' => ['label' => __('پیوند نسخهٔ عربی', 'erfansanat-theme'), 'type' => 'url'],
            'hrefmeta_fa' => ['label' => __('پیوند نسخهٔ فارسی', 'erfansanat-theme'), 'type' => 'url'],
            'same_key_hrefl' => ['label' => __('کلید مشترک ترجمه', 'erfansanat-theme'), 'type' => 'text'],
            'number2-viewc' => ['label' => __('تعداد بازدید', 'erfansanat-theme'), 'type' => 'number'],
        ];
    }

    /** Register hidden-from-REST metadata with native post edit authorization. */
    public static function register_meta(): void
    {
        foreach (self::POST_TYPES as $postType) {
            foreach (self::meta_schema() as $key => $field) {
                register_post_meta(
                    $postType,
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

    /** Authorize registered writes with edit_post. */
    public static function authorize_meta($allowed, $metaKey, $postId): bool
    {
        return absint($postId) > 0 && current_user_can('edit_post', absint($postId));
    }

    /** Add the compatibility panel for a supported post type. */
    public static function add_meta_box(\WP_Post $post): void
    {
        if (!in_array($post->post_type, self::POST_TYPES, true)) {
            return;
        }
        add_meta_box(
            self::META_BOX_ID,
            __('ترجمه‌ها و بازدید', 'erfansanat-theme'),
            [self::class, 'render_meta_box'],
            $post->post_type,
            'side',
            'default'
        );
    }

    /** Render escaped hreflang URLs, group key, and view count. */
    public static function render_meta_box(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
        ?>
        <div class="es-content-meta">
            <?php foreach (self::meta_schema() as $key => $field) : ?>
                <?php $value = 'number2-viewc' === $key ? self::get_view_count($post->ID) : get_post_meta($post->ID, $key, true); ?>
                <?php $id = 'es-content-meta-' . sanitize_html_class(str_replace('_', '-', $key)); ?>
                <p class="es-content-meta-field">
                    <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($field['label']); ?></label>
                    <input id="<?php echo esc_attr($id); ?>" type="<?php echo esc_attr('number' === $field['type'] ? 'number' : ('url' === $field['type'] ? 'url' : 'text')); ?>" name="<?php echo esc_attr('es_content_meta[' . $key . ']'); ?>" value="<?php echo esc_attr((string) $value); ?>" <?php echo 'number' === $field['type'] ? 'min="0" step="1"' : ''; ?> />
                </p>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /** Save the shared metadata only for an authorized, nonce-verified edit. */
    public static function save_post(int $postId, \WP_Post $post, bool $update): void
    {
        if (
            wp_is_post_autosave($postId)
            || wp_is_post_revision($postId)
            || !in_array($post->post_type, self::POST_TYPES, true)
            || !current_user_can('edit_post', $postId)
        ) {
            return;
        }
        $nonce = isset($_POST[self::NONCE_NAME]) ? sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME])) : '';
        if ('' === $nonce || !wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            return;
        }
        if (!isset($_POST['es_content_meta']) || !is_array($_POST['es_content_meta'])) {
            return;
        }

        foreach (self::sanitize_post_payload(wp_unslash($_POST['es_content_meta'])) as $key => $value) {
            update_post_meta($postId, $key, $value);
        }
    }

    /** Sanitize whitelisted hreflang and view values. */
    public static function sanitize_post_payload($payload): array
    {
        if (!is_array($payload)) {
            return [];
        }
        $clean = [];
        foreach (self::meta_schema() as $key => $field) {
            if (!array_key_exists($key, $payload)) {
                continue;
            }
            $value = $payload[$key];
            if ('number' === $field['type']) {
                $clean[$key] = absint($value);
            } elseif ('url' === $field['type']) {
                $clean[$key] = esc_url_raw(is_scalar($value) ? (string) $value : '');
            } else {
                $clean[$key] = sanitize_text_field(is_scalar($value) ? (string) $value : '');
            }
        }

        return $clean;
    }

    /** Sanitize writes through the metadata API. */
    public static function sanitize_registered_value($value, $metaKey = '')
    {
        if (!is_string($metaKey) || !isset(self::meta_schema()[$metaKey])) {
            return $value;
        }
        return self::sanitize_post_payload([$metaKey => $value])[$metaKey] ?? '';
    }

    /**
     * Read the modern view count, falling back to the historic `number-viewc` key.
     *
     * @param int $postId Content ID.
     */
    public static function get_view_count(int $postId): int
    {
        if (metadata_exists('post', $postId, 'number2-viewc')) {
            return absint(get_post_meta($postId, 'number2-viewc', true));
        }

        return absint(get_post_meta($postId, 'number-viewc', true));
    }
}
