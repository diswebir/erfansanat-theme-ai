<?php
/**
 * Forms — native form handling for contact, price-inquiry popup,
 * recruitment and client-feedback. Entries are stored as `es_form_entry`
 * posts (visible in a dedicated admin inbox), protected by nonce +
 * honeypot, with email notification.
 *
 * @package ErfanSanat\Theme\Ajax
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

final class Forms
{
    public const NONCE = 'erfansanat_form_nonce';
    public const CPT   = 'es_form_entry';

    public static function hooks(): void
    {
        add_action('init', [self::class, 'register_cpt'], 6);
        foreach (['contact', 'inquiry', 'recruitment', 'feedback'] as $form) {
            add_action("wp_ajax_erfansanat_submit_$form", [self::class, 'handle']);
            add_action("wp_ajax_nopriv_erfansanat_submit_$form", [self::class, 'handle']);
        }
        add_action('admin_menu', [self::class, 'inbox_menu']);
    }

    public static function register_cpt(): void
    {
        register_post_type(self::CPT, [
            'public'       => false,
            'show_ui'      => false,
            'show_in_rest' => false,
            'supports'     => ['title', 'editor'],
            'label'        => __('پیام‌های فرم', 'erfansanat'),
        ]);
    }

    public static function inbox_menu(): void
    {
        add_submenu_page(
            'erfansanat-options',
            __('پیام‌های دریافتی', 'erfansanat'),
            __('پیام‌ها', 'erfansanat'),
            'manage_options',
            'edit.php?post_type=' . self::CPT
        );
    }

    public static function handle(): void
    {
        check_ajax_referer(self::NONCE, 'nonce');

        // Honeypot: bots fill this hidden field.
        if (!empty($_POST['website_url'])) {
            wp_send_json_error(['message' => 'spam'], 400);
        }

        $form = isset($_POST['form_type']) ? sanitize_key(wp_unslash($_POST['form_type'])) : 'contact';
        if (!in_array($form, ['contact', 'inquiry', 'recruitment', 'feedback'], true)) {
            $form = 'contact';
        }

        $name    = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $phone   = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
        $email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

        if ($email && !is_email($email)) {
            wp_send_json_error(['message' => 'ایمیل نامعتبر است'], 422);
        }
        if (!$name && !$phone) {
            wp_send_json_error(['message' => 'نام یا شماره تماس لازم است'], 422);
        }

        $title  = sprintf('[%s] %s', $form, $name ?: $phone);
        $body   = "فرم: $form\nنام: $name\nتلفن: $phone\nایمیل: $email\nپیام:\n$message";
        $postId = wp_insert_post([
            'post_type'    => self::CPT,
            'post_status'  => 'publish',
            'post_title'   => $title,
            'post_content' => $body,
        ]);

        if (is_wp_error($postId) || !$postId) {
            wp_send_json_error(['message' => 'خطا در ذخیره'], 500);
        }

        $adminEmail = get_option('admin_email');
        if ($adminEmail) {
            wp_mail($adminEmail, "پیام جدید فرم $form — erfansanat.com", $body);
        }

        wp_send_json_success(['message' => __('پیام شما با موفقیت ثبت شد.', 'erfansanat')]);
    }
}
