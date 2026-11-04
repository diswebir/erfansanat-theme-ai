<?php
/** Shared security and persistence service for public theme forms. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Forms;

use WP_Error;

final class FormManager
{
    public const NONCE_ACTION = 'erfan_sanat_form';
    private const RATE_LIMIT = 3;
    private const RATE_WINDOW = 600;

    /** @param array<string,mixed> $request @return true|WP_Error */
    public function checkSubmission(array $request): true|WP_Error
    {
        $nonce = isset($request['form_nonce']) && is_scalar($request['form_nonce']) ? sanitize_text_field((string) $request['form_nonce']) : '';
        if ($nonce === '' || !wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            return new WP_Error('invalid_form_nonce', 'درخواست معتبر نیست. صفحه را تازه‌سازی کنید.');
        }
        $honeypot = $request['website'] ?? $request['company_website'] ?? '';
        if (is_scalar($honeypot) && trim((string) $honeypot) !== '') {
            return new WP_Error('spam_detected', 'درخواست قابل ثبت نیست.');
        }

        $key = $this->rateLimitKey();
        $count = (int) get_transient($key);
        if ($count >= self::RATE_LIMIT) {
            return new WP_Error('form_rate_limited', 'تعداد درخواست‌ها بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.');
        }
        set_transient($key, $count + 1, self::RATE_WINDOW);
        return true;
    }

    /** @param array<string,mixed> $request @param array<string,mixed> $fields @return int|WP_Error */
    public function saveSubmission(string $formType, array $fields, array $request = []): int|WP_Error
    {
        $checked = $this->checkSubmission($request);
        if (is_wp_error($checked)) return $checked;

        $formType = sanitize_key($formType);
        if ($formType === '') return new WP_Error('invalid_form_type', 'نوع فرم معتبر نیست.');
        $clean = $this->sanitizeFields($fields);
        $name = isset($clean['name']) && is_string($clean['name']) ? $clean['name'] : '';
        $message = isset($clean['message']) && is_string($clean['message']) ? $clean['message'] : '';
        $title = '[' . $formType . '] ' . ($name !== '' ? $name : current_time('mysql'));
        $postId = wp_insert_post([
            'post_type' => FormSubmissions::POST_TYPE,
            'post_status' => 'publish',
            'post_title' => sanitize_text_field($title),
            'post_content' => $message,
            'meta_input' => ['es_message_type' => $formType, 'es_message_read' => '0'],
        ], true);
        if (is_wp_error($postId)) return $postId;
        foreach ($clean as $key => $value) update_post_meta((int) $postId, $key, $value);
        return (int) $postId;
    }

    /** @param array<string,mixed> $fields @return array<string,string> */
    public function sanitizeFields(array $fields): array
    {
        $clean = [];
        foreach ($fields as $key => $value) {
            if (!is_string($key) || in_array($key, ['form_nonce', 'nonce', '_ajax_nonce', 'action', 'website', 'company_website'], true) || !is_scalar($value)) continue;
            $safeKey = sanitize_key($key);
            if ($safeKey === '') continue;
            $raw = (string) $value;
            if (str_contains($safeKey, 'email')) $clean[$safeKey] = sanitize_email($raw);
            elseif (in_array($safeKey, ['message', 'details', 'description', 'comment'], true)) $clean[$safeKey] = sanitize_textarea_field($raw);
            else $clean[$safeKey] = sanitize_text_field($raw);
        }
        return $clean;
    }

    private function rateLimitKey(): string
    {
        $ip = isset($_SERVER['REMOTE_ADDR']) && is_scalar($_SERVER['REMOTE_ADDR']) ? sanitize_text_field((string) $_SERVER['REMOTE_ADDR']) : 'unknown';
        return 'es_form_rate_' . substr(hash('sha256', $ip), 0, 40);
    }
}
