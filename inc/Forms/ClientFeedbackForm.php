<?php
/** Server-side client feedback validation and storage. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Forms;

use ErfanSanat\Theme\Contracts\ServiceInterface;
use WP_Error;

final class ClientFeedbackForm implements ServiceInterface
{
    public function register(): void
    {
        add_filter('erfan_sanat_ajax_routes', [$this, 'registerRoute']);
    }

    /** @param mixed $routes @return array<string,callable> */
    public function registerRoute(mixed $routes): array
    {
        $routes = is_array($routes) ? $routes : [];
        $routes['client_feedback'] = [$this, 'handle'];
        return $routes;
    }

    /** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
    public function submit(array $input): array|WP_Error
    {
        $manager = new FormManager();
        $fields = $manager->sanitizeFields($input);
        $name = trim($fields['name'] ?? '');
        $message = trim($fields['message'] ?? '');
        $rating = isset($fields['rating']) && ctype_digit($fields['rating']) ? (int) $fields['rating'] : 0;
        if (strlen($name) < 2) return new WP_Error('feedback_name_invalid', 'نام را به‌درستی وارد کنید.');
        if ($rating < 1 || $rating > 5) return new WP_Error('feedback_rating_invalid', 'امتیاز باید بین ۱ تا ۵ باشد.');
        if ($message === '' || (function_exists('mb_strlen') ? mb_strlen($message) : strlen($message)) > 3000) return new WP_Error('feedback_message_invalid', 'متن بازخورد را بررسی کنید.');
        $fields['name'] = $name;
        $fields['rating'] = (string) $rating;
        $fields['message'] = $message;
        $id = $manager->saveSubmission('client-feedback', $fields, $input);
        if (is_wp_error($id)) return $id;
        $to = sanitize_email((string) get_option('admin_email'));
        if ($to !== '') wp_mail($to, 'بازخورد مشتری از وب‌سایت', "نام: {$name}\nسازمان: " . ($fields['organization'] ?? '') . "\nامتیاز: {$rating}/5\n\n{$message}");
        return ['submission_id' => $id, 'message' => 'از ثبت بازخورد شما سپاسگزاریم.'];
    }

    /** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
    public function handle(array $input): array|WP_Error
    {
        $result = $this->submit($input);
        if (is_wp_error($result)) return $result;
        return ['data' => $result, 'html' => '', 'count' => 1];
    }
}
