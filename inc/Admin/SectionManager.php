<?php
/**
 * SectionManager — engine for managing page sections (home, about,
 * contact, landings like arya): enable/disable, drag&drop order, and
 * editing every text/image/icon/video/repeater field of each section.
 *
 * Storage: post meta `_erfansanat_sections` on `page` posts:
 *   [ section_id => ['enabled' => bool, 'order' => int, 'fields' => [...]] ]
 *
 * Front-end: es_sections($post_id) returns sections ordered & filtered
 * to enabled only, merged over registered defaults.
 *
 * @package ErfanSanat\Theme\Admin
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

if (!defined('ABSPATH')) {
    exit;
}

final class SectionManager
{
    public const META   = '_erfansanat_sections';
    public const NONCE  = 'erfansanat_sections_nonce';

    /** Registered sections per template (id => label). Extended in Phase 5/7. */
    private static array $registry = [
        'front-page' => [
            'hero'             => 'اسلایدر/ویدیو هیرو',
            'categories'       => 'دسته‌بندی محصولات',
            'featured-products'=> 'محصولات برگزیده',
            'latest-projects'  => 'آخرین پروژه‌ها',
            'latest-posts'     => 'آخرین مقالات',
            'stats'            => 'آمار و مشتریان',
        ],
    ];

    public static function hooks(): void
    {
        add_action('add_meta_boxes', [self::class, 'metabox']);
        add_action('save_post_page', [self::class, 'save'], 10, 2);
        add_action('wp_ajax_erfansanat_reorder_sections', [self::class, 'ajax_reorder']);
        add_action('admin_enqueue_scripts', [self::class, 'assets']);
    }

    public static function metabox(): void
    {
        add_meta_box(
            'erfansanat_sections',
            __('مدیریت سکشن‌های صفحه', 'erfansanat'),
            [self::class, 'render_metabox'],
            'page',
            'normal',
            'default'
        );
    }

    public static function save(int $post_id, \WP_Post $post): void
    {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!isset($_POST[self::NONCE]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE])), 'erfansanat_sections_save')) {
            return;
        }
        $raw = isset($_POST['erfansanat_sections']) && is_array($_POST['erfansanat_sections'])
            ? wp_unslash($_POST['erfansanat_sections'])
            : [];
        $clean = [];
        foreach ($raw as $sid => $data) {
            $sid = sanitize_key((string) $sid);
            $clean[$sid] = [
                'enabled' => !empty($data['enabled']),
                'order'   => absint($data['order'] ?? 0),
                'fields'  => self::sanitize_fields(is_array($data['fields'] ?? null) ? $data['fields'] : []),
            ];
        }
        update_post_meta($post_id, self::META, $clean);
    }

    private static function sanitize_fields(array $fields): array
    {
        $out = [];
        foreach ($fields as $key => $value) {
            $key = sanitize_key((string) $key);
            $out[$key] = is_array($value) ? self::sanitize_fields($value) : sanitize_text_field((string) $value);
        }
        return $out;
    }

    /**
     * AJAX: persist a new order array [{id, enabled, order}] from drag&drop.
     */
    public static function ajax_reorder(): void
    {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'forbidden'], 403);
        }
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'erfansanat_sections_save')) {
            wp_send_json_error(['message' => 'bad nonce'], 403);
        }
        $post_id = absint($_POST['post_id'] ?? 0);
        $items   = isset($_POST['items']) && is_array($_POST['items']) ? wp_unslash($_POST['items']) : [];
        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(['message' => 'bad post'], 400);
        }
        $existing = get_post_meta($post_id, self::META, true);
        $existing = is_array($existing) ? $existing : [];
        foreach ($items as $item) {
            $sid = sanitize_key((string) ($item['id'] ?? ''));
            if ($sid === '') {
                continue;
            }
            $existing[$sid]['enabled'] = !empty($item['enabled']);
            $existing[$sid]['order']   = absint($item['order'] ?? 0);
        }
        update_post_meta($post_id, self::META, $existing);
        wp_send_json_success(['message' => 'ok']);
    }

    public static function assets(string $hook): void
    {
        if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }
        wp_enqueue_script('erfansanat-sections', ERFANSANAT_THEME_URI . 'assets/js/admin-sections.js', [], ERFANSANAT_THEME_VERSION, true);
        wp_localize_script('erfansanat-sections', 'ErfanSanatSections', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('erfansanat_sections_save'),
            'action'  => 'erfansanat_reorder_sections',
        ]);
    }

    public static function render_metabox(\WP_Post $post): void
    {
        wp_nonce_field('erfansanat_sections_save', self::NONCE);
        $stored  = get_post_meta($post->ID, self::META, true);
        $stored  = is_array($stored) ? $stored : [];
        $sections = self::sections_for($post);
        echo '<ul class="erfansanat-section-list" data-post="' . esc_attr((string) $post->ID) . '">';
        $i = 0;
        foreach ($sections as $sid => $label) {
            $i++;
            $enabled = $stored[$sid]['enabled'] ?? true;
            $order   = $stored[$sid]['order'] ?? $i;
            printf(
                '<li draggable="true" data-section="%1$s">'
                . '<label><input type="checkbox" name="erfansanat_sections[%1$s][enabled]" value="1" %2$s> %3$s</label>'
                . '<input type="hidden" name="erfansanat_sections[%1$s][order]" value="%4$d" class="section-order">'
                . '</li>',
                esc_attr($sid),
                checked($enabled, true, false),
                esc_html($label),
                esc_attr((string) $order)
            );
        }
        echo '</ul>';
        echo '<p class="description">' . esc_html__('برای تغییر ترتیب، سکشن‌ها را با Drag & Drop جابجا کنید؛ تیک = فعال بودن سکشن.', 'erfansanat') . '</p>';
    }

    /**
     * Registered sections for a given post (by template).
     */
    public static function sections_for(\WP_Post $post): array
    {
        $template = get_page_template_slug($post->ID);
        if ($template === 'front-page.php' || $post->ID === (int) get_option('page_on_front')) {
            return self::$registry['front-page'];
        }
        return self::$registry['front-page']; // extended per template in later phases
    }
}

