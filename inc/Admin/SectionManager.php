<?php
/**
 * Native editable sections for selected page templates.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

defined('ABSPATH') || exit;

/**
 * Stores page-section configuration as sanitized structured post metadata.
 */
final class SectionManager
{
    public const META_KEY = '_es_page_sections';
    private const NONCE_ACTION = 'es_save_page_sections';
    private const NONCE_NAME = 'es_page_sections_nonce';
    private const META_BOX_ID = 'es-page-section-builder';

    /** Attach metadata, editor UI, and save hooks. */
    public static function register(): void
    {
        add_action('init', [self::class, 'register_meta']);
        add_action('add_meta_boxes_page', [self::class, 'add_meta_box']);
        add_action('save_post_page', [self::class, 'save_post'], 10, 3);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
    }

    /** Register a private, capability-checked page meta field. */
    public static function register_meta(): void
    {
        register_post_meta(
            'page',
            self::META_KEY,
            [
                'type' => 'array',
                'single' => true,
                'show_in_rest' => false,
                'sanitize_callback' => [self::class, 'sanitize_sections'],
                'auth_callback' => [self::class, 'authorize_meta'],
            ]
        );
    }

    /**
     * Authorize edits using the page's native edit capability.
     *
     * @param bool   $allowed Whether WordPress already allowed the write.
     * @param string $metaKey Meta key.
     * @param int    $postId Post ID.
     * @return bool
     */
    public static function authorize_meta(bool $allowed, string $metaKey, int $postId): bool
    {
        return $postId > 0 && current_user_can('edit_post', $postId);
    }

    /** Add the builder to the native Page editor. */
    public static function add_meta_box(): void
    {
        add_meta_box(
            self::META_BOX_ID,
            __('مدیریت سکشن‌های صفحه', 'erfansanat-theme'),
            [self::class, 'render_meta_box'],
            'page',
            'normal',
            'high'
        );
    }

    /**
     * Enqueue the builder UI only on page edit screens.
     *
     * @param string $hook Current admin screen hook.
     */
    public static function enqueue_assets(string $hook): void
    {
        if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || 'page' !== $screen->post_type) {
            return;
        }

