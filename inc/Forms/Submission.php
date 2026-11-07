<?php
/**
 * Public, nonce-protected submissions for contact, quote, recruitment, and client feedback forms.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Forms;

defined('ABSPATH') || exit;

/** Stores form submissions privately and sends optional notifications. */
final class Submission
{
    public const POST_TYPE = 'ese_submission';
    public const NONCE_ACTION = 'ese_submit_form';

    private const MAX_MESSAGE_LENGTH = 4000;
    private const RATE_LIMIT = 5;
    private const RATE_WINDOW = 600;

    /** Register storage and both public/authenticated submission endpoints. */
    public static function register(): void
    {
        add_action('init', [self::class, 'register_post_type']);
        add_action('init', [self::class, 'sync_retention_schedule'], 20);
        add_action('ese_purge_expired_submissions', [self::class, 'purge_expired_submissions']);
        add_action('switch_theme', [self::class, 'clear_retention_schedule']);
        add_action('wp_ajax_ese_submit_form', [self::class, 'dispatch']);
        add_action('wp_ajax_nopriv_ese_submit_form', [self::class, 'dispatch']);
        add_filter('wp_privacy_personal_data_exporters', [self::class, 'register_privacy_exporter']);
        add_filter('wp_privacy_personal_data_erasers', [self::class, 'register_privacy_eraser']);
    }

