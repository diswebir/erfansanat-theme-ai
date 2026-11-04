<?php
/** Private form-message content type and read/unread admin controls. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Forms;

use ErfanSanat\Theme\Contracts\ServiceInterface;

final class FormSubmissions implements ServiceInterface
{
    public const POST_TYPE = 'es_form_message';

    public function register(): void
    {
        add_action('init', [$this, 'registerPostType']);
        add_filter('manage_' . self::POST_TYPE . '_posts_columns', [$this, 'columns']);
        add_action('manage_' . self::POST_TYPE . '_posts_custom_column', [$this, 'renderColumn'], 10, 2);
        add_action('admin_post_es_toggle_form_message_read', [$this, 'handleToggleRead']);
    }

    public function registerPostType(): void
    {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name' => 'پیام‌های فرم',
                'singular_name' => 'پیام فرم',
                'menu_name' => 'پیام‌های فرم',
                'all_items' => 'همه پیام‌ها',
                'edit_item' => 'مشاهده پیام',
            ],
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => false,
            'has_archive' => false,
            'rewrite' => false,
            'supports' => ['title', 'editor', 'custom-fields'],
            'capability_type' => 'post',
            'capabilities' => [
                'edit_post' => 'manage_options',
                'read_post' => 'manage_options',
                'delete_post' => 'manage_options',
                'edit_posts' => 'manage_options',
                'edit_others_posts' => 'manage_options',
                'publish_posts' => 'manage_options',
                'read_private_posts' => 'manage_options',
                'delete_posts' => 'manage_options',
                'delete_private_posts' => 'manage_options',
                'delete_published_posts' => 'manage_options',
                'delete_others_posts' => 'manage_options',
                'edit_private_posts' => 'manage_options',
                'edit_published_posts' => 'manage_options',
            ],
            'map_meta_cap' => false,
            'menu_icon' => 'dashicons-email-alt',
        ]);
    }

    /** @param array<string,string> $columns @return array<string,string> */
    public function columns(array $columns): array
    {
        return [
            'cb' => $columns['cb'] ?? '<input type="checkbox" />',
            'title' => 'عنوان پیام',
            'es_message_type' => 'فرم',
            'es_message_read' => 'وضعیت',
            'date' => 'تاریخ',
        ];
    }

    public function renderColumn(string $column, int $postId): void
    {
        if ($column === 'es_message_type') {
            echo esc_html((string) get_post_meta($postId, 'es_message_type', true));
            return;
        }
        if ($column !== 'es_message_read') return;
        $isRead = get_post_meta($postId, 'es_message_read', true) === '1';
        $label = $isRead ? 'خوانده‌شده' : 'خوانده‌نشده';
        $next = $isRead ? '0' : '1';
        $url = wp_nonce_url(
            add_query_arg(['action' => 'es_toggle_form_message_read', 'post_id' => $postId], admin_url('admin-post.php')),
            'es_toggle_form_message_read_' . $postId
        );
        echo '<span>' . esc_html($label) . '</span> <a href="' . esc_url($url) . '" aria-label="' . esc_attr($isRead ? 'علامت‌گذاری به‌عنوان نخوانده' : 'علامت‌گذاری به‌عنوان خوانده‌شده') . '">' . esc_html($next === '1' ? 'علامت‌گذاری خوانده‌شده' : 'علامت‌گذاری نخوانده') . '</a>';
    }

    public function setReadStatus(int $postId, bool $isRead): bool
    {
        if (get_post_type($postId) !== self::POST_TYPE) return false;
        update_post_meta($postId, 'es_message_read', $isRead ? '1' : '0');
        return get_post_meta($postId, 'es_message_read', true) === ($isRead ? '1' : '0');
    }

    public function handleToggleRead(): void
    {
        if (!current_user_can('manage_options')) wp_die(esc_html__('دسترسی غیرمجاز است.'));
        $postId = isset($_GET['post_id']) ? absint(wp_unslash($_GET['post_id'])) : 0;
        check_admin_referer('es_toggle_form_message_read_' . $postId);
        $this->setReadStatus($postId, get_post_meta($postId, 'es_message_read', true) !== '1');
        wp_safe_redirect(admin_url('edit.php?post_type=' . self::POST_TYPE));
        exit;
    }
}
