<?php
/** Contact and consultation form validation, storage and notification. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Forms;

use ErfanSanat\Theme\Contracts\ServiceInterface;
use WP_Error;

final class ContactForm implements ServiceInterface
{
    public function register(): void
    {
        add_filter('erfan_sanat_ajax_routes', [$this, 'registerRoute']);
    }

    /** @param mixed $routes @return array<string,callable> */
    public function registerRoute(mixed $routes): array
    {
        $routes = is_array($routes) ? $routes : [];
        $routes['contact_submission'] = [$this, 'handle'];
        return $routes;
    }

    /** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
    public function submit(array $input): array|WP_Error
    {
        $fields = $this->fields($input);
        if (is_wp_error($fields)) return $fields;
        $id = (new FormManager())->saveSubmission('contact', $fields, $input);
        if (is_wp_error($id)) return $id;
        $this->notify($fields);
        return ['submission_id' => $id, 'message' => 'درخواست شما با موفقیت ثبت شد. کارشناسان ما با شما تماس می‌گیرند.'];
    }

    /** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
    public function handle(array $input): array|WP_Error
    {
        $result = $this->submit($input);
        if (is_wp_error($result)) return $result;
        return ['data' => $result, 'html' => '', 'count' => 1];
    }

    /** @param array<string,mixed> $input @return array<string,string>|WP_Error */
    private function fields(array $input): array|WP_Error
    {
        $clean = (new FormManager())->sanitizeFields($input);
        $name = trim($clean['name'] ?? '');
        $phone = preg_replace('/[^0-9+]/', '', $clean['phone'] ?? '') ?: '';
        $message = trim($clean['message'] ?? '');
        $email = trim($clean['email'] ?? '');
        if ((function_exists('mb_strlen') ? mb_strlen($name) : strlen($name)) < 2) return new WP_Error('contact_name_required', 'نام را به‌درستی وارد کنید.');
        if (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) return new WP_Error('contact_phone_invalid', 'شماره تماس معتبر وارد کنید.');
        if ($message === '' || (function_exists('mb_strlen') ? mb_strlen($message) : strlen($message)) > 5000) return new WP_Error('contact_message_invalid', 'شرح درخواست را بررسی کنید.');
        if ($email !== '' && !is_email($email)) return new WP_Error('contact_email_invalid', 'ایمیل معتبر وارد کنید.');
        $clean['phone'] = $phone;
        $clean['name'] = $name;
        $clean['message'] = $message;
        return $clean;
    }

    /** @param array<string,string> $fields */
    private function notify(array $fields): void
    {
        $to = sanitize_email((string) get_option('admin_email'));
        if ($to === '') return;
        $body = "نام: {$fields['name']}\nتلفن: {$fields['phone']}\nایمیل: " . ($fields['email'] ?? '') . "\n\n{$fields['message']}";
        wp_mail($to, 'درخواست تماس از وب‌سایت عرفان صنعت', $body);
    }
}