    /** Register a non-public post type used only as the inbox data store. */
    public static function register_post_type(): void
    {
        if (post_type_exists(self::POST_TYPE)) {
            return;
        }
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name' => __('درخواست‌های فرم', 'erfansanat-theme'),
                'singular_name' => __('درخواست فرم', 'erfansanat-theme'),
            ],
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => false,
            'show_in_rest' => false,
            'rewrite' => false,
            'query_var' => false,
            'supports' => ['title', 'editor'],
            'capability_type' => 'post',
            'map_meta_cap' => true,
        ]);
    }

    /** Dispatch the AJAX handler and return generic errors only. */
    public static function dispatch(): void
    {
        if ('POST' !== strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? ''))) {
            wp_send_json_error(['message' => __('ارسال فرم ناموفق بود. دوباره تلاش کنید.', 'erfansanat-theme')], 405);
        }
        $nonce = isset($_POST['form_nonce']) ? wp_unslash($_POST['form_nonce']) : '';
        if (!is_string($nonce) || false === wp_verify_nonce(sanitize_text_field($nonce), self::NONCE_ACTION)) {
            wp_send_json_error(['message' => __('نشست فرم منقضی شده است؛ صفحه را تازه‌سازی کنید.', 'erfansanat-theme')], 403);
        }

        $input = function_exists('wp_unslash') ? wp_unslash($_POST) : $_POST;
        $result = self::process($input);
        if (is_wp_error($result)) {
            $data = $result->get_error_data();
            $status = is_array($data) && isset($data['status']) ? (int) $data['status'] : 422;
            wp_send_json_error(['message' => __('ارسال فرم انجام نشد. اطلاعات را بررسی و دوباره تلاش کنید.', 'erfansanat-theme')], $status);
        }
        wp_send_json_success(['message' => __('درخواست شما با موفقیت ثبت شد. کارشناسان ما پس از بررسی با شما تماس می‌گیرند.', 'erfansanat-theme')]);
    }

    /** Validate and store one public submission. A filled honeypot is silently accepted. */
    public static function process(array $input)
    {
        $type = isset($input['form_type']) && is_scalar($input['form_type']) ? sanitize_key((string) $input['form_type']) : '';
        $schemas = self::schemas();
        if (!isset($schemas[$type])) {
            return new \WP_Error('ese_invalid_form', 'Invalid form type.', ['status' => 422]);
        }

        $honeypot = isset($input['website']) && is_scalar($input['website'])
            ? trim((string) $input['website'])
            : '';
        if ('' !== $honeypot) {
            return ['accepted' => true, 'stored' => false];
        }
        if (!self::allow_request()) {
            return new \WP_Error('ese_rate_limited', 'Rate limit reached.', ['status' => 429]);
        }

        $fields = self::sanitize_fields($type, $input);
        if (is_wp_error($fields)) {
            return $fields;
        }

        self::register_post_type();
        $typeLabel = $schemas[$type]['label'];
        $name = isset($fields['name']) ? $fields['name'] : __('بدون نام', 'erfansanat-theme');
        $postId = wp_insert_post([
            'post_type' => self::POST_TYPE,
            'post_status' => 'private',
            'post_title' => sanitize_text_field($typeLabel . ' — ' . $name),
            'post_content' => self::format_content($type, $fields),
            'post_author' => get_current_user_id(),
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ], true);
        if (is_wp_error($postId) || !$postId) {
            return new \WP_Error('ese_storage_failed', 'Submission could not be stored.', ['status' => 500]);
        }

        update_post_meta((int) $postId, '_ese_form_type', $type);
        update_post_meta((int) $postId, '_ese_form_status', 'new');
        update_post_meta((int) $postId, '_ese_form_payload', $fields);
        update_post_meta((int) $postId, '_ese_notification_status', 'not_sent');
        self::send_notification((int) $postId, $type, $typeLabel, $fields);

        return ['accepted' => true, 'stored' => true, 'id' => (int) $postId];
    }

    /** Public schema descriptions used by validation and the admin inbox. */
    public static function schemas(): array
    {
        return [
            'contact' => ['label' => __('تماس با ما', 'erfansanat-theme')],
            'quote' => ['label' => __('استعلام قیمت', 'erfansanat-theme')],
            'recruitment' => ['label' => __('درخواست استخدام', 'erfansanat-theme')],
            'client_feedback' => ['label' => __('بازخورد مشتری', 'erfansanat-theme')],
        ];
    }

    /** Validate and sanitize only the fields allowed for the selected form. */
    public static function sanitize_fields(string $type, array $input)
    {
        $name = self::text($input['name'] ?? '', 100);
        $phone = self::phone($input['phone'] ?? '');
        $email = self::email($input['email'] ?? '', false);
        if ('' === $name || self::length($name) < 2 || '' === $phone || is_wp_error($email)) {
            return new \WP_Error('ese_invalid_contact', 'Invalid contact details.', ['status' => 422]);
        }

        $fields = ['name' => $name, 'phone' => $phone];
        if ('' !== $email) {
            $fields['email'] = $email;
        }
        $organization = self::text($input['organization'] ?? '', 120);
        if ('' !== $organization) {
            $fields['organization'] = $organization;
        }

        if ('contact' === $type || 'quote' === $type) {
            $message = self::text($input['message'] ?? '', self::MAX_MESSAGE_LENGTH, true);
            if (self::length($message) < 8) {
                return new \WP_Error('ese_invalid_message', 'Message is too short.', ['status' => 422]);
            }
            $fields['message'] = $message;
            if ('quote' === $type) {
                $city = self::text($input['city'] ?? '', 100);
                $product = self::text($input['product'] ?? '', 160);
                $productId = absint($input['product_id'] ?? 0);
                if ($productId > 0) {
                    $post = get_post($productId);
                    if (!$post instanceof \WP_Post || 'product' !== $post->post_type || 'publish' !== $post->post_status) {
                        return new \WP_Error('ese_invalid_product', 'Invalid product.', ['status' => 422]);
                    }
                    $product = get_the_title($productId);
                    $fields['product_id'] = $productId;
                }
                foreach (['city' => $city, 'product' => $product] as $key => $value) {
                    if ('' !== $value) {
                        $fields[$key] = $value;
                    }
                }
            }
        } elseif ('recruitment' === $type) {
            $position = self::text($input['position'] ?? '', 160);
            $department = self::text($input['department'] ?? '', 100);
            $experience = self::text($input['experience'] ?? '', 60);
            $message = self::text($input['message'] ?? '', self::MAX_MESSAGE_LENGTH, true);
            $rawResumeUrl = $input['resume_url'] ?? '';
            $resumeUrl = self::http_url($rawResumeUrl);
            $resumeWasProvided = array_key_exists('resume_url', $input)
                && (!is_scalar($rawResumeUrl) || '' !== trim((string) $rawResumeUrl));
            if ('' === $position || self::length($message) < 20 || ($resumeWasProvided && '' === $resumeUrl)) {
                return new \WP_Error('ese_invalid_application', 'Invalid application.', ['status' => 422]);
            }
            $fields['position'] = $position;
            $fields['message'] = $message;
            foreach (['department' => $department, 'experience' => $experience, 'resume_url' => $resumeUrl] as $key => $value) {
                if ('' !== $value) {
                    $fields[$key] = $value;
                }
            }
        } elseif ('client_feedback' === $type) {
            $message = self::text($input['message'] ?? '', 2000, true);
            $rating = absint($input['rating'] ?? 0);
            $productId = absint($input['product_id'] ?? 0);
            $post = $productId > 0 ? get_post($productId) : null;
            if (!$post instanceof \WP_Post || 'product' !== $post->post_type || 'publish' !== $post->post_status || $rating < 1 || $rating > 5 || self::length($message) < 8) {
                return new \WP_Error('ese_invalid_feedback', 'Invalid feedback.', ['status' => 422]);
            }
            $fields['product_id'] = $productId;
            $fields['product'] = get_the_title($productId);
            $fields['rating'] = $rating;
            $fields['message'] = $message;
        } else {
            return new \WP_Error('ese_invalid_form', 'Invalid form type.', ['status' => 422]);
        }
        return $fields;
    }

    /** Return true when the request passes a short per-IP/per-type transient limit. */
    public static function allow_request(?string $ip = null, ?string $type = null): bool
    {
        $ip = $ip ?? (isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown');
        $postedType = $_POST['form_type'] ?? 'form';
        $type = $type ?? (is_scalar($postedType) ? sanitize_key((string) $postedType) : 'form');
        $key = 'ese_form_rate_' . substr(hash_hmac('sha256', $ip . '|' . $type, wp_salt('auth')), 0, 32);
        $count = (int) get_transient($key);
        if ($count >= self::RATE_LIMIT) {
            return false;
        }
        set_transient($key, $count + 1, self::RATE_WINDOW);
        return true;
    }

    /** Set an allowlisted admin inbox status. */
    public static function set_status(int $postId, string $status): bool
    {
        if (!in_array($status, ['new', 'in_progress', 'completed', 'spam'], true)) {
            return false;
        }
        $post = get_post($postId);
        if (!$post instanceof \WP_Post || self::POST_TYPE !== $post->post_type) {
            return false;
        }
        if ($status === (string) get_post_meta($postId, '_ese_form_status', true)) {
            return true;
        }
        return false !== update_post_meta($postId, '_ese_form_status', $status);
    }

    /** Register this inbox with the WordPress personal-data exporter. */
    public static function register_privacy_exporter(array $exporters): array
    {
        $exporters['erfansanat-theme-submissions'] = [
            'exporter_friendly_name' => __('درخواست‌های ثبت‌شده در سایت', 'erfansanat-theme'),
            'callback' => [self::class, 'export_personal_data'],
        ];
        return $exporters;
    }

    /** Register this inbox with the WordPress personal-data eraser. */
    public static function register_privacy_eraser(array $erasers): array
    {
        $erasers['erfansanat-theme-submissions'] = [
            'eraser_friendly_name' => __('درخواست‌های ثبت‌شده در سایت', 'erfansanat-theme'),
            'callback' => [self::class, 'erase_personal_data'],
        ];
        return $erasers;
    }

    /** Export submissions that match the requested email address. */
    public static function export_personal_data(string $emailAddress, int $page = 1): array
    {
        $email = sanitize_email($emailAddress);
        if (!is_email($email)) {
            return ['data' => [], 'done' => true];
        }
        $query = self::privacy_query($email, $page);
        $data = [];
        foreach ($query->posts as $post) {
            $payload = get_post_meta((int) $post->ID, '_ese_form_payload', true);
            if (!is_array($payload) || !isset($payload['email']) || !is_string($payload['email']) || 0 !== strcasecmp($email, $payload['email'])) {
                continue;
            }
            $itemData = [
                ['name' => __('نوع درخواست', 'erfansanat-theme'), 'value' => (string) get_post_meta((int) $post->ID, '_ese_form_type', true)],
                ['name' => __('تاریخ ثبت', 'erfansanat-theme'), 'value' => mysql2date('c', $post->post_date, false)],
            ];
            foreach ($payload as $key => $value) {
                if (is_scalar($value)) {
                    $itemData[] = [
                        'name' => self::privacy_field_labels()[$key] ?? sanitize_text_field((string) $key),
                        'value' => (string) $value,
                    ];
                }
            }
            $data[] = [
                'group_id' => 'erfansanat-theme-submissions',
                'group_label' => __('درخواست‌های ثبت‌شده در سایت', 'erfansanat-theme'),
                'item_id' => 'submission-' . (int) $post->ID,
                'data' => $itemData,
            ];
        }
        return ['data' => $data, 'done' => $query->max_num_pages <= max(1, $page)];
    }

    /** Delete submissions that match the requested email address. */
    public static function erase_personal_data(string $emailAddress, int $page = 1): array
    {
        $email = sanitize_email($emailAddress);
        if (!is_email($email)) {
            return ['items_removed' => false, 'items_retained' => false, 'messages' => [], 'done' => true];
        }
        $query = self::privacy_query($email, $page);
        $removed = false;
        $retained = false;
        foreach ($query->posts as $post) {
            $payload = get_post_meta((int) $post->ID, '_ese_form_payload', true);
            if (!is_array($payload) || !isset($payload['email']) || !is_string($payload['email']) || 0 !== strcasecmp($email, $payload['email'])) {
                continue;
            }
            if (wp_delete_post((int) $post->ID, true)) {
                $removed = true;
            } else {
                $retained = true;
            }
        }
        return [
            'items_removed' => $removed,
            'items_retained' => $retained,
            'messages' => [],
            'done' => $query->max_num_pages <= max(1, $page),
        ];
    }

    /** Return the administrator-selected retention window; zero means keep records until manually removed. */
    public static function retention_days(): int
    {
        $settings = get_option('ese_form_settings', []);
        $days = is_array($settings) && isset($settings['submission_retention_days'])
            ? (int) $settings['submission_retention_days']
            : 0;
        return in_array($days, [0, 90, 180, 365], true) ? $days : 0;
    }

    /** Keep the daily cleanup schedule in sync with the explicit opt-in retention setting. */
    public static function sync_retention_schedule(): void
    {
        if (0 === self::retention_days()) {
            self::clear_retention_schedule();
            return;
        }
        if (!wp_next_scheduled('ese_purge_expired_submissions')) {
            wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'ese_purge_expired_submissions');
        }
    }

    /** Remove scheduled cleanup when this theme is switched off or retention is disabled. */
    public static function clear_retention_schedule(): void
    {
        wp_clear_scheduled_hook('ese_purge_expired_submissions');
    }

    /** Permanently remove a bounded batch of private submissions older than the opted-in window. */
    public static function purge_expired_submissions(): int
    {
        $retentionDays = self::retention_days();
        if (0 === $retentionDays) {
            return 0;
        }
        $cutoff = current_datetime()->modify('-' . $retentionDays . ' days')->format('Y-m-d H:i:s');
        $query = new \WP_Query([
            'post_type' => self::POST_TYPE,
            'post_status' => 'private',
            'posts_per_page' => 200,
            'fields' => 'ids',
            'orderby' => 'date',
            'order' => 'ASC',
            'no_found_rows' => true,
            'date_query' => [[
                'before' => $cutoff,
                'inclusive' => true,
            ]],
        ]);
        $removed = 0;
        foreach ((array) $query->posts as $postId) {
            if (wp_delete_post((int) $postId, true)) {
                $removed++;
            }
        }
        return $removed;
    }

    /** Retry a failed mail notification after checking administrator permission and record integrity. */
    public static function retry_notification(int $postId): bool
    {
        if (!current_user_can('manage_options')) {
            return false;
        }
        $post = get_post($postId);
        $status = (string) get_post_meta($postId, '_ese_notification_status', true);
        $type = (string) get_post_meta($postId, '_ese_form_type', true);
        $payload = get_post_meta($postId, '_ese_form_payload', true);
        if (
            !$post instanceof \WP_Post
            || self::POST_TYPE !== $post->post_type
            || 'private' !== $post->post_status
            || !in_array($status, ['failed', 'no_recipient', 'not_sent'], true)
            || !isset(self::schemas()[$type])
            || !is_array($payload)
        ) {
            return false;
        }
        self::send_notification($postId, $type, self::schemas()[$type]['label'], $payload);
        return 'sent' === (string) get_post_meta($postId, '_ese_notification_status', true);
    }

    /** Locate candidate records; exact email matching is repeated after the serialized-meta LIKE query. */
    private static function privacy_query(string $email, int $page): \WP_Query
    {
        return new \WP_Query([
            'post_type' => self::POST_TYPE,
            'post_status' => 'private',
            'posts_per_page' => 100,
            'paged' => max(1, $page),
            'orderby' => 'ID',
            'order' => 'ASC',
            'no_found_rows' => false,
            'meta_query' => [[
                'key' => '_ese_form_payload',
                'value' => $email,
                'compare' => 'LIKE',
            ]],
        ]);
    }

    /** Labels for values returned through WordPress privacy tools. */
    private static function privacy_field_labels(): array
    {
        return [
            'name' => __('نام', 'erfansanat-theme'),
            'phone' => __('تلفن', 'erfansanat-theme'),
            'email' => __('ایمیل', 'erfansanat-theme'),
            'organization' => __('سازمان', 'erfansanat-theme'),
            'message' => __('پیام', 'erfansanat-theme'),
            'city' => __('شهر', 'erfansanat-theme'),
            'product' => __('محصول', 'erfansanat-theme'),
            'product_id' => __('شناسه محصول', 'erfansanat-theme'),
            'position' => __('عنوان شغلی', 'erfansanat-theme'),
            'department' => __('واحد', 'erfansanat-theme'),
            'experience' => __('سابقه', 'erfansanat-theme'),
            'resume_url' => __('پیوند رزومه', 'erfansanat-theme'),
            'rating' => __('امتیاز', 'erfansanat-theme'),
        ];
    }

    /** Format fields as readable plain text for the editor and notification email. */
    private static function format_content(string $type, array $fields): string
    {
        $labels = [
            'name' => __('نام', 'erfansanat-theme'), 'phone' => __('تلفن', 'erfansanat-theme'),
            'email' => __('ایمیل', 'erfansanat-theme'), 'organization' => __('سازمان', 'erfansanat-theme'),
            'message' => __('پیام', 'erfansanat-theme'), 'city' => __('شهر', 'erfansanat-theme'),
            'product' => __('محصول', 'erfansanat-theme'), 'position' => __('عنوان شغلی', 'erfansanat-theme'),
            'department' => __('واحد', 'erfansanat-theme'), 'experience' => __('سابقه', 'erfansanat-theme'),
            'resume_url' => __('پیوند رزومه', 'erfansanat-theme'), 'rating' => __('امتیاز', 'erfansanat-theme'),
        ];
        $lines = [self::schemas()[$type]['label']];
        foreach ($fields as $key => $value) {
            $label = $labels[$key] ?? $key;
            $lines[] = $label . ': ' . (is_scalar($value) ? (string) $value : '');
        }
        return implode("\n\n", $lines);
    }

    /** Send an optional notification; never return mail transport details to the visitor. */
    private static function send_notification(int $postId, string $type, string $typeLabel, array $fields): void
    {
        $settings = get_option('ese_form_settings', []);
        $enabled = is_array($settings) && array_key_exists('notifications_enabled', $settings)
            ? (bool) $settings['notifications_enabled']
            : true;
        if (!$enabled) {
            update_post_meta($postId, '_ese_notification_status', 'disabled');
            return;
        }
        $rawRecipient = is_array($settings) && array_key_exists('notification_email', $settings)
            ? $settings['notification_email']
            : get_option('admin_email');
        $recipient = is_scalar($rawRecipient) ? sanitize_email((string) $rawRecipient) : '';
        if (!is_email($recipient)) {
            update_post_meta($postId, '_ese_notification_status', 'no_recipient');
            return;
        }
        $subject = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES) . ' — ' . wp_strip_all_tags($typeLabel);
        $body = self::format_content($type, $fields);
        $sent = wp_mail($recipient, sanitize_text_field($subject), $body);
        update_post_meta($postId, '_ese_notification_status', $sent ? 'sent' : 'failed');
    }

    /** Normalize and validate a human name/short text field. */
    private static function text($value, int $limit, bool $multiline = false): string
    {
        if (!is_scalar($value)) {
            return '';
        }
        $text = $multiline ? sanitize_textarea_field((string) $value) : sanitize_text_field((string) $value);
        $text = trim($text);
        return function_exists('mb_substr') ? mb_substr($text, 0, $limit) : substr($text, 0, $limit);
    }

    /** Normalize Persian and Arabic digits before validating phone numbers. */
    private static function phone($value): string
    {
        $phone = self::text($value, 32);
        $phone = strtr($phone, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
        if (strlen(preg_replace('/\D+/', '', $phone)) < 7 || !preg_match('/^[0-9+().\-\s]{7,32}$/', $phone)) {
            return '';
        }
        return $phone;
    }

    /** Return a valid optional email or a WP_Error for malformed input. */
    private static function email($value, bool $required)
    {
        if (!is_scalar($value)) {
            return new \WP_Error('ese_invalid_email', 'Invalid email.', ['status' => 422]);
        }
        $raw = trim((string) $value);
        if ('' === $raw && !$required) {
            return '';
        }
        $email = sanitize_email($raw);
        return is_email($email) ? $email : new \WP_Error('ese_invalid_email', 'Invalid email.', ['status' => 422]);
    }

    /** Allow only plain HTTP(S) resume links. */
    private static function http_url($value): string
    {
        if (!is_scalar($value)) {
            return '';
        }
        $url = esc_url_raw(trim((string) $value), ['http', 'https']);
        $scheme = strtolower((string) wp_parse_url($url, PHP_URL_SCHEME));
        return in_array($scheme, ['http', 'https'], true) ? $url : '';
    }

    /** Count Unicode characters when mbstring is available. */
    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    }
}
