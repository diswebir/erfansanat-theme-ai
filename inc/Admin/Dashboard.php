<?php
/**
 * Adds a concise, capability-aware overview to the WordPress dashboard.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

defined('ABSPATH') || exit;

/** Presents useful site status and safe shortcuts on the native dashboard. */
final class Dashboard
{
    private const WIDGET_ID = 'ese-admin-overview';
    private const STYLE_HANDLE = 'erfansanat-admin-dashboard';
    private const STYLE_FILE = 'assets/css/admin-dashboard.css';

    /** Register the dashboard overview and its page-scoped stylesheet. */
    public static function register(): void
    {
        add_action('wp_dashboard_setup', [self::class, 'register_widget']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets'], 20);
    }

    /** Register the overview widget. */
    public static function register_widget(): void
    {
        wp_add_dashboard_widget(
            self::WIDGET_ID,
            __('نمای کلی عرفان صنعت', 'erfansanat-theme'),
            [self::class, 'render_widget']
        );
    }

    /** Enqueue dashboard presentation only on the native Dashboard screen. */
    public static function enqueue_assets(string $hook): void
    {
        if ('index.php' !== $hook) {
            return;
        }

        $path = get_theme_file_path(self::STYLE_FILE);
        $url = get_theme_file_uri(self::STYLE_FILE);
        if (!is_file($path) || false === $url) {
            return;
        }

        $version = hash_file('sha256', $path);
        wp_enqueue_style(
            self::STYLE_HANDLE,
            $url,
            ['erfansanat-admin-vazirmatn'],
            false !== $version ? substr($version, 0, 12) : (string) filemtime($path)
        );
    }

    /** Render a localized, capability-aware dashboard overview. */
    public static function render_widget(): void
    {
        $user = wp_get_current_user();
        $name = is_object($user) ? trim((string) $user->display_name) : '';
        $stats = [];

        foreach ([
            'product' => [
                'capability' => 'edit_products',
                'label' => __('محصولات منتشرشده', 'erfansanat-theme'),
                'description' => __('مدیریت کاتالوگ فروشگاه', 'erfansanat-theme'),
                'icon' => 'dashicons-products',
            ],
            'projects' => [
                'capability' => 'edit_posts',
                'label' => __('پروژه‌های منتشرشده', 'erfansanat-theme'),
                'description' => __('نمونه‌کارهای ثبت‌شده در سایت', 'erfansanat-theme'),
                'icon' => 'dashicons-portfolio',
            ],
            'page' => [
                'capability' => 'edit_pages',
                'label' => __('برگه‌های منتشرشده', 'erfansanat-theme'),
                'description' => __('صفحه‌های اصلی وب‌سایت', 'erfansanat-theme'),
                'icon' => 'dashicons-admin-page',
            ],
        ] as $postType => $config) {
            $count = self::published_count($postType, $config['capability']);
            if (null !== $count) {
                $stats[] = [
                    'label' => $config['label'],
                    'description' => $config['description'],
                    'icon' => $config['icon'],
                    'count' => $count,
                ];
            }
        }

        if (current_user_can('manage_options') && post_type_exists('ese_submission')) {
            $counts = wp_count_posts('ese_submission');
            $stats[] = [
                'label' => __('درخواست‌های دریافتی', 'erfansanat-theme'),
                'description' => __('مشاهده و پیگیری صندوق درخواست‌ها', 'erfansanat-theme'),
                'icon' => 'dashicons-email-alt',
                'count' => is_object($counts) ? (int) ($counts->private ?? 0) : 0,
            ];
        }

        $actions = self::available_actions();
        ?>
        <section class="ese-dashboard-overview" dir="rtl" lang="fa">
            <header class="ese-dashboard-hero">
                <div class="ese-dashboard-hero-copy">
                    <p class="ese-dashboard-eyebrow"><?php echo esc_html__('مدیریت وب‌سایت', 'erfansanat-theme'); ?></p>
                    <h2><?php echo esc_html($name ? sprintf(__('خوش آمدید، %s', 'erfansanat-theme'), $name) : __('خوش آمدید', 'erfansanat-theme')); ?></h2>
                    <p><?php echo esc_html__('از اینجا وضعیت محتوای سایت را ببینید و به کارهای پرتکرار دسترسی پیدا کنید.', 'erfansanat-theme'); ?></p>
                </div>
                <span class="ese-dashboard-brand" aria-hidden="true">ES</span>
            </header>

            <?php if ($stats) : ?>
                <div class="ese-dashboard-stats" aria-label="<?php echo esc_attr__('آمار وب‌سایت', 'erfansanat-theme'); ?>">
                    <?php foreach ($stats as $stat) : ?>
                        <article class="ese-dashboard-stat">
                            <span class="ese-dashboard-stat-icon dashicons <?php echo esc_attr($stat['icon']); ?>" aria-hidden="true"></span>
                            <div>
                                <p><?php echo esc_html($stat['label']); ?></p>
                                <strong><?php echo esc_html(number_format_i18n($stat['count'])); ?></strong>
                                <small><?php echo esc_html($stat['description']); ?></small>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="ese-dashboard-actions">
                <div class="ese-dashboard-section-title">
                    <h3><?php echo esc_html__('دسترسی سریع', 'erfansanat-theme'); ?></h3>
                    <p><?php echo esc_html__('مسیرهای اصلی مدیریت سایت', 'erfansanat-theme'); ?></p>
                </div>
                <nav class="ese-dashboard-action-grid" aria-label="<?php echo esc_attr__('دسترسی‌های سریع مدیریت', 'erfansanat-theme'); ?>">
                    <?php foreach ($actions as $action) : ?>
                        <a class="ese-dashboard-action" href="<?php echo esc_url($action['url']); ?>">
                            <span class="dashicons <?php echo esc_attr($action['icon']); ?>" aria-hidden="true"></span>
                            <span><strong><?php echo esc_html($action['label']); ?></strong><small><?php echo esc_html($action['description']); ?></small></span>
                            <span class="ese-dashboard-action-arrow" aria-hidden="true">←</span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <footer class="ese-dashboard-footer">
                <span class="dashicons dashicons-info-outline" aria-hidden="true"></span>
                <p><?php echo esc_html__('برای تغییر محتوای صفحات، از بخش برگه‌ها یا ویرایش همان صفحه استفاده کنید. تغییرات ابتدا پیش‌نمایش و سپس منتشر شوند.', 'erfansanat-theme'); ?></p>
            </footer>
        </section>
        <?php
    }

    /** Return an authorized published-post count, or null when unavailable. */
    private static function published_count(string $postType, string $capability): ?int
    {
        if (!post_type_exists($postType) || !current_user_can($capability)) {
            return null;
        }
        $counts = wp_count_posts($postType);
        return is_object($counts) ? (int) ($counts->publish ?? 0) : 0;
    }

    /** Build only shortcuts the current user is authorized to follow. */
    private static function available_actions(): array
    {
        $actions = [[
            'label' => __('مشاهدهٔ وب‌سایت', 'erfansanat-theme'),
            'description' => __('باز کردن نسخهٔ عمومی سایت', 'erfansanat-theme'),
            'url' => home_url('/'),
            'icon' => 'dashicons-external',
        ]];
        if (current_user_can('manage_options')) {
            $actions[] = [
                'label' => __('تنظیمات قالب', 'erfansanat-theme'),
                'description' => __('هویت، سربرگ، پاورقی و ظاهر سایت', 'erfansanat-theme'),
                'url' => admin_url('admin.php?page=erfansanat-theme-options'),
                'icon' => 'dashicons-admin-customizer',
            ];
            $actions[] = [
                'label' => __('صندوق درخواست‌ها', 'erfansanat-theme'),
                'description' => __('پیگیری پیام‌ها و درخواست‌های مشتریان', 'erfansanat-theme'),
                'url' => admin_url('admin.php?page=ese-form-inbox'),
                'icon' => 'dashicons-email-alt',
            ];
        }
        if (current_user_can('edit_posts')) {
            $actions[] = [
                'label' => __('افزودن نوشته', 'erfansanat-theme'),
                'description' => __('انتشار خبر یا مقالهٔ تازه', 'erfansanat-theme'),
                'url' => admin_url('post-new.php'),
                'icon' => 'dashicons-edit-page',
            ];
        }
        if (current_user_can('edit_posts') && post_type_exists('projects')) {
            $actions[] = [
                'label' => __('ثبت پروژه', 'erfansanat-theme'),
                'description' => __('افزودن نمونه‌کار به آرشیو پروژه‌ها', 'erfansanat-theme'),
                'url' => admin_url('post-new.php?post_type=projects'),
                'icon' => 'dashicons-portfolio',
            ];
        }
        if (current_user_can('edit_products') && post_type_exists('product')) {
            $actions[] = [
                'label' => __('افزودن محصول', 'erfansanat-theme'),
                'description' => __('ثبت محصول در فروشگاه', 'erfansanat-theme'),
                'url' => admin_url('post-new.php?post_type=product'),
                'icon' => 'dashicons-products',
            ];
        }
        if (current_user_can('upload_files')) {
            $actions[] = [
                'label' => __('رسانه‌ها', 'erfansanat-theme'),
                'description' => __('مدیریت تصاویر و فایل‌های سایت', 'erfansanat-theme'),
                'url' => admin_url('upload.php'),
                'icon' => 'dashicons-format-image',
            ];
        }
        return $actions;
    }
}
