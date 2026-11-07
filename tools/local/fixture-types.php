<?php
/**
 * Plugin Name: ErfanSanat Local Fixture Types
 * Description: Environment-only CPT/taxonomy registration for synthetic test data. Not the production theme.
 * Version: 1.0.0
 *
 * This mu-plugin exists only so the isolated WordPress test site can store and
 * render fixtures before the bespoke theme reaches Phase 3. It is never shipped
 * as part of the theme and must not be installed on production.
 */

defined('ABSPATH') || exit;

add_action('init', static function (): void {
    if (!post_type_exists('projects')) {
        register_post_type('projects', [
            'labels' => ['name' => 'پروژه‌ها', 'singular_name' => 'پروژه', 'add_new_item' => 'افزودن پروژه آزمایشی'],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'projects', 'with_front' => false],
            'hierarchical' => false,
            'menu_icon' => 'dashicons-screenoptions',
            'supports' => ['title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions'],
            'show_in_rest' => true,
            'taxonomies' => ['project_type', 'city'],
        ]);
    }
    if (!post_type_exists('services')) {
        register_post_type('services', [
            'labels' => ['name' => 'خدمات', 'singular_name' => 'خدمت', 'add_new_item' => 'افزودن خدمت آزمایشی'],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'services', 'with_front' => false],
            'hierarchical' => false,
            'menu_icon' => 'dashicons-hammer',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes'],
            'show_in_rest' => true,
        ]);
    }
    foreach (['project_type' => 'نوع پروژه', 'city' => 'شهر'] as $tax => $label) {
        if (!taxonomy_exists($tax)) {
            register_taxonomy($tax, ['projects'], [
                'labels' => ['name' => $label, 'singular_name' => $label],
                'hierarchical' => true,
                'public' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'rewrite' => ['slug' => $tax, 'with_front' => false, 'hierarchical' => false],
            ]);
        }
    }
}, 5);

add_action('admin_notices', static function (): void {
    echo '<div class="notice notice-warning"><p><strong>محیط آزمایشی عرفان صنعت:</strong> همه داده‌ها، قیمت‌ها، سفارش‌ها و دیدگاه‌های این سایت ساختگی هستند و نباید به سایت زنده منتقل شوند.</p></div>';
});

add_filter('admin_footer_text', static fn() => 'محیط آزمایشی محلی — داده‌ها ساختگی هستند.');

// Arena preview is embedded under a different origin than the sandbox proxy.
// Remove only the local admin/login frame restrictions so the live preview works.
// This mu-plugin is environment-only and must never ship to production.
add_action('admin_init', static function (): void {
    remove_action('admin_init', 'send_frame_options_header');
}, 0);
add_action('login_init', static function (): void {
    remove_action('login_init', 'send_frame_options_header');
}, 0);
add_filter('wp_headers', static function (array $headers): array {
    unset($headers['X-Frame-Options'], $headers['Content-Security-Policy']);
    return $headers;
});
