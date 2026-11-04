<?php
/**
 * ذخیره‌سازی دیدگاه محصول — امتیاز (rating) و سازمان (es_review_org) در comment_meta
 * فرم میرور فیلد email ندارد؛ برای مهمان‌ها ایمیل placeholder تولید می‌شود تا هسته رد نکند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Woo;

final class Reviews {

    public static function init(): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        add_filter('preprocess_comment', [self::class, 'guest_email_fallback']);
        add_action('comment_post', [self::class, 'save_meta'], 10, 3);
    }

    /** مهمان بدون ایمیل → placeholder (فرم میرور فیلد ایمیل ندارد) */
    public static function guest_email_fallback(array $comment): array {
        $email = trim((string) ($comment['comment_author_email'] ?? ''));
        if ($email === '' && trim((string) ($comment['comment_author'] ?? '')) !== '') {
            $comment['comment_author_email'] = 'no-reply@' . (string) (wp_parse_url(home_url(), PHP_URL_HOST) ?: 'erfansanat.local');
        }
        return $comment;
    }

    /** ذخیره امتیاز و سازمان به‌عنوان متای دیدگاه */
    public static function save_meta(int $comment_id, int|string $approved, array $comment): void {
        unset($approved, $comment);
        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
        if ($rating >= 1 && $rating <= 5) {
            add_comment_meta($comment_id, 'rating', $rating, true);
        }
        $org = isset($_POST['es_review_org']) ? sanitize_text_field((string) wp_unslash((string) $_POST['es_review_org'])) : '';
        if ($org !== '') {
            add_comment_meta($comment_id, 'es_review_org', $org, true);
        }
    }
}
