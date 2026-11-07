<?php
/**
 * Capability-bound administrative inbox for public website submissions.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Forms\Submission;

defined('ABSPATH') || exit;

/** Provides a private, status-managed forms inbox and notification settings. */
final class Forms
{
    private const PAGE_SLUG = 'ese-form-inbox';

    /** Attach the inbox screen and its protected post handlers. */
    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'register_menu']);
        add_action('admin_post_ese_update_form_status', [self::class, 'handle_status_update']);
        add_action('admin_post_ese_save_form_settings', [self::class, 'handle_settings_save']);
        add_action('admin_post_ese_retry_form_notification', [self::class, 'handle_notification_retry']);
    }

    /** Register a single top-level inbox menu visible only to administrators. */
    public static function register_menu(): void
    {
        add_menu_page(
            __('درخواست‌های سایت', 'erfansanat-theme'),
            __('درخواست‌های سایت', 'erfansanat-theme'),
            'manage_options',
            self::PAGE_SLUG,
            [self::class, 'render_inbox_page'],
            'dashicons-email-alt',
            58
        );
    }

    /** Render the settings and filtered/paginated inbox, escaping every stored value. */
    public static function render_inbox_page(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('شما اجازهٔ مشاهدهٔ این صفحه را ندارید.', 'erfansanat-theme'), '', ['response' => 403]);
        }
        $settings = get_option('ese_form_settings', []);
        $settings = is_array($settings) ? $settings : [];
        $rawRecipient = !empty($settings['notification_email']) ? $settings['notification_email'] : get_option('admin_email');
        $recipient = is_scalar($rawRecipient) ? (string) $rawRecipient : '';
        $enabled = !array_key_exists('notifications_enabled', $settings) || !empty($settings['notifications_enabled']);
        $retentionDays = Submission::retention_days();
        $rawNotice = isset($_GET['notice']) ? wp_unslash($_GET['notice']) : '';
        $notice = is_scalar($rawNotice) ? sanitize_key((string) $rawNotice) : '';
        $statusLabels = self::status_labels();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('درخواست‌های سایت', 'erfansanat-theme'); ?></h1>
            <?php if ('updated' === $notice) : ?><div class="notice notice-success is-dismissible"><p><?php echo esc_html__('تغییرات ذخیره شد.', 'erfansanat-theme'); ?></p></div><?php endif; ?>
            <?php if ('settings_error' === $notice) : ?><div class="notice notice-error"><p><?php echo esc_html__('نشانی ایمیل یا دورهٔ نگهداری معتبر نیست؛ تنظیمات قبلی حفظ شد.', 'erfansanat-theme'); ?></p></div><?php endif; ?>
            <?php if ('update_error' === $notice) : ?><div class="notice notice-error"><p><?php echo esc_html__('به‌روزرسانی وضعیت درخواست انجام نشد.', 'erfansanat-theme'); ?></p></div><?php endif; ?>
            <?php if ('notification_sent' === $notice) : ?><div class="notice notice-success is-dismissible"><p><?php echo esc_html__('اعلان ایمیلی با موفقیت دوباره ارسال شد.', 'erfansanat-theme'); ?></p></div><?php endif; ?>
            <?php if ('notification_error' === $notice) : ?><div class="notice notice-error"><p><?php echo esc_html__('ارسال دوبارهٔ اعلان انجام نشد؛ نشانی ایمیل و تنظیمات ارسال را بررسی کنید.', 'erfansanat-theme'); ?></p></div><?php endif; ?>
            <details class="postbox" style="max-width:980px;margin:18px 0;padding:12px 16px">
                <summary style="cursor:pointer;font-weight:600"><?php echo esc_html__('تنظیم اعلان ایمیلی', 'erfansanat-theme'); ?></summary>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:12px">
                    <input type="hidden" name="action" value="ese_save_form_settings" />
                    <?php wp_nonce_field('ese_save_form_settings'); ?>
                    <p><label for="ese-notification-email"><?php echo esc_html__('گیرندهٔ اعلان', 'erfansanat-theme'); ?></label><br /><input id="ese-notification-email" type="email" name="notification_email" class="regular-text" value="<?php echo esc_attr($recipient); ?>" autocomplete="email" /></p>
                    <p><label><input type="checkbox" name="notifications_enabled" value="1" <?php checked($enabled); ?> /> <?php echo esc_html__('ارسال اعلان پس از ثبت درخواست', 'erfansanat-theme'); ?></label></p>
                    <p><label for="ese-submission-retention"><strong><?php echo esc_html__('مدت نگهداری درخواست‌ها', 'erfansanat-theme'); ?></strong></label><br />
                        <select id="ese-submission-retention" name="submission_retention_days" aria-describedby="ese-submission-retention-help">
                            <?php foreach ([0 => __('حذف خودکار غیرفعال', 'erfansanat-theme'), 90 => __('۹۰ روز', 'erfansanat-theme'), 180 => __('۱۸۰ روز', 'erfansanat-theme'), 365 => __('۳۶۵ روز', 'erfansanat-theme')] as $days => $label) : ?>
                                <option value="<?php echo esc_attr((string) $days); ?>" <?php selected($retentionDays, $days); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p id="ese-submission-retention-help" class="description"><?php echo esc_html__('در صورت فعال‌سازی، درخواست‌های خصوصی پس از این مدت به‌طور دائمی حذف می‌شوند. مقدار پیش‌فرض حذف خودکار را غیرفعال می‌کند؛ پیش از انتخاب، سیاست نگهداری و الزامات قانونی سایت را بررسی کنید.', 'erfansanat-theme'); ?></p>
                    <?php submit_button(__('ذخیره تنظیمات', 'erfansanat-theme'), 'secondary', 'submit', false); ?>
                </form>
            </details>

            <?php
            $detailId = isset($_GET['submission']) ? absint($_GET['submission']) : 0;
            if ($detailId) {
                self::render_submission_detail($detailId);
            } else {
                self::render_submission_list($statusLabels);
            }
            ?>
        </div>
        <?php
    }

    /** Handle a status update after an administrator capability and per-record nonce check. */
    public static function handle_status_update(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('درخواست نامعتبر است.', 'erfansanat-theme'), '', ['response' => 403]);
        }
        $postId = isset($_POST['submission_id']) ? absint($_POST['submission_id']) : 0;
        check_admin_referer('ese_update_form_status_' . $postId);
        $rawStatus = isset($_POST['form_status']) ? wp_unslash($_POST['form_status']) : '';
        $status = is_scalar($rawStatus) ? sanitize_key((string) $rawStatus) : '';
        if ($postId && Submission::set_status($postId, $status)) {
            self::redirect('updated');
        }
        self::redirect('update_error');
    }

    /** Save notification settings using a strict administrator-only allowlist. */
    public static function handle_settings_save(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('درخواست نامعتبر است.', 'erfansanat-theme'), '', ['response' => 403]);
        }
        check_admin_referer('ese_save_form_settings');
        $rawEmail = isset($_POST['notification_email']) ? wp_unslash($_POST['notification_email']) : '';
        if (!is_scalar($rawEmail)) {
            self::redirect('settings_error');
        }
        $email = sanitize_email((string) $rawEmail);
        if ('' !== $email && !is_email($email)) {
            self::redirect('settings_error');
        }
        $rawEnabled = isset($_POST['notifications_enabled']) ? wp_unslash($_POST['notifications_enabled']) : '';
        $rawRetentionDays = isset($_POST['submission_retention_days']) ? wp_unslash($_POST['submission_retention_days']) : '0';
        if (!is_scalar($rawRetentionDays) || !in_array((string) $rawRetentionDays, ['0', '90', '180', '365'], true)) {
            self::redirect('settings_error');
        }
        update_option('ese_form_settings', [
            'notification_email' => $email,
            'notifications_enabled' => is_scalar($rawEnabled) && '1' === (string) $rawEnabled,
            'submission_retention_days' => (int) $rawRetentionDays,
        ], false);
        Submission::sync_retention_schedule();
        self::redirect('updated');
    }

    /** Retry a failed form notification using an administrator-only POST and per-record nonce. */
    public static function handle_notification_retry(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('درخواست نامعتبر است.', 'erfansanat-theme'), '', ['response' => 403]);
        }
        if ('POST' !== strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? ''))) {
            wp_die(esc_html__('روش ارسال درخواست معتبر نیست.', 'erfansanat-theme'), '', ['response' => 405]);
        }
        $postId = isset($_POST['submission_id']) ? absint($_POST['submission_id']) : 0;
        check_admin_referer('ese_retry_form_notification_' . $postId);
        self::redirect($postId && Submission::retry_notification($postId) ? 'notification_sent' : 'notification_error');
    }

    /** Render one submission's details and status form. */
    public static function render_submission_detail(int $postId): void
    {
        $post = get_post($postId);
        if (!$post instanceof \WP_Post || Submission::POST_TYPE !== $post->post_type || 'private' !== $post->post_status) {
            echo '<div class="notice notice-error"><p>' . esc_html__('درخواست موردنظر پیدا نشد.', 'erfansanat-theme') . '</p></div>';
            return;
        }
        $payload = get_post_meta($postId, '_ese_form_payload', true);
        $payload = is_array($payload) ? $payload : [];
        $type = (string) get_post_meta($postId, '_ese_form_type', true);
        $status = (string) get_post_meta($postId, '_ese_form_status', true);
        $notification = (string) get_post_meta($postId, '_ese_notification_status', true);
        $notificationLabels = [
            'sent' => __('ارسال شد', 'erfansanat-theme'),
            'failed' => __('ارسال ناموفق', 'erfansanat-theme'),
            'disabled' => __('غیرفعال', 'erfansanat-theme'),
            'no_recipient' => __('گیرنده تعیین نشده', 'erfansanat-theme'),
            'not_sent' => __('ارسال نشده', 'erfansanat-theme'),
        ];
        $labels = self::field_labels();
        ?>
        <p><a href="<?php echo esc_url(admin_url('admin.php?page=' . self::PAGE_SLUG)); ?>">← <?php echo esc_html__('بازگشت به صندوق ورودی', 'erfansanat-theme'); ?></a></p>
        <h2><?php echo esc_html(get_the_title($postId)); ?></h2>
        <p><?php echo esc_html(sprintf(__('نوع: %1$s | وضعیت اعلان: %2$s', 'erfansanat-theme'), Submission::schemas()[$type]['label'] ?? $type, $notificationLabels[$notification] ?? __('نامشخص', 'erfansanat-theme'))); ?></p>
        <table class="widefat striped" style="max-width:980px"><tbody>
        <?php foreach ($payload as $key => $value) : if (!is_scalar($value)) { continue; } ?>
            <tr><th style="width:200px"><?php echo esc_html($labels[$key] ?? sanitize_text_field((string) $key)); ?></th><td><?php echo nl2br(esc_html((string) $value)); ?></td></tr>
        <?php endforeach; ?>
        </tbody></table>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:980px;margin-top:18px">
            <input type="hidden" name="action" value="ese_update_form_status" />
            <input type="hidden" name="submission_id" value="<?php echo esc_attr((string) $postId); ?>" />
            <?php wp_nonce_field('ese_update_form_status_' . $postId); ?>
            <label for="ese-form-status"><strong><?php echo esc_html__('وضعیت پیگیری', 'erfansanat-theme'); ?></strong></label>
            <select id="ese-form-status" name="form_status">
                <?php foreach (self::status_labels() as $key => $label) : ?><option value="<?php echo esc_attr($key); ?>" <?php selected($status, $key); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?>
            </select>
            <?php submit_button(__('به‌روزرسانی وضعیت', 'erfansanat-theme'), 'primary', 'submit', false); ?>
        </form>
        <?php if (in_array($notification, ['failed', 'no_recipient', 'not_sent'], true)) : ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:980px;margin-top:10px">
                <input type="hidden" name="action" value="ese_retry_form_notification" />
                <input type="hidden" name="submission_id" value="<?php echo esc_attr((string) $postId); ?>" />
                <?php wp_nonce_field('ese_retry_form_notification_' . $postId); ?>
                <?php submit_button(__('تلاش دوباره برای ارسال اعلان', 'erfansanat-theme'), 'secondary', 'submit', false); ?>
            </form>
        <?php endif; ?>
        <?php
    }

    /** Human-readable inbox status names. */
    public static function status_labels(): array
    {
        return [
            'new' => __('جدید', 'erfansanat-theme'),
            'in_progress' => __('در حال پیگیری', 'erfansanat-theme'),
            'completed' => __('پاسخ داده شد', 'erfansanat-theme'),
            'spam' => __('هرزنامه', 'erfansanat-theme'),
        ];
    }

    /** Render the filtered inbox table. */
    private static function render_submission_list(array $statusLabels): void
    {
        $rawType = isset($_GET['type']) ? wp_unslash($_GET['type']) : '';
        $type = is_scalar($rawType) ? sanitize_key((string) $rawType) : '';
        if (!isset(Submission::schemas()[$type])) {
            $type = '';
        }
        $rawStatus = isset($_GET['form_status']) ? wp_unslash($_GET['form_status']) : '';
        $status = is_scalar($rawStatus) ? sanitize_key((string) $rawStatus) : '';
        if (!isset($statusLabels[$status])) {
            $status = '';
        }
        $page = max(1, isset($_GET['paged']) ? absint($_GET['paged']) : 1);
        $queryArgs = [
            'post_type' => Submission::POST_TYPE,
            'post_status' => 'private',
            'posts_per_page' => 20,
            'paged' => $page,
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => false,
        ];
        $metaQuery = ['relation' => 'AND'];
        if ($type) {
            $metaQuery[] = ['key' => '_ese_form_type', 'value' => $type];
        }
        if ($status) {
            $metaQuery[] = ['key' => '_ese_form_status', 'value' => $status];
        }
        if (count($metaQuery) > 1) {
            $queryArgs['meta_query'] = $metaQuery;
        }
        $submissions = new \WP_Query($queryArgs);
        ?>
        <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>" style="margin:18px 0">
            <input type="hidden" name="page" value="<?php echo esc_attr(self::PAGE_SLUG); ?>" />
            <label for="ese-form-type"><?php echo esc_html__('نوع فرم', 'erfansanat-theme'); ?></label>
            <select id="ese-form-type" name="type"><option value=""><?php echo esc_html__('همهٔ درخواست‌ها', 'erfansanat-theme'); ?></option><?php foreach (Submission::schemas() as $key => $schema) : ?><option value="<?php echo esc_attr($key); ?>" <?php selected($type, $key); ?>><?php echo esc_html($schema['label']); ?></option><?php endforeach; ?></select>
            <label for="ese-form-filter-status"><?php echo esc_html__('وضعیت', 'erfansanat-theme'); ?></label>
            <select id="ese-form-filter-status" name="form_status"><option value=""><?php echo esc_html__('همهٔ وضعیت‌ها', 'erfansanat-theme'); ?></option><?php foreach ($statusLabels as $key => $label) : ?><option value="<?php echo esc_attr($key); ?>" <?php selected($status, $key); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select>
            <?php submit_button(__('فیلتر', 'erfansanat-theme'), 'secondary', '', false); ?>
        </form>
        <table class="widefat striped">
            <thead><tr><th><?php echo esc_html__('تاریخ', 'erfansanat-theme'); ?></th><th><?php echo esc_html__('نوع', 'erfansanat-theme'); ?></th><th><?php echo esc_html__('نام', 'erfansanat-theme'); ?></th><th><?php echo esc_html__('تلفن', 'erfansanat-theme'); ?></th><th><?php echo esc_html__('موضوع / محصول', 'erfansanat-theme'); ?></th><th><?php echo esc_html__('وضعیت', 'erfansanat-theme'); ?></th><th><?php echo esc_html__('جزئیات', 'erfansanat-theme'); ?></th></tr></thead>
            <tbody>
            <?php if ($submissions->have_posts()) : while ($submissions->have_posts()) : $submissions->the_post();
                $postId = get_the_ID();
                $payload = get_post_meta($postId, '_ese_form_payload', true);
                $payload = is_array($payload) ? $payload : [];
                $formType = (string) get_post_meta($postId, '_ese_form_type', true);
                $formStatus = (string) get_post_meta($postId, '_ese_form_status', true);
                $subject = $payload['product'] ?? $payload['position'] ?? $payload['message'] ?? '';
                if (is_string($subject) && function_exists('mb_substr')) { $subject = mb_substr($subject, 0, 80); }
                $detailUrl = add_query_arg(['page' => self::PAGE_SLUG, 'submission' => $postId], admin_url('admin.php'));
                ?>
                <tr>
                    <td><?php echo esc_html(get_the_date(get_option('date_format') . ' ' . get_option('time_format'), $postId)); ?></td>
                    <td><?php echo esc_html(Submission::schemas()[$formType]['label'] ?? $formType); ?></td>
                    <td><?php echo esc_html((string) ($payload['name'] ?? '')); ?></td>
                    <td><?php echo esc_html((string) ($payload['phone'] ?? '')); ?></td>
                    <td><?php echo esc_html(is_scalar($subject) ? (string) $subject : ''); ?></td>
                    <td><?php echo esc_html($statusLabels[$formStatus] ?? $statusLabels['new']); ?></td>
                    <td><a href="<?php echo esc_url($detailUrl); ?>"><?php echo esc_html__('مشاهده', 'erfansanat-theme'); ?></a></td>
                </tr>
            <?php endwhile; wp_reset_postdata(); else : ?><tr><td colspan="7"><?php echo esc_html__('درخواستی ثبت نشده است.', 'erfansanat-theme'); ?></td></tr><?php endif; ?>
            </tbody>
        </table>
        <?php if ($submissions->max_num_pages > 1) : ?>
            <div class="tablenav"><div class="tablenav-pages">
                <?php echo wp_kses_post(paginate_links([
                    'base' => add_query_arg(['page' => self::PAGE_SLUG, 'type' => $type, 'form_status' => $status, 'paged' => '%#%'], admin_url('admin.php')),
                    'format' => '', 'current' => $page, 'total' => (int) $submissions->max_num_pages,
                ])); ?>
            </div></div>
        <?php endif; ?>
        <?php
    }

    /** Labels for user-submitted detail fields. */
    private static function field_labels(): array
    {
        return [
            'name' => __('نام', 'erfansanat-theme'), 'phone' => __('تلفن', 'erfansanat-theme'),
            'email' => __('ایمیل', 'erfansanat-theme'), 'organization' => __('سازمان', 'erfansanat-theme'),
            'message' => __('پیام', 'erfansanat-theme'), 'city' => __('شهر', 'erfansanat-theme'),
            'product' => __('محصول', 'erfansanat-theme'), 'product_id' => __('شناسه محصول', 'erfansanat-theme'),
            'position' => __('عنوان شغلی', 'erfansanat-theme'), 'department' => __('واحد', 'erfansanat-theme'),
            'experience' => __('سابقه', 'erfansanat-theme'), 'resume_url' => __('پیوند رزومه', 'erfansanat-theme'),
            'rating' => __('امتیاز', 'erfansanat-theme'),
        ];
    }

    /** Redirect only to the known inbox route. */
    private static function redirect(string $notice): void
    {
        wp_safe_redirect(add_query_arg(['page' => self::PAGE_SLUG, 'notice' => sanitize_key($notice)], admin_url('admin.php')));
        exit;
    }
}
