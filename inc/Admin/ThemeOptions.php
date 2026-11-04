<?php
/**
 * Secure, RTL theme options screen.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

defined('ABSPATH') || exit;

/**
 * Registers and renders the theme's global settings page.
 */
final class ThemeOptions
{
    public const OPTION_NAME = 'erfansanat_theme_options';
    public const NONCE_ACTION = 'erfansanat_save_theme_options';
    public const AJAX_ACTION = 'erfansanat_save_theme_options';

    /** @var string */
    private static $pageHook = '';

    /**
     * Attach the settings screen and secure AJAX endpoint.
     */
    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'register_menu']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
        add_action('wp_ajax_erfansanat_save_theme_options', [self::class, 'save_options']);
        add_action('wp_head', [self::class, 'render_css_variables'], 30);
        add_action('wp_head', [self::class, 'render_head_script'], 99);
        add_action('wp_body_open', [self::class, 'render_body_script'], 1);
        add_action('wp_footer', [self::class, 'render_footer_script'], 99);
        add_action('pre_get_posts', [self::class, 'apply_archive_defaults']);
        add_filter('loop_shop_per_page', [self::class, 'filter_products_per_page'], 20);
        add_filter('woocommerce_default_catalog_orderby', [self::class, 'filter_products_orderby'], 20);
    }

    /**
     * Add a top-level settings page for site administrators.
     */
    public static function register_menu(): void
    {
        self::$pageHook = (string) add_menu_page(
            __('تنظیمات عرفان صنعت', 'erfansanat-theme'),
            __('تنظیمات قالب', 'erfansanat-theme'),
            'manage_options',
            'erfansanat-theme-options',
            [self::class, 'render_page'],
            'dashicons-admin-customizer',
            58
        );
    }

    /**
     * Return the settings tabs and their field definitions.
     *
     * Field paths are the stable public keys consumed by es_option().
     *
     * @return array<string, array{label:string, sections:array<int, array{title:string, fields:array<int, array<string, mixed>>}>}>
     */
    public static function tabs(): array
    {
        return [
            'general' => [
                'label' => __('عمومی', 'erfansanat-theme'),
                'sections' => [
                    [
                        'title' => __('هویت سایت', 'erfansanat-theme'),
                        'fields' => [
                            [
                                'path' => 'branding.site_name',
                                'label' => __('نام سایت', 'erfansanat-theme'),
                                'type' => 'text',
                                'default' => 'عرفان صنعت اصفهان',
                                'description' => __('نامی که در سربرگ و بخش‌های عمومی سایت نمایش داده می‌شود.', 'erfansanat-theme'),
                            ],
                            [
                                'path' => 'branding.site_tagline',
                                'label' => __('شعار کوتاه', 'erfansanat-theme'),
                                'type' => 'text',
                                'default' => 'نورپردازی شهری و المان‌های نوری',
                            ],
                            [
                                'path' => 'branding.logo_url',
                                'label' => __('نشانی لوگو', 'erfansanat-theme'),
                                'type' => 'media',
                                'default' => function_exists('get_theme_file_uri') ? get_theme_file_uri('assets/images/ese-logo.svg') : '',
                                'description' => __('نشانی فایل لوگوی سایت را وارد یا از کتابخانه رسانه انتخاب کنید.', 'erfansanat-theme'),
                            ],
                        ],
                    ],
                ],
            ],
            'contact' => [
                'label' => __('اطلاعات تماس', 'erfansanat-theme'),
                'sections' => [
                    [
                        'title' => __('راه‌های ارتباطی', 'erfansanat-theme'),
                        'fields' => [
                            [
                                'path' => 'contact.phone',
                                'label' => __('تلفن اصلی', 'erfansanat-theme'),
                                'type' => 'text',
                                'default' => '031-91091011',
                            ],
                            [
                                'path' => 'contact.sales_phone',
                                'label' => __('تلفن فروش', 'erfansanat-theme'),
                                'type' => 'text',
                                'default' => '09137976915',
                            ],
                            [
                                'path' => 'contact.email',
                                'label' => __('ایمیل', 'erfansanat-theme'),
                                'type' => 'email',
                                'default' => 'info@erfansanat.com',
                            ],
                            [
                                'path' => 'contact.address',
                                'label' => __('نشانی کارخانه', 'erfansanat-theme'),
                                'type' => 'textarea',
                                'default' => 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹',
                                'description' => __('نشانی کامل را می‌توانید در چند خط وارد کنید.', 'erfansanat-theme'),
                            ],
                            self::option_field('contact.business_hours', __('ساعات پاسخگویی', 'erfansanat-theme'), 'text', 'شنبه تا پنجشنبه · ۷:۰۰ الی ۱۶:۳۰'),
                        ],
                    ],
                ],
            ],
            'header' => [
                'label' => __('سربرگ و مگامنو', 'erfansanat-theme'),
                'sections' => [
                    [
                        'title' => __('نوار بالایی و اطلاعیه', 'erfansanat-theme'),
                        'fields' => [
                            self::option_field('header.topbar_enabled', __('نمایش نوار بالایی', 'erfansanat-theme'), 'checkbox', true),
                            self::option_field('header.topbar_message', __('متن نوار بالایی', 'erfansanat-theme'), 'text', 'گواهینامه ISO 9001:2015 و کدهای رسمی سامانه ستاد و GS1'),
                            self::option_field('header.topbar_link_label', __('عنوان پیوند نوار', 'erfansanat-theme'), 'text', 'مجوزها و افتخارات'),
                            self::option_field('header.topbar_link_url', __('نشانی پیوند نوار', 'erfansanat-theme'), 'url', '/licenses-and-honors/'),
                            self::option_field('header.topbar_links', __('پیوندهای نوار (عنوان | نشانی، هر مورد در یک خط)', 'erfansanat-theme'), 'textarea', "مجوزها و افتخارات | /licenses-and-honors/\nگارانتی ۲۴ ماهه | /refund_returns/\nاستخدام | /recruitment/"),
                            self::option_field('header.announcement_enabled', __('نمایش اطلاعیه', 'erfansanat-theme'), 'checkbox', false),
                            self::option_field('header.announcement_text', __('متن اطلاعیه', 'erfansanat-theme'), 'text', ''),
                            self::option_field('header.announcement_url', __('پیوند اطلاعیه', 'erfansanat-theme'), 'url', ''),
                        ],
                    ],
                    [
                        'title' => __('منوی اصلی و مگامنو', 'erfansanat-theme'),
                        'fields' => [
                            self::option_field('header.mega_menu_title', __('عنوان گروه‌های مگامنو', 'erfansanat-theme'), 'text', 'دسته‌بندی تولیدات (۱۵ گروه)'),
                            self::option_field('header.mega_menu_description', __('توضیح مگامنو', 'erfansanat-theme'), 'text', 'مشاهده محصولات تخصصی نورپردازی شهری و صنعتی'),
                            self::option_field('header.mega_menu_cta_label', __('عنوان پیوند همه محصولات', 'erfansanat-theme'), 'text', 'مشاهده همه محصولات'),
                            self::option_field('header.mega_menu_cta_url', __('نشانی پیوند همه محصولات', 'erfansanat-theme'), 'url', '/shop/'),
                            self::option_field('header.sticky', __('ثابت ماندن سربرگ هنگام پیمایش', 'erfansanat-theme'), 'checkbox', true),
                        ],
                    ],
                ],
            ],
            'footer' => [
                'label' => __('پاورقی', 'erfansanat-theme'),
                'sections' => [
                    [
                        'title' => __('معرفی و پیوندها', 'erfansanat-theme'),
                        'fields' => [
                            self::option_field('footer.company_name', __('نام شرکت در پاورقی', 'erfansanat-theme'), 'text', 'شرکت دانش‌بنیان عرفان صنعت اصفهان'),
                            self::option_field('footer.description', __('معرفی شرکت', 'erfansanat-theme'), 'textarea', 'طراح و تولیدکننده تجهیزات نورپردازی پیشرفته، المان‌های نوری تزئینی، درختان نوری هوشمند، خورشیدنماها، ریسه‌های LED (بلوطی، فندقی، سوزنی IP68) و پوینت لایت پیکسل با ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش.'),
                            self::option_field('footer.badges', __('نشان‌ها (هر نشان در یک خط)', 'erfansanat-theme'), 'textarea', "ISO 9001:2015\nIP67 / IP68\nکد بین‌المللی GS1\nعرضه در سامانه ستاد"),
                            self::option_field('footer.quick_links', __('پیوندهای دسترسی سریع (عنوان | نشانی، هر مورد در یک خط)', 'erfansanat-theme'), 'textarea', "فروشگاه محصولات | /shop/\nپروژه‌های اجراشده | /projects/\nخدمات نورپردازی | /services/\nمجوزها و افتخارات | /licenses-and-honors/\nسوالات متداول | /faq/\nشرایط گارانتی محصولات | /refund_returns/\nفرصت‌های شغلی و استخدام | /recruitment/\nاخبار و مقالات | /blog/"),
                            self::option_field('footer.copyright', __('متن حق نشر', 'erfansanat-theme'), 'text', 'تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.'),
                            self::option_field('footer.show_back_to_top', __('نمایش دکمهٔ بازگشت به بالا', 'erfansanat-theme'), 'checkbox', true),
                        ],
                    ],
                ],
            ],
            'social' => [
                'label' => __('شبکه‌های اجتماعی', 'erfansanat-theme'),
                'sections' => [
                    [
                        'title' => __('نشانی شبکه‌های اجتماعی', 'erfansanat-theme'),
                        'fields' => [
                            self::option_field('social.instagram', __('اینستاگرام', 'erfansanat-theme'), 'url', 'https://instagram.com/erfan_sanat_esfahan'),
                            self::option_field('social.telegram', __('تلگرام', 'erfansanat-theme'), 'url', 'https://t.me/erfansanat'),
                            self::option_field('social.linkedin', __('لینکدین', 'erfansanat-theme'), 'url', 'https://linkedin.com/company/erfansanat'),
                        ],
                    ],
                ],
            ],
            'appearance' => [
                'label' => __('رنگ‌ها', 'erfansanat-theme'),
                'sections' => [
                    [
                        'title' => __('رنگ‌های سراسری قالب', 'erfansanat-theme'),
                        'fields' => [
                            self::option_field('appearance.primary_color', __('رنگ اصلی', 'erfansanat-theme'), 'color', '#047857'),
                            self::option_field('appearance.secondary_color', __('رنگ مکمل', 'erfansanat-theme'), 'color', '#0f766e'),
                            self::option_field('appearance.accent_color', __('رنگ تأکیدی', 'erfansanat-theme'), 'color', '#d97706'),
                            self::option_field('appearance.text_color', __('رنگ متن', 'erfansanat-theme'), 'color', '#0f172a'),
                            self::option_field('appearance.surface_color', __('رنگ پس‌زمینهٔ روشن', 'erfansanat-theme'), 'color', '#f8fafc'),
                        ],
                    ],
                ],
            ],
            'scripts' => [
                'label' => __('کدهای سفارشی', 'erfansanat-theme'),
                'sections' => [
                    [
                        'title' => __('کدهای JavaScript سراسری', 'erfansanat-theme'),
                        'fields' => [
                            self::option_field('scripts.head', __('کد سربرگ (JavaScript)', 'erfansanat-theme'), 'code', '', __('فقط کد JavaScript وارد کنید؛ برای ذخیره به مجوز unfiltered_html نیاز است.', 'erfansanat-theme')),
                            self::option_field('scripts.body', __('کد پس از شروع بدنه (JavaScript)', 'erfansanat-theme'), 'code', '', __('کد در نقطهٔ wp_body_open بارگذاری می‌شود.', 'erfansanat-theme')),
                            self::option_field('scripts.footer', __('کد پیش از بسته‌شدن بدنه (JavaScript)', 'erfansanat-theme'), 'code', '', __('کد در پاورقی صفحه بارگذاری می‌شود.', 'erfansanat-theme')),
                        ],
                    ],
                ],
            ],
            'archives' => [
                'label' => __('پیش‌فرض آرشیوها', 'erfansanat-theme'),
                'sections' => [
                    [
                        'title' => __('فروشگاه، پروژه‌ها و نوشته‌ها', 'erfansanat-theme'),
                        'fields' => [
                            self::option_field('archives.products_per_page', __('تعداد محصولات در هر صفحه', 'erfansanat-theme'), 'number', 9, '', ['min' => 1, 'max' => 48]),
                            self::option_field('archives.products_orderby', __('مرتب‌سازی پیش‌فرض محصولات', 'erfansanat-theme'), 'select', 'menu_order', '', ['choices' => ['menu_order' => 'ترتیب پیش‌فرض', 'date' => 'جدیدترین', 'title' => 'نام', 'popularity' => 'محبوبیت', 'rating' => 'امتیاز', 'price' => 'قیمت کم به زیاد', 'price-desc' => 'قیمت زیاد به کم']]),
                            self::option_field('archives.projects_per_page', __('تعداد پروژه‌ها در هر صفحه', 'erfansanat-theme'), 'number', 6, '', ['min' => 1, 'max' => 48]),
                            self::option_field('archives.posts_per_page', __('تعداد نوشته‌ها در هر صفحه', 'erfansanat-theme'), 'number', 4, '', ['min' => 1, 'max' => 48]),
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Return defaults in nested form, keyed by each field's public path.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        $defaults = [];
        foreach (self::all_fields() as $field) {
            self::write_path($defaults, $field['path'], $field['default'] ?? '');
        }

        return $defaults;
    }

    /**
     * Sanitize only known field paths, returning a nested options array.
     *
     * @param array<string, mixed> $payload Flat path-to-value payload from the admin form.
     * @return array<string, mixed>
     */
    public static function sanitize_payload(array $payload): array
    {
        $knownFields = [];
        foreach (self::all_fields() as $field) {
            $knownFields[$field['path']] = $field;
        }

        $clean = [];
        foreach ($payload as $path => $value) {
            if (!is_string($path) || !isset($knownFields[$path])) {
                continue;
            }

            $field = $knownFields[$path];
            $type = $field['type'] ?? 'text';
            if (is_array($value) || is_object($value)) {
                continue;
            }
            if ('code' === $type && !current_user_can('unfiltered_html')) {
                continue;
            }

            switch ($type) {
                case 'email':
                    $sanitized = sanitize_email((string) $value);
                    if (!is_email($sanitized)) {
                        $sanitized = '';
                    }
                    break;
                case 'textarea':
                    $sanitized = sanitize_textarea_field((string) $value);
                    break;
                case 'url':
                case 'media':
                    $sanitized = esc_url_raw((string) $value);
                    break;
                case 'number':
                    $sanitized = is_numeric($value) ? (float) $value : (float) ($field['default'] ?? 0);
                    if (isset($field['min'])) {
                        $sanitized = max((float) $field['min'], $sanitized);
                    }
                    if (isset($field['max'])) {
                        $sanitized = min((float) $field['max'], $sanitized);
                    }
                    $sanitized = floor($sanitized) === $sanitized ? (int) $sanitized : $sanitized;
                    break;
                case 'checkbox':
                    $sanitized = in_array($value, [true, 1, '1', 'true', 'on'], true);
                    break;
                case 'color':
                    $sanitized = sanitize_hex_color((string) $value);
                    if (!$sanitized) {
                        $sanitized = (string) ($field['default'] ?? '');
                    }
                    break;
                case 'select':
                    $choices = isset($field['choices']) && is_array($field['choices']) ? $field['choices'] : [];
                    $sanitized = array_key_exists((string) $value, $choices)
                        ? (string) $value
                        : (string) ($field['default'] ?? '');
                    break;
                case 'code':
                    $sanitized = trim(wp_check_invalid_utf8((string) $value));
                    if (strlen($sanitized) > 20000) {
                        $sanitized = substr($sanitized, 0, 20000);
                    }
                    break;
                default:
                    $sanitized = sanitize_text_field((string) $value);
                    break;
            }

            self::write_path($clean, $path, $sanitized);
        }

        return $clean;
    }

    /**
     * Verify the capability and nonce for a settings write.
     *
     * @param mixed $nonce Request nonce.
     */
    public static function is_authorized_request($nonce): bool
    {
        return current_user_can('manage_options')
            && is_string($nonce)
            && '' !== $nonce
            && false !== wp_verify_nonce($nonce, self::NONCE_ACTION);
    }

    /**
     * Process the AJAX write after enforcing capability and nonce checks.
     */
    public static function save_options(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('دسترسی کافی ندارید.', 'erfansanat-theme')], 403);
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (false === check_ajax_referer(self::NONCE_ACTION, 'nonce', false)) {
            wp_send_json_error(['message' => __('درخواست منقضی یا نامعتبر است.', 'erfansanat-theme')], 403);
        }

        $rawPayload = isset($_POST['options']) ? wp_unslash($_POST['options']) : '';
        if (!is_string($rawPayload) || strlen($rawPayload) > 65536) {
            wp_send_json_error(['message' => __('داده‌های تنظیمات معتبر نیستند.', 'erfansanat-theme')], 400);
        }

        $payload = json_decode($rawPayload, true);
        if (!is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => __('ساختار تنظیمات معتبر نیست.', 'erfansanat-theme')], 400);
        }

        $clean = self::sanitize_payload($payload);
        $existing = get_option(self::OPTION_NAME, []);
        $merged = array_replace_recursive(is_array($existing) ? $existing : [], $clean);
        update_option(self::OPTION_NAME, $merged, false);

        wp_send_json_success(
            [
                'message' => __('تنظیمات با موفقیت ذخیره شد.', 'erfansanat-theme'),
                'options' => $merged,
            ]
        );
    }

    /**
     * Render the RTL settings screen and its tab navigation.
     */
    public static function render_page(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('شما اجازهٔ دسترسی به این صفحه را ندارید.', 'erfansanat-theme'));
        }

        $tabs = self::tabs();
        $activeTab = isset($_GET['tab']) && is_string($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'general';
        if (!isset($tabs[$activeTab])) {
            $activeTab = 'general';
        }

        $settings = array_replace_recursive(
            self::defaults(),
            is_array(get_option(self::OPTION_NAME, [])) ? get_option(self::OPTION_NAME, []) : []
        );
        $pageUrl = admin_url('admin.php?page=erfansanat-theme-options');
        ?>
        <div class="wrap es-theme-options" dir="rtl">
            <header class="es-options-header">
                <div>
                    <p class="es-options-eyebrow"><?php echo esc_html__('مدیریت وب‌سایت', 'erfansanat-theme'); ?></p>
                    <h1><?php echo esc_html__('تنظیمات عرفان صنعت', 'erfansanat-theme'); ?></h1>
                    <p><?php echo esc_html__('اطلاعات عمومی و راه‌های ارتباطی وب‌سایت را از این بخش مدیریت کنید.', 'erfansanat-theme'); ?></p>
                </div>
                <span class="es-options-mark" aria-hidden="true">ES</span>
            </header>

            <nav class="nav-tab-wrapper es-options-tabs" aria-label="<?php echo esc_attr__('بخش‌های تنظیمات', 'erfansanat-theme'); ?>">
                <?php foreach ($tabs as $tabKey => $tab) : ?>
                    <a class="nav-tab<?php echo $activeTab === $tabKey ? ' nav-tab-active' : ''; ?>"
                       href="<?php echo esc_url(add_query_arg('tab', $tabKey, $pageUrl)); ?>"
                       <?php echo $activeTab === $tabKey ? 'aria-current="page"' : ''; ?>>
                        <?php echo esc_html($tab['label']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="es-options-notice" role="status" aria-live="polite" hidden></div>
            <form class="es-options-form" method="post" action="#">
                <?php foreach ($tabs[$activeTab]['sections'] as $section) : ?>
                    <section class="es-options-card">
                        <div class="es-options-card-heading">
                            <h2><?php echo esc_html($section['title']); ?></h2>
                        </div>
                        <div class="es-options-fields">
                            <?php foreach ($section['fields'] as $field) : ?>
                                <?php self::render_field($field, $settings); ?>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>

                <div class="es-options-actions">
                    <button type="submit" class="button button-primary es-options-save">
                        <?php echo esc_html__('ذخیرهٔ تنظیمات', 'erfansanat-theme'); ?>
                    </button>
                    <span class="es-options-saving" aria-live="polite"></span>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueue assets only on this theme settings page.
     *
     * @param string $hook Current admin page hook suffix.
     */
    public static function enqueue_assets(string $hook): void
    {
        $isOptionsPage = (self::$pageHook && self::$pageHook === $hook)
            || (isset($_GET['page']) && 'erfansanat-theme-options' === sanitize_key(wp_unslash($_GET['page'])));
        if (!$isOptionsPage) {
            return;
        }

        $cssPath = get_theme_file_path('assets/css/admin-theme-options.css');
        $jsPath = get_theme_file_path('assets/js/admin-theme-options.js');
        wp_enqueue_media();
        wp_enqueue_style(
            'erfansanat-admin-theme-options',
            get_theme_file_uri('assets/css/admin-theme-options.css'),
            [],
            is_file($cssPath) ? (string) filemtime($cssPath) : '1.0.0'
        );
        wp_enqueue_script(
            'erfansanat-admin-theme-options',
            get_theme_file_uri('assets/js/admin-theme-options.js'),
            [],
            is_file($jsPath) ? (string) filemtime($jsPath) : '1.0.0',
            true
        );
        wp_localize_script(
            'erfansanat-admin-theme-options',
            'EsThemeOptions',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'action' => self::AJAX_ACTION,
                'nonce' => wp_create_nonce(self::NONCE_ACTION),
                'messages' => [
                    'saved' => __('تنظیمات با موفقیت ذخیره شد.', 'erfansanat-theme'),
                    'error' => __('ذخیرهٔ تنظیمات انجام نشد. دوباره تلاش کنید.', 'erfansanat-theme'),
                    'saving' => __('در حال ذخیره…', 'erfansanat-theme'),
                ],
            ]
        );
    }

    /**
     * Output safe CSS variables consumed by future template parts and user CSS.
     */
    public static function render_css_variables(): void
    {
        if (!function_exists('es_option')) {
            return;
        }

        $colors = [
            '--ese-color-primary' => ['appearance.primary_color', '#047857'],
            '--ese-color-secondary' => ['appearance.secondary_color', '#0f766e'],
            '--ese-color-accent' => ['appearance.accent_color', '#d97706'],
            '--ese-color-text' => ['appearance.text_color', '#0f172a'],
            '--ese-color-surface' => ['appearance.surface_color', '#f8fafc'],
        ];
        $declarations = [];
        foreach ($colors as $property => [$path, $fallback]) {
            $value = sanitize_hex_color((string) es_option($path, $fallback));
            $declarations[] = $property . ':' . esc_attr($value ?: $fallback);
        }

        echo '<style id="erfansanat-theme-option-colors">:root{' . implode(';', $declarations) . '}</style>';
    }

    /** Output the trusted admin-configured script in wp_head. */
    public static function render_head_script(): void
    {
        self::render_custom_script('scripts.head', 'erfansanat-custom-head-script');
    }

    /** Output the trusted admin-configured script after wp_body_open. */
    public static function render_body_script(): void
    {
        self::render_custom_script('scripts.body', 'erfansanat-custom-body-script');
    }

    /** Output the trusted admin-configured script in wp_footer. */
    public static function render_footer_script(): void
    {
        self::render_custom_script('scripts.footer', 'erfansanat-custom-footer-script');
    }

    /**
     * Apply configured native archive pagination without changing unrelated queries.
     *
     * @param \WP_Query $query Current query.
     */
    public static function apply_archive_defaults(\WP_Query $query): void
    {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->is_post_type_archive('projects') || $query->is_tax(['project_type', 'city'])) {
            $query->set('posts_per_page', self::bounded_archive_count('archives.projects_per_page', 9));
            return;
        }

        if ($query->is_post_type_archive('product') || $query->is_tax('product_cat')) {
            return;
        }

        if ($query->is_home() || $query->is_archive()) {
            $query->set('posts_per_page', self::bounded_archive_count('archives.posts_per_page', 9));
        }
    }

    /**
     * Set WooCommerce product pagination from the global archive option.
     *
     * @param int $perPage WooCommerce default.
     * @return int
     */
    public static function filter_products_per_page(int $perPage): int
    {
        return self::bounded_archive_count('archives.products_per_page', $perPage > 0 ? $perPage : 12);
    }

    /**
     * Set a validated WooCommerce catalog ordering default.
     *
     * @param string $orderby WooCommerce default.
     * @return string
     */
    public static function filter_products_orderby(string $orderby): string
    {
        $allowed = ['menu_order', 'date', 'title', 'popularity', 'rating', 'price', 'price-desc'];
        $configured = function_exists('es_option') ? (string) es_option('archives.products_orderby', $orderby) : $orderby;

        return in_array($configured, $allowed, true) ? $configured : $orderby;
    }

    /**
     * Print a configured JavaScript fragment saved by an unfiltered_html-capable admin.
     *
     * @param string $path Option key.
     * @param string $id Unique script element id.
     */
    private static function render_custom_script(string $path, string $id): void
    {
        if (!function_exists('es_option') || !function_exists('wp_print_inline_script_tag')) {
            return;
        }

        $code = es_option($path, '');
        if (!is_string($code) || '' === trim($code)) {
            return;
        }

        wp_print_inline_script_tag($code, ['id' => $id]);
    }

    /**
     * Return a bounded positive archive size.
     *
     * @param string $path Option key.
     * @param int    $fallback Fallback count.
     */
    private static function bounded_archive_count(string $path, int $fallback): int
    {
        $value = function_exists('es_option') ? absint(es_option($path, $fallback)) : $fallback;

        return max(1, min(48, $value ?: $fallback));
    }

    /**
     * Create one compact schema field definition.
     *
     * @param string               $path Public dot-separated option key.
     * @param string               $label Visible label.
     * @param string               $type Input type.
     * @param mixed                $default Schema default.
     * @param string               $description Optional help text.
     * @param array<string, mixed> $extra Additional schema constraints.
     * @return array<string, mixed>
     */
    private static function option_field(string $path, string $label, string $type, $default, string $description = '', array $extra = []): array
    {
        $field = array_merge(
            [
                'path' => $path,
                'label' => $label,
                'type' => $type,
                'default' => $default,
            ],
            $extra
        );
        if ('' !== $description) {
            $field['description'] = $description;
        }

        return $field;
    }

    /**
     * Render one schema field.
     *
     * @param array<string, mixed> $field Field definition.
     * @param array<string, mixed> $settings Nested settings values.
     */
    private static function render_field(array $field, array $settings): void
    {
        $path = (string) $field['path'];
        $type = (string) ($field['type'] ?? 'text');
        $id = 'es-option-' . sanitize_html_class(str_replace('.', '-', $path));
        $value = self::read_path($settings, $path, $field['default'] ?? '');
        $descriptionId = $id . '-description';
        ?>
        <div class="es-option-field es-option-field-<?php echo esc_attr($type); ?>">
            <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($field['label'] ?? $path); ?></label>
            <?php if (in_array($type, ['textarea', 'code'], true)) : ?>
                <textarea id="<?php echo esc_attr($id); ?>" data-option-path="<?php echo esc_attr($path); ?>" rows="<?php echo 'code' === $type ? '8' : '4'; ?>" <?php echo 'code' === $type && !current_user_can('unfiltered_html') ? 'disabled' : ''; ?> <?php echo !empty($field['description']) ? 'aria-describedby="' . esc_attr($descriptionId) . '"' : ''; ?>><?php echo esc_textarea((string) $value); ?></textarea>
            <?php elseif ('select' === $type) : ?>
                <select id="<?php echo esc_attr($id); ?>" data-option-path="<?php echo esc_attr($path); ?>" <?php echo !empty($field['description']) ? 'aria-describedby="' . esc_attr($descriptionId) . '"' : ''; ?>>
                    <?php foreach (($field['choices'] ?? []) as $choiceValue => $choiceLabel) : ?>
                        <option value="<?php echo esc_attr((string) $choiceValue); ?>" <?php selected((string) $value, (string) $choiceValue); ?>><?php echo esc_html((string) $choiceLabel); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ('checkbox' === $type) : ?>
                <input id="<?php echo esc_attr($id); ?>" type="checkbox" value="1" data-option-path="<?php echo esc_attr($path); ?>" <?php checked((bool) $value); ?> <?php echo !empty($field['description']) ? 'aria-describedby="' . esc_attr($descriptionId) . '"' : ''; ?> />
            <?php elseif ('media' === $type) : ?>
                <div class="es-option-media-control">
                    <input id="<?php echo esc_attr($id); ?>" type="url" value="<?php echo esc_attr((string) $value); ?>" data-option-path="<?php echo esc_attr($path); ?>" <?php echo !empty($field['description']) ? 'aria-describedby="' . esc_attr($descriptionId) . '"' : ''; ?> />
                    <button type="button" class="button es-options-select-media" data-target="<?php echo esc_attr($id); ?>"><?php echo esc_html__('انتخاب از رسانه', 'erfansanat-theme'); ?></button>
                </div>
            <?php else : ?>
                <input id="<?php echo esc_attr($id); ?>" type="<?php echo esc_attr(in_array($type, ['text', 'email', 'url', 'number', 'color'], true) ? $type : 'text'); ?>" value="<?php echo esc_attr((string) $value); ?>" data-option-path="<?php echo esc_attr($path); ?>" <?php echo isset($field['min']) ? 'min="' . esc_attr((string) $field['min']) . '"' : ''; ?> <?php echo isset($field['max']) ? 'max="' . esc_attr((string) $field['max']) . '"' : ''; ?> <?php echo !empty($field['description']) ? 'aria-describedby="' . esc_attr($descriptionId) . '"' : ''; ?> />
            <?php endif; ?>
            <?php if (!empty($field['description'])) : ?>
                <p class="description" id="<?php echo esc_attr($descriptionId); ?>"><?php echo esc_html($field['description']); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Flatten all field definitions while preserving their paths.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function all_fields(): array
    {
        $fields = [];
        foreach (self::tabs() as $tab) {
            foreach ($tab['sections'] as $section) {
                foreach ($section['fields'] as $field) {
                    $fields[] = $field;
                }
            }
        }

        return $fields;
    }

    /**
     * Read a dot-separated path from a nested array.
     *
     * @param array<string, mixed> $source Source settings.
     * @param string               $path Dot-separated path.
     * @param mixed                $fallback Fallback value.
     * @return mixed
     */
    private static function read_path(array $source, string $path, $fallback)
    {
        $value = $source;
        foreach (explode('.', $path) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $fallback;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Write a dot-separated path to a nested array.
     *
     * @param array<string, mixed> $target Target settings, changed by reference.
     * @param string               $path Dot-separated path.
     * @param mixed                $value Value to set.
     */
    private static function write_path(array &$target, string $path, $value): void
    {
        $segments = explode('.', $path);
        $cursor = &$target;
        foreach ($segments as $segment) {
            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                $cursor[$segment] = [];
            }
            $cursor = &$cursor[$segment];
        }
        $cursor = $value;
    }
}