        $cssPath = get_theme_file_path('assets/css/admin-section-builder.css');
        $jsPath = get_theme_file_path('assets/js/admin-section-builder.js');
        wp_enqueue_media();
        wp_enqueue_style(
            'erfansanat-section-builder',
            get_theme_file_uri('assets/css/admin-section-builder.css'),
            [],
            is_file($cssPath) ? (string) filemtime($cssPath) : '1.0.0'
        );
        wp_enqueue_script(
            'erfansanat-section-builder',
            get_theme_file_uri('assets/js/admin-section-builder.js'),
            [],
            is_file($jsPath) ? (string) filemtime($jsPath) : '1.0.0',
            true
        );
    }

    /**
     * Render section configuration controls for a page.
     *
     * @param \WP_Post $post Current page post.
     */
    public static function render_meta_box(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
        $savedSections = get_post_meta($post->ID, self::META_KEY, true);
        $sections = self::configured_sections($post, $savedSections);
        usort(
            $sections,
            static function (array $left, array $right): int {
                return ((int) ($left['order'] ?? 0)) <=> ((int) ($right['order'] ?? 0));
            }
        );
        ?>
        <div class="es-section-builder" data-section-builder>
            <p class="es-section-builder-intro"><?php echo esc_html__('سکشن‌ها را بکشید تا ترتیب نمایش آن‌ها تغییر کند. با برداشتن تیک نمایش، سکشن بدون حذف محتوا پنهان می‌شود.', 'erfansanat-theme'); ?></p>
            <div class="es-section-list" data-section-list>
                <?php foreach ($sections as $index => $section) : ?>
                    <?php self::render_section_card($section, (int) $index); ?>
                <?php endforeach; ?>
            </div>
            <p class="es-section-builder-footnote"><?php echo esc_html__('فایل‌های متنی و رسانه‌ای در متادیتای صفحه ذخیره می‌شوند؛ اگر هنوز برای سکشنی قالب اختصاصی ساخته نشده باشد، خروجی استاتیک آن حفظ می‌شود.', 'erfansanat-theme'); ?></p>
        </div>
        <?php
    }

    /**
     * Save sanitized section metadata after verifying capability, request origin, and nonce.
     *
     * @param int     $postId Page ID.
     * @param \WP_Post $post Saved page.
     * @param bool    $update Whether this is an update.
     */
    public static function save_post(int $postId, \WP_Post $post, bool $update): void
    {
        if (
            wp_is_post_autosave($postId)
            || wp_is_post_revision($postId)
            || 'page' !== $post->post_type
            || !current_user_can('edit_post', $postId)
        ) {
            return;
        }

        $rawNonce = isset($_POST[self::NONCE_NAME]) ? wp_unslash($_POST[self::NONCE_NAME]) : '';
        $nonce = is_string($rawNonce) ? sanitize_text_field($rawNonce) : '';
        if ('' === $nonce || !wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            return;
        }
        if (!isset($_POST['es_page_sections']) || !is_array($_POST['es_page_sections'])) {
            return;
        }

        $sections = self::sanitize_sections(wp_unslash($_POST['es_page_sections']), $postId);
        update_post_meta($postId, self::META_KEY, $sections);
    }

    /**
     * Sanitize section records against the page schemas; unknown sections and fields are discarded.
     *
     * @param mixed $sections Submitted section configuration.
     * @param int   $postId Optional page ID used to select its schema.
     * @return array<int, array{id:string, enabled:bool, order:int, fields:array<string, mixed>}>
     */
    public static function sanitize_sections($sections, $postId = 0): array
    {
        if (!is_array($sections)) {
            return [];
        }

        $schema = self::schema_for_post($postId);
        $clean = [];
        $seen = [];
        foreach (array_values($sections) as $section) {
            if (!is_array($section)) {
                continue;
            }
            $id = isset($section['id']) ? sanitize_key((string) $section['id']) : '';
            if ('' === $id || !isset($schema[$id]) || isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;
            $definition = $schema[$id];
            $values = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
            $clean[] = [
                'id' => $id,
                'enabled' => self::to_bool($section['enabled'] ?? false),
                'order' => max(0, min(999, absint($section['order'] ?? count($clean)))),
                'fields' => self::sanitize_fields($definition['fields'], $values),
            ];
        }

        return $clean;
    }

    /**
     * Return visible sections in their saved order, merging in trusted static fallbacks.
     *
     * Fallbacks are a map keyed by section ID. Each item may contain `html` (template-authored
     * markup) and optional default `fields`; user-supplied content is never treated as markup.
     *
     * @param int                  $postId Page ID.
     * @param array<string, mixed> $staticFallbacks Trusted static template fallbacks.
     * @return array<int, array<string, mixed>>
     */
    public static function get_renderable_sections(int $postId, array $staticFallbacks = []): array
    {
        $post = get_post($postId);
        if (!$post instanceof \WP_Post || 'page' !== $post->post_type) {
            return [];
        }

        $saved = get_post_meta($postId, self::META_KEY, true);
        $sections = self::configured_sections($post, $saved);
        $fallbacks = self::normalize_fallbacks($staticFallbacks);
        $schema = self::schema_for_post($postId);
        $renderable = [];

        foreach ($sections as $section) {
            if (empty($section['enabled'])) {
                continue;
            }
            $id = $section['id'];
            $fallback = $fallbacks[$id] ?? [];
            $defaults = isset($schema[$id]) ? self::default_fields($schema[$id]['fields']) : [];
            $renderable[] = array_merge(
                $section,
                [
                    'label' => $schema[$id]['label'] ?? $id,
                    'fields' => array_merge($defaults, $section['fields'] ?? []),
                    'fallback_html' => isset($fallback['html']) && is_string($fallback['html']) ? $fallback['html'] : '',
                ]
            );
        }

        usort(
            $renderable,
            static function (array $left, array $right): int {
                $order = ((int) ($left['order'] ?? 0)) <=> ((int) ($right['order'] ?? 0));
                return 0 === $order ? strcmp((string) $left['id'], (string) $right['id']) : $order;
            }
        );

        return $renderable;
    }

    /**
     * Render each enabled section in configured order, using section templates or static fallbacks.
     *
     * @param int                  $postId Page ID.
     * @param array<string, mixed> $staticFallbacks Trusted static template fallback map.
     */
    public static function render_sections(int $postId, array $staticFallbacks = []): void
    {
        $post = get_post($postId);
        $isHome = $post instanceof \WP_Post
            && (int) get_option('page_on_front') === (int) $post->ID;
        $templateDirectory = $isHome ? 'template-parts/home/' : 'template-parts/sections/';

        foreach (self::get_renderable_sections($postId, $staticFallbacks) as $section) {
            $template = get_theme_file_path($templateDirectory . sanitize_key($section['id']) . '.php');
            $html = '';
            if (is_file($template)) {
                $args = ['section' => $section, 'post_id' => $postId];
                ob_start();
                include $template;
                $html = (string) ob_get_clean();
            }
            $html = (string) apply_filters('erfansanat_render_page_section', $html, $section, $postId);
            if ('' === trim($html)) {
                $html = (string) ($section['fallback_html'] ?? '');
            }
            // A fallback is supplied by a trusted PHP template, never by post metadata.
            echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }

    /**
     * Return section schemas keyed by stable ID for a page or post ID.
     *
     * @param int|\WP_Post $post Page or ID.
     * @return array<string, array<string, mixed>>
     */
    public static function section_schema($post = 0): array
    {
        return self::schema_for_post($post);
    }

    /**
     * Resolve a page's context-specific section schema.
     *
     * @param int|\WP_Post $post Page or ID.
     * @return array<string, array<string, mixed>>
     */
    private static function schema_for_post($post): array
    {
        if (is_int($post) && $post > 0) {
            $post = get_post($post);
        }
        $catalog = self::schemas();
        if ($post instanceof \WP_Post) {
            if ((int) get_option('page_on_front') === (int) $post->ID) {
                return $catalog['home'];
            }
            $rawSlug = strtolower(rawurldecode((string) $post->post_name));
            $slug = sanitize_key($rawSlug);
            if (isset($catalog[$slug])) {
                return $catalog[$slug];
            }
            $pageAliases = [
                'درباره-ما' => 'about',
                'تماس-با-ما' => 'contact',
                'سوالات-متداول' => 'faq',
                'پرسش-های-متداول' => 'faq',
                'مجوزها-و-افتخارات' => 'licenses-and-honors',
                'فرصت-های-شغلی' => 'recruitment',
                'شرایط-گارانتی' => 'refund_returns',
                'بازگشت-کالا' => 'refund_returns',
            ];
            $pageContext = $pageAliases[trim($rawSlug, '/')] ?? '';
            return isset($catalog[$pageContext]) ? $catalog[$pageContext] : $catalog['default'];
        }

        // The registered meta sanitizer has no post ID; validate against the union of known IDs.
        $allSections = [];
        foreach ($catalog as $sections) {
            foreach ($sections as $id => $definition) {
                if (!isset($allSections[$id])) {
                    $allSections[$id] = $definition;
                    continue;
                }
                $allSections[$id]['fields'] = array_merge(
                    $allSections[$id]['fields'],
                    $definition['fields']
                );
            }
        }
        return $allSections;
    }

    /**
     * Resolve configured sections and append newly-added homepage sections for existing front pages.
     *
     * @param \\WP_Post $post Page.
     * @param mixed     $saved Previously saved section records.
     * @return array<int, array<string, mixed>>
     */
    private static function configured_sections(\WP_Post $post, $saved): array
    {
        $sections = is_array($saved) && $saved
            ? self::sanitize_sections($saved, $post->ID)
            : self::default_sections($post);

        if ((int) get_option('page_on_front') !== (int) $post->ID) {
            return $sections;
        }

        $seen = [];
        $nextOrder = 0;
        foreach ($sections as $section) {
            $seen[$section['id']] = true;
            $nextOrder = max($nextOrder, (int) ($section['order'] ?? 0) + 1);
        }
        foreach (self::default_sections($post) as $defaultSection) {
            if (isset($seen[$defaultSection['id']])) {
                continue;
            }
            $defaultSection['order'] = $nextOrder++;
            $sections[] = $defaultSection;
        }

        return $sections;
    }

    /**
     * Defaults for the current page schema.
     *
     * @param \WP_Post $post Page.
     * @return array<int, array<string, mixed>>
     */
    private static function default_sections(\WP_Post $post): array
    {
        $defaults = [];
        $position = 0;
        $isHome = (int) get_option('page_on_front') === (int) $post->ID
            || 'home' === sanitize_key($post->post_name);
        foreach (self::schema_for_post($post) as $definition) {
            if ($isHome && 'gallery' === $definition['id']) {
                continue;
            }
            $defaults[] = [
                'id' => $definition['id'],
                'enabled' => true,
                'order' => $position++,
                'fields' => self::default_fields($definition['fields']),
            ];
        }

        return $defaults;
    }

    /**
     * Defaults for every field in a section schema.
     *
     * @param array<string, array<string, mixed>> $fieldSchema Field definitions.
     * @return array<string, mixed>
     */
    private static function default_fields(array $fieldSchema): array
    {
        $values = [];
        foreach ($fieldSchema as $key => $field) {
            $values[$key] = $field['default'] ?? ('repeater' === ($field['type'] ?? '') ? [] : '');
        }

        return $values;
    }

    /**
     * Sanitize defined fields recursively, including structured links and repeaters.
     *
     * @param array<string, array<string, mixed>> $fieldSchema Field definitions.
     * @param array<string, mixed>                $values Submitted values.
     * @return array<string, mixed>
     */
    private static function sanitize_fields(array $fieldSchema, array $values): array
    {
        $clean = [];
        foreach ($fieldSchema as $key => $field) {
            $value = array_key_exists($key, $values) ? $values[$key] : ($field['default'] ?? '');
            $clean[$key] = self::sanitize_field($field, $value);
        }

        return $clean;
    }

    /**
     * Sanitize one schema-typed value.
     *
     * @param array<string, mixed> $field Field definition.
     * @param mixed                $value Submitted value.
     * @return mixed
     */
    private static function sanitize_field(array $field, $value)
    {
        $type = $field['type'] ?? 'text';
        if ('repeater' === $type) {
            if (!is_array($value)) {
                return [];
            }
            $rows = [];
            foreach (array_slice(array_values($value), 0, 100) as $row) {
                if (is_array($row)) {
                    $rows[] = self::sanitize_fields($field['fields'] ?? [], $row);
                }
            }
            return $rows;
        }
        if ('link' === $type) {
            $value = is_array($value) ? $value : [];
            return [
                'label' => sanitize_text_field((string) ($value['label'] ?? '')),
                'url' => esc_url_raw((string) ($value['url'] ?? '')),
                'target_blank' => self::to_bool($value['target_blank'] ?? false),
            ];
        }
        if ('checkbox' === $type) {
            return self::to_bool($value);
        }
        if (is_array($value) || is_object($value)) {
            return $field['default'] ?? '';
        }

        $value = (string) $value;
        switch ($type) {
            case 'textarea':
                return sanitize_textarea_field($value);
            case 'richtext':
                return wp_kses_post($value);
            case 'image':
            case 'video':
            case 'media':
            case 'url':
                return esc_url_raw($value);
            case 'icon':
                $choices = isset($field['choices']) && is_array($field['choices']) ? $field['choices'] : [];
                return array_key_exists($value, $choices) ? $value : (string) ($field['default'] ?? '');
            case 'color':
                return sanitize_hex_color($value) ?: (string) ($field['default'] ?? '');
            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Normalize an enabled/checked value to a boolean.
     *
     * @param mixed $value Submitted value.
     */
    private static function to_bool($value): bool
    {
        return in_array($value, [true, 1, '1', 'true', 'yes', 'on'], true);
    }

    /**
     * Normalize a keyed or indexed fallback map to section-ID keys.
     *
     * @param array<string, mixed> $fallbacks Fallback sections.
     * @return array<string, array<string, mixed>>
     */
    private static function normalize_fallbacks(array $fallbacks): array
    {
        $normalized = [];
        foreach ($fallbacks as $key => $fallback) {
            if (!is_array($fallback)) {
                continue;
            }
            $id = is_string($key) ? sanitize_key($key) : (isset($fallback['id']) ? sanitize_key((string) $fallback['id']) : '');
            if ('' !== $id) {
                $normalized[$id] = $fallback;
            }
        }

        return $normalized;
    }

    /**
     * Render one draggable section card and its schema-defined fields.
     *
     * @param array<string, mixed> $section Section state.
     * @param int                 $index Section order/index.
     */
    private static function render_section_card(array $section, int $index): void
    {
        $definition = self::section_schema_for_id((string) $section['id']);
        if (!$definition) {
            return;
        }
        $base = 'es_page_sections[' . $index . ']';
        ?>
        <article class="es-section-card" data-section-card data-section-id="<?php echo esc_attr($section['id']); ?>" draggable="true">
            <input type="hidden" name="<?php echo esc_attr($base . '[id]'); ?>" value="<?php echo esc_attr($section['id']); ?>" />
            <input type="hidden" name="<?php echo esc_attr($base . '[order]'); ?>" value="<?php echo esc_attr((string) $section['order']); ?>" data-section-order />
            <header class="es-section-card-header">
                <button type="button" class="es-section-drag" aria-label="<?php echo esc_attr__('کشیدن برای تغییر ترتیب سکشن', 'erfansanat-theme'); ?>" title="<?php echo esc_attr__('تغییر ترتیب', 'erfansanat-theme'); ?>">⠿</button>
                <span class="es-section-order-badge" data-section-order-label><?php echo esc_html((string) ($index + 1)); ?></span>
                <div class="es-section-title-wrap">
                    <h3><?php echo esc_html($definition['label']); ?></h3>
                    <span><?php echo esc_html($section['id']); ?></span>
                </div>
                <label class="es-section-visibility">
                    <input type="hidden" name="<?php echo esc_attr($base . '[enabled]'); ?>" value="0" />
                    <input type="checkbox" name="<?php echo esc_attr($base . '[enabled]'); ?>" value="1" <?php checked(!empty($section['enabled'])); ?> />
                    <span><?php echo esc_html__('نمایش سکشن', 'erfansanat-theme'); ?></span>
                </label>
                <button type="button" class="es-section-toggle" aria-expanded="true"><?php echo esc_html__('بستن', 'erfansanat-theme'); ?></button>
            </header>
            <div class="es-section-card-fields" data-section-fields>
                <?php foreach ($definition['fields'] as $key => $field) : ?>
                    <?php self::render_field($field, $section['fields'][$key] ?? ($field['default'] ?? ''), $base . '[fields][' . $key . ']', $section['id'] . '-' . $key); ?>
                <?php endforeach; ?>
            </div>
        </article>
        <?php
    }

    /**
     * Render a schema-typed field, including media pickers and editable repeaters.
     *
     * @param array<string, mixed> $field Field definition.
     * @param mixed                $value Current value.
     * @param string               $name HTML input name.
     * @param string               $id Stable field ID.
     */
    private static function render_field(array $field, $value, string $name, string $id): void
    {
        $type = (string) ($field['type'] ?? 'text');
        ?>
        <div class="es-section-field es-section-field-<?php echo esc_attr($type); ?>">
            <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($field['label'] ?? ''); ?></label>
            <?php if (in_array($type, ['textarea', 'richtext'], true)) : ?>
                <textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" rows="<?php echo 'richtext' === $type ? '7' : '4'; ?>"><?php echo esc_textarea((string) $value); ?></textarea>
            <?php elseif (in_array($type, ['image', 'video', 'media'], true)) : ?>
                <div class="es-section-media-control">
                    <input id="<?php echo esc_attr($id); ?>" type="url" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr((string) $value); ?>" data-media-type="<?php echo esc_attr('video' === $type ? 'video' : 'image'); ?>" />
                    <button type="button" class="button es-section-select-media"><?php echo esc_html('video' === $type ? __('انتخاب ویدئو', 'erfansanat-theme') : __('انتخاب تصویر', 'erfansanat-theme')); ?></button>
                </div>
            <?php elseif ('link' === $type) : ?>
                <?php $link = is_array($value) ? $value : []; ?>
                <div class="es-section-link-control">
                    <input id="<?php echo esc_attr($id); ?>-label" type="text" name="<?php echo esc_attr($name . '[label]'); ?>" value="<?php echo esc_attr((string) ($link['label'] ?? '')); ?>" placeholder="<?php echo esc_attr__('متن پیوند', 'erfansanat-theme'); ?>" />
                    <input id="<?php echo esc_attr($id); ?>-url" type="url" name="<?php echo esc_attr($name . '[url]'); ?>" value="<?php echo esc_attr((string) ($link['url'] ?? '')); ?>" placeholder="<?php echo esc_attr__('نشانی پیوند', 'erfansanat-theme'); ?>" />
                    <label class="es-section-link-target"><input type="checkbox" name="<?php echo esc_attr($name . '[target_blank]'); ?>" value="1" <?php checked(!empty($link['target_blank'])); ?> /> <?php echo esc_html__('بازکردن در زبانهٔ تازه', 'erfansanat-theme'); ?></label>
                </div>
            <?php elseif ('icon' === $type) : ?>
                <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>">
                    <?php foreach (($field['choices'] ?? []) as $choice => $label) : ?>
                        <option value="<?php echo esc_attr((string) $choice); ?>" <?php selected((string) $value, (string) $choice); ?>><?php echo esc_html((string) $label); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ('checkbox' === $type) : ?>
                <input type="hidden" name="<?php echo esc_attr($name); ?>" value="0" />
                <input id="<?php echo esc_attr($id); ?>" type="checkbox" name="<?php echo esc_attr($name); ?>" value="1" <?php checked((bool) $value); ?> />
            <?php elseif ('repeater' === $type) : ?>
                <?php self::render_repeater($field, is_array($value) ? $value : [], $name, $id); ?>
            <?php else : ?>
                <input id="<?php echo esc_attr($id); ?>" type="<?php echo esc_attr('number' === $type ? 'number' : 'text'); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr((string) $value); ?>" />
            <?php endif; ?>
            <?php if (!empty($field['description'])) : ?>
                <p class="description"><?php echo esc_html($field['description']); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /** Render rows and a cloneable template for a repeater field. */
    private static function render_repeater(array $field, array $rows, string $name, string $id): void
    {
        ?>
        <div class="es-section-repeater" data-section-repeater data-next-index="<?php echo esc_attr((string) count($rows)); ?>">
            <div class="es-repeater-rows" data-repeater-rows>
                <?php foreach ($rows as $rowIndex => $row) : ?>
                    <?php self::render_repeater_row($field, is_array($row) ? $row : [], $name, $id, (string) $rowIndex); ?>
                <?php endforeach; ?>
            </div>
            <template data-repeater-template>
                <?php self::render_repeater_row($field, [], $name, $id, '__INDEX__'); ?>
            </template>
            <button type="button" class="button es-repeater-add"><?php echo esc_html__('افزودن مورد', 'erfansanat-theme'); ?></button>
        </div>
        <?php
    }

    /** Render one repeater row. */
    private static function render_repeater_row(array $field, array $row, string $name, string $id, string $rowIndex): void
    {
        ?>
        <div class="es-repeater-row" data-repeater-row>
            <div class="es-repeater-row-top"><strong><?php echo esc_html__('مورد تکرارشونده', 'erfansanat-theme'); ?></strong><button type="button" class="button-link-delete es-repeater-remove"><?php echo esc_html__('حذف', 'erfansanat-theme'); ?></button></div>
            <div class="es-repeater-row-fields">
                <?php foreach (($field['fields'] ?? []) as $key => $subfield) : ?>
                    <?php self::render_field($subfield, $row[$key] ?? ($subfield['default'] ?? ''), $name . '[' . $rowIndex . '][' . $key . ']', $id . '-' . $rowIndex . '-' . $key); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /** Find a section's schema across all selected pages. */
    private static function section_schema_for_id(string $id): ?array
    {
        foreach (self::schemas() as $sections) {
            if (isset($sections[$id])) {
                return $sections[$id];
            }
        }

        return null;
    }

    /**
     * Page section schema catalog. Content fields are intentionally data-only; template partials
     * escape each value for its context when rendering.
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    private static function schemas(): array
    {
        $icons = [
            'sparkles' => 'درخشش',
            'tree' => 'درخت',
            'building' => 'ساختمان',
            'shield' => 'ضمانت',
            'award' => 'افتخار',
            'phone' => 'تلفن',
            'sun' => 'خورشید',
            'lightbulb' => 'لامپ',
        ];
        $link = self::field('link', __('پیوند', 'erfansanat-theme'), 'link', ['label' => '', 'url' => '', 'target_blank' => false]);
        $hero = self::section(
            'hero',
            __('بخش آغازین', 'erfansanat-theme'),
            [
                self::field('eyebrow', __('متن بالای عنوان', 'erfansanat-theme'), 'text', 'المان‌های نوری شهری · استاندارد IP68'),
                self::field('title', __('عنوان اصلی', 'erfansanat-theme'), 'text', 'وقتی کیفیت و ماندگاری مهمه'),
                self::field('description', __('توضیح', 'erfansanat-theme'), 'textarea', 'عرفان صنعت انتخاب توئه...'),
                self::field('background_image', __('تصویر پس‌زمینه', 'erfansanat-theme'), 'image', get_theme_file_uri('assets/images/hero.jpg')),
                self::field('background_video', __('ویدئوی پس‌زمینه', 'erfansanat-theme'), 'video', get_theme_file_uri('assets/images/hero-video.mp4')),
                self::field('categories_title', __('عنوان دسته‌بندی‌های پربازدید', 'erfansanat-theme'), 'text', 'پربازدیدترین دسته‌بندی‌ها'),
                self::field('video_title', __('عنوان تیزر معرفی', 'erfansanat-theme'), 'text', 'تیزر رسمی خط تولید و پروژه‌های عرفان صنعت اصفهان'),
                self::field('video_cta', __('متن پیوند تیزر', 'erfansanat-theme'), 'text', 'درباره کارخانه'),
                self::field('video_button_label', __('برچسب دکمه تیزر', 'erfansanat-theme'), 'text', 'معرفی شرکت'),
                self::field('project_cta', __('متن پیوند پروژه‌های اسلایدر', 'erfansanat-theme'), 'text', 'مشاهده پروژه'),
                self::field('icon', __('آیکون', 'erfansanat-theme'), 'icon', 'sparkles', '', ['choices' => $icons]),
                self::field('primary_link', __('پیوند اصلی', 'erfansanat-theme'), 'link', ['label' => 'درباره کارخانه', 'url' => home_url('/درباره-ما/'), 'target_blank' => false]),
                self::field('secondary_link', __('پیوند دوم', 'erfansanat-theme'), 'link', $link['default']),
                self::field('highlights', __('نکته‌های برجسته', 'erfansanat-theme'), 'repeater', [], '', [
                    'fields' => [
                        'title' => self::field('title', __('عنوان', 'erfansanat-theme'), 'text', ''),
                        'description' => self::field('description', __('توضیح', 'erfansanat-theme'), 'text', ''),
                        'icon' => self::field('icon', __('آیکون', 'erfansanat-theme'), 'icon', 'sparkles', '', ['choices' => $icons]),
                    ],
                ]),
            ]
        );
        $heading = self::field('title', __('عنوان سکشن', 'erfansanat-theme'), 'text', '');
        $description = self::field('description', __('توضیح سکشن', 'erfansanat-theme'), 'textarea', '');
        $content = self::field('content', __('متن کامل', 'erfansanat-theme'), 'richtext', '');
        $media = self::field('image', __('تصویر', 'erfansanat-theme'), 'image', '');
        $cards = static function (string $key, string $label) use ($icons, $link): array {
            return self::field($key, $label, 'repeater', [], '', [
                'fields' => [
                    'image' => self::field('image', __('تصویر', 'erfansanat-theme'), 'image', ''),
                    'title' => self::field('title', __('عنوان', 'erfansanat-theme'), 'text', ''),
                    'description' => self::field('description', __('توضیح', 'erfansanat-theme'), 'textarea', ''),
                    'icon' => self::field('icon', __('آیکون', 'erfansanat-theme'), 'icon', 'sparkles', '', ['choices' => $icons]),
                    'link' => self::field('link', __('پیوند', 'erfansanat-theme'), 'link', $link['default']),
                ],
            ]);
        };
        $timeline = self::field('milestones', __('نقاط عطف', 'erfansanat-theme'), 'repeater', [], '', [
            'fields' => [
                'year' => self::field('year', __('سال', 'erfansanat-theme'), 'text', ''),
                'title' => self::field('title', __('عنوان', 'erfansanat-theme'), 'text', ''),
                'description' => self::field('description', __('توضیح', 'erfansanat-theme'), 'textarea', ''),
                'image' => self::field('image', __('تصویر', 'erfansanat-theme'), 'image', ''),
            ],
        ]);
        $faqItems = self::field('items', __('سوال‌ها و جواب‌ها', 'erfansanat-theme'), 'repeater', [], '', [
            'fields' => [
                'question' => self::field('question', __('سوال', 'erfansanat-theme'), 'text', ''),
                'answer' => self::field('answer', __('پاسخ', 'erfansanat-theme'), 'textarea', ''),
            ],
        ]);

        $projectTitle = self::field('title', __('عنوان سکشن', 'erfansanat-theme'), 'text', 'نمونه‌کارهای شاخص نورپردازی شهری');
        $projectDescription = self::field('description', __('توضیح سکشن', 'erfansanat-theme'), 'textarea', 'اجرای تخصصی پروژه‌های نورپردازی برای شهرداری‌ها و سازمان‌های زیباسازی؛ تلفیق مهندسی سازه، استاندارد حفاظتی IP68 و زیبایی‌شناسی شهری.');
        $projectArchive = self::section('project-archive', __('آرشیو پروژه‌ها', 'erfansanat-theme'), [
            self::field('title', __('عنوان سکشن', 'erfansanat-theme'), 'text', 'نمونه‌کارهای اجراشده در کلان‌شهرهای کشور'),
            self::field('description', __('توضیح سکشن', 'erfansanat-theme'), 'textarea', 'آرشیو تفکیکی پروژه‌های اجرایی عرفان صنعت.'),
            $cards('items', __('پروژه‌ها', 'erfansanat-theme')),
        ]);
        $companyIdentity = self::section('company-identity', __('هویت و معرفی شرکت', 'erfansanat-theme'), [$heading, $content, $media, $link]);
        $catalog = self::section('catalog', __('کاتالوگ محصولات', 'erfansanat-theme'), [$heading, $description, self::field('catalog_image', __('تصویر کاتالوگ', 'erfansanat-theme'), 'image', ''), self::field('catalog_link', __('پیوند کاتالوگ', 'erfansanat-theme'), 'link', $link['default'])]);
        $customProductCta = [
            self::field('custom_cta_title', __('عنوان سفارش اختصاصی', 'erfansanat-theme'), 'text', 'طراحی و ساخت المان مطابق طرح اختصاصی شما'),
            self::field('custom_cta_description', __('توضیح سفارش اختصاصی', 'erfansanat-theme'), 'textarea', 'واحد مهندسی و قالب‌سازی کارخانه آماده طراحی سه‌بعدی و ساخت سازه‌های نوری سفارشی برای میادین و بوستان‌هاست.'),
            self::field('custom_cta_link', __('پیوند سفارش اختصاصی', 'erfansanat-theme'), 'link', ['label' => 'ثبت سفارش اختصاصی', 'url' => home_url('/تماس-با-ما/'), 'target_blank' => false]),
        ];
        $benefits = self::section('benefits', __('مزایا و فرایند همکاری', 'erfansanat-theme'), [$heading, $description, $cards('items', __('مزایا', 'erfansanat-theme'))]);
        $clientDefaults = [
            ['image' => '', 'title' => 'شهرداری اصفهان', 'description' => 'پروژه‌های تونل و المان نوری', 'link' => $link['default']],
            ['image' => '', 'title' => 'شهرداری شیراز', 'description' => 'نورپردازی بلوار و میادین', 'link' => $link['default']],
            ['image' => '', 'title' => 'شهرداری مشهد', 'description' => 'درختان نوری و ریسه IP68', 'link' => $link['default']],
            ['image' => '', 'title' => 'شهرداری کرج', 'description' => 'تامین تجهیزات روشنایی', 'link' => $link['default']],
            ['image' => '', 'title' => 'شهرداری اهواز', 'description' => 'پروژه‌های ساحلی و پل‌ها', 'link' => $link['default']],
            ['image' => '', 'title' => 'شهرداری یزد', 'description' => 'المان‌های نوری بافت تاریخی', 'link' => $link['default']],
            ['image' => '', 'title' => 'شهرداری تبریز', 'description' => 'سازه‌های نوری مقاوم به سرما', 'link' => $link['default']],
            ['image' => '', 'title' => 'سامانه ستاد دولت', 'description' => 'تامین‌کننده رسمی ثبت‌شده', 'link' => $link['default']],
            ['image' => '', 'title' => 'مرکز ملی ایران‌کد', 'description' => 'دارای کد ملی کالا GS1', 'link' => $link['default']],
            ['image' => '', 'title' => 'شهرداری ورامین', 'description' => 'لوسترهای ستون و بلوار', 'link' => $link['default']],
            ['image' => '', 'title' => 'شهرداری ارومیه', 'description' => 'نورپردازی پارک‌ها و معابر', 'link' => $link['default']],
            ['image' => '', 'title' => 'سازمان زیباسازی', 'description' => 'طراحی و ساخت المان حجمی', 'link' => $link['default']],
        ];
        $clientLogos = self::field('logos', __('مشتریان و همکاران', 'erfansanat-theme'), 'repeater', $clientDefaults, '', [
            'fields' => [
                'image' => $media,
                'title' => self::field('title', __('نام سازمان', 'erfansanat-theme'), 'text', ''),
                'description' => self::field('description', __('شرح همکاری', 'erfansanat-theme'), 'textarea', ''),
                'link' => $link,
            ],
        ]);
        $statSources = ['projects' => __('پروژه‌های منتشرشده', 'erfansanat-theme'), 'products' => __('محصولات منتشرشده', 'erfansanat-theme'), 'clients' => __('تعداد مشتریان تنظیم‌شده', 'erfansanat-theme'), 'custom' => __('عدد دستی', 'erfansanat-theme')];
        $statDefaults = [
            ['source' => 'projects', 'value' => '850', 'suffix' => '+', 'label' => 'پروژه تکمیل‌شده', 'description' => 'در کلان‌شهرهای ایران و عراق'],
            ['source' => 'custom', 'value' => '100', 'suffix' => '+', 'label' => 'نیروی متخصص', 'description' => 'واحد طراحی، قالب‌سازی و مونتاژ'],
            ['source' => 'custom', 'value' => '289', 'suffix' => '+', 'label' => 'کارفرمای سازمانی', 'description' => 'شهرداری‌ها و سازمان‌های عمرانی'],
            ['source' => 'products', 'value' => '125', 'suffix' => '+', 'label' => 'محصول تخصصی', 'description' => 'دارای کد ملی کالا GS1'],
        ];
        $stats = self::field('stats', __('آمار و دستاوردها', 'erfansanat-theme'), 'repeater', $statDefaults, '', [
            'fields' => [
                'source' => self::field('source', __('منبع مقدار', 'erfansanat-theme'), 'icon', 'custom', '', ['choices' => $statSources]),
                'value' => self::field('value', __('مقدار دستی', 'erfansanat-theme'), 'number', ''),
                'suffix' => self::field('suffix', __('پسوند مقدار', 'erfansanat-theme'), 'text', '+'),
                'label' => self::field('label', __('عنوان آمار', 'erfansanat-theme'), 'text', ''),
                'description' => self::field('description', __('توضیح آمار', 'erfansanat-theme'), 'text', ''),
            ],
        ]);

        return [
            'home' => [
                'hero' => $hero,
                'projects' => self::section('projects', __('پروژه‌های منتخب', 'erfansanat-theme'), [$projectTitle, $projectDescription, $cards('items', __('پروژه‌ها', 'erfansanat-theme'))]),
                'project-archive' => $projectArchive,
                'about' => self::section('about', __('درباره شرکت', 'erfansanat-theme'), [self::field('title', __('عنوان سکشن', 'erfansanat-theme'), 'text', 'بیش از ۲۵ سال تجربه مهندسی در تولید سازه‌های نوری شهری'), $content, $media, $link, $stats]),
                'company-identity' => $companyIdentity,
                'history' => self::section('history', __('تاریخچه شرکت', 'erfansanat-theme'), [$heading, $description, $timeline]),
                'products' => self::section('products', __('محصولات', 'erfansanat-theme'), [self::field('title', __('عنوان سکشن', 'erfansanat-theme'), 'text', 'منتخب محصولات عرفان صنعت اصفهان'), self::field('description', __('توضیح سکشن', 'erfansanat-theme'), 'textarea', 'تولید صنعتی تجهیزات نورپردازی شهری با عایق‌بندی تزریق مستقیم، بدنه پلی‌کربنات نشکن و کنترلرهای مرکزی بی‌سیم.'), $cards('items', __('محصولات', 'erfansanat-theme')), ...$customProductCta]),
                'catalog' => $catalog,
                'services' => self::section('services', __('خدمات', 'erfansanat-theme'), [$heading, $description, $cards('items', __('خدمات', 'erfansanat-theme'))]),
                'benefits' => $benefits,
                'after-sales' => self::section('after-sales', __('خدمات پس از فروش', 'erfansanat-theme'), [$heading, $description, $cards('items', __('مزایا', 'erfansanat-theme'))]),
                'clients' => self::section('clients', __('مشتریان و همکاران', 'erfansanat-theme'), [self::field('title', __('عنوان سکشن', 'erfansanat-theme'), 'text', 'مشتریان و سازمان‌های همکار عرفان صنعت'), self::field('description', __('توضیح سکشن', 'erfansanat-theme'), 'textarea', 'افتخار همراهی با بیش از ۱۸۰ شهرداری، سازمان زیباسازی و پیمانکار عمرانی در سراسر کشور'), $clientLogos]),
                'blog-preview' => self::section('blog-preview', __('مقالات منتخب', 'erfansanat-theme'), [self::field('title', __('عنوان سکشن', 'erfansanat-theme'), 'text', 'جدیدترین مقالات و راهنماهای تخصصی نورپردازی'), self::field('description', __('توضیح سکشن', 'erfansanat-theme'), 'textarea', 'بررسی فنی تجهیزات LED، استانداردهای حفاظتی IP68 و راهنمای خرید المان‌های نوری شهری'), $link]),
                'contact' => self::section('contact', __('تماس با ما', 'erfansanat-theme'), [$heading, $description, self::field('phone', __('تلفن', 'erfansanat-theme'), 'text', ''), self::field('email', __('ایمیل', 'erfansanat-theme'), 'text', ''), self::field('address', __('نشانی', 'erfansanat-theme'), 'textarea', ''), $link]),
                'location' => self::section('location', __('موقعیت کارخانه', 'erfansanat-theme'), [$heading, self::field('address', __('نشانی', 'erfansanat-theme'), 'textarea', ''), self::field('map_url', __('پیوند نقشه', 'erfansanat-theme'), 'url', ''), $link]),
                // Retained for legacy metadata compatibility; not part of the mirror's default homepage.
                'gallery' => self::section('gallery', __('گالری پروژه‌ها', 'erfansanat-theme'), [$heading, $description, self::field('images', __('تصاویر', 'erfansanat-theme'), 'repeater', [], '', ['fields' => ['image' => $media, 'caption' => self::field('caption', __('شرح تصویر', 'erfansanat-theme'), 'text', '')]])]),
            ],
            'about' => [
                'hero' => $hero,
                'company' => self::section('company', __('معرفی شرکت', 'erfansanat-theme'), [$heading, $content, $media, $link]),
                'history' => self::section('history', __('تاریخچه', 'erfansanat-theme'), [$heading, $description, $timeline]),
                'certifications' => self::section('certifications', __('گواهینامه‌ها و افتخارات', 'erfansanat-theme'), [$heading, $cards('items', __('افتخارات', 'erfansanat-theme'))]),
            ],
            'contact' => [
                'hero' => $hero,
                'contact-info' => self::section('contact-info', __('اطلاعات تماس', 'erfansanat-theme'), [$heading, $description, self::field('phone', __('تلفن', 'erfansanat-theme'), 'text', ''), self::field('sales_phone', __('تلفن فروش', 'erfansanat-theme'), 'text', ''), self::field('email', __('ایمیل', 'erfansanat-theme'), 'text', ''), self::field('address', __('نشانی', 'erfansanat-theme'), 'textarea', ''), self::field('hours', __('ساعات کاری', 'erfansanat-theme'), 'text', ''), $link]),
                'location' => self::section('location', __('موقعیت روی نقشه', 'erfansanat-theme'), [$heading, self::field('map_url', __('پیوند نقشه', 'erfansanat-theme'), 'url', ''), self::field('map_embed_url', __('نشانی embed نقشه', 'erfansanat-theme'), 'url', '')]),
            ],
            'services' => [
                'hero' => $hero,
                'services-list' => self::section('services-list', __('فهرست خدمات', 'erfansanat-theme'), [$heading, $description, $cards('items', __('خدمات', 'erfansanat-theme'))]),
                'cta' => self::section('cta', __('دعوت به اقدام', 'erfansanat-theme'), [$heading, $description, $link]),
            ],
            'faq' => [
                'hero' => $hero,
                'faq' => self::section('faq', __('پرسش‌های متداول', 'erfansanat-theme'), [$heading, $description, $faqItems]),
                'contact' => self::section('contact', __('تماس برای پرسش بیشتر', 'erfansanat-theme'), [$heading, $description, $link]),
            ],
            'licenses-and-honors' => [
                'hero' => $hero,
                'licenses' => self::section('licenses', __('مجوزها و افتخارات', 'erfansanat-theme'), [$heading, $description, $cards('items', __('مدارک و افتخارات', 'erfansanat-theme'))]),
            ],
            'recruitment' => [
                'hero' => $hero,
                'jobs' => self::section('jobs', __('فرصت‌های شغلی', 'erfansanat-theme'), [
                    $heading,
                    $description,
                    self::field('items', __('فرصت‌ها', 'erfansanat-theme'), 'repeater', [], '', [
                        'fields' => [
                            'title' => self::field('title', __('عنوان موقعیت شغلی', 'erfansanat-theme'), 'text', ''),
                            'category' => self::field('category', __('دسته‌بندی شغلی', 'erfansanat-theme'), 'text', ''),
                            'description' => self::field('description', __('شرح موقعیت', 'erfansanat-theme'), 'textarea', ''),
                            'image' => $media,
                            'link' => $link,
                        ],
                    ]),
                ]),
            ],
            'refund_returns' => [
                'hero' => $hero,
                'policy' => self::section('policy', __('ضمانت و خدمات پس از فروش', 'erfansanat-theme'), [$heading, $content, $media]),
                'faq' => self::section('faq', __('پرسش‌های گارانتی', 'erfansanat-theme'), [$heading, $faqItems]),
            ],
            'arya' => [
                'hero' => $hero,
                'features' => self::section('features', __('ویژگی‌های محصول آریا', 'erfansanat-theme'), [$heading, $description, $cards('items', __('ویژگی‌ها', 'erfansanat-theme'))]),
                'content' => self::section('content', __('معرفی محصول آریا', 'erfansanat-theme'), [$heading, $content, $media]),
                'specs' => self::section('specs', __('مشخصات آریا', 'erfansanat-theme'), [$heading, $cards('items', __('مشخصات', 'erfansanat-theme'))]),
                'faq' => self::section('faq', __('پرسش‌های آریا', 'erfansanat-theme'), [$heading, $faqItems]),
                'cta' => self::section('cta', __('درخواست مشاوره آریا', 'erfansanat-theme'), [$heading, $description, $link]),
            ],
            'smartelement' => [
                'hero' => $hero,
                'company' => self::section('company', __('معرفی خدمات هوشمند', 'erfansanat-theme'), [$heading, $content, $media, $link]),
                'services-list' => self::section('services-list', __('خدمات هوشمند', 'erfansanat-theme'), [$heading, $description, $cards('items', __('خدمات', 'erfansanat-theme'))]),
                'benefits' => $benefits,
                'faq' => self::section('faq', __('پرسش‌های خدمات هوشمند', 'erfansanat-theme'), [$heading, $faqItems]),
                'cta' => self::section('cta', __('درخواست خدمات', 'erfansanat-theme'), [$heading, $description, $link]),
            ],
            'default' => [
                'hero' => $hero,
                'content' => self::section('content', __('محتوای اصلی', 'erfansanat-theme'), [$heading, $content, $media]),
                'gallery' => self::section('gallery', __('گالری رسانه', 'erfansanat-theme'), [$heading, self::field('images', __('تصاویر', 'erfansanat-theme'), 'repeater', [], '', ['fields' => ['image' => $media, 'caption' => self::field('caption', __('شرح تصویر', 'erfansanat-theme'), 'text', '')]])]),
                'cta' => self::section('cta', __('دعوت به اقدام', 'erfansanat-theme'), [$heading, $description, $link]),
            ],
        ];
    }

    /** Build a named section schema. */
    private static function section(string $id, string $label, array $fields): array
    {
        $indexed = [];
        foreach ($fields as $field) {
            if (isset($field['key'])) {
                $indexed[$field['key']] = $field;
            }
        }

        return ['id' => $id, 'label' => $label, 'fields' => $indexed];
    }

    /** Build one field schema. */
    private static function field(string $key, string $label, string $type, $default = '', string $description = '', array $extra = []): array
    {
        $field = array_merge(
            [
                'key' => $key,
                'label' => $label,
                'type' => $type,
                'default' => $default,
            ],
            $extra
        );
        if ('' !== $description) {
            $field['description'] = $description;
        }

        return $field;
    }
}
