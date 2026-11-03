<?php
/**
 * داشبورد اختصاصی عرفان صنعت — صفحه تنظیمات تب‌دار با ذخیره ایجکس امن
 *
 * ذخیره‌سازی در آپشن واحد erfansanat_theme_options (autoload=false)؛
 * خواندن فرانت از طریق es_option() (کش‌شده — inc/Helpers.php).
 * پیش‌فرض‌ها عیناً از میرور استاتیک استخراج شده‌اند (temp/STATIC_TEMPLATE_MAP.md).
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

final class ThemeOptions {

    public const OPTION_KEY   = 'erfansanat_theme_options';
    public const NONCE_ACTION = 'es_admin';
    public const MENU_SLUG    = 'erfansanat-theme';
    public const AJAX_ACTION  = 'es_theme_options_save';

    public static function init(): void {
        add_action('admin_menu', [self::class, 'register_menu']);
        add_action('admin_enqueue_scripts', [AdminAssets::class, 'enqueue']);
        add_action('wp_ajax_' . self::AJAX_ACTION, [self::class, 'ajax_save']);
        add_action('admin_init', [self::class, 'register_media']);
        add_filter('es_default_options', [self::class, 'merge_defaults']);
    }

    /** کتابخانه رسانه فقط در صفحه تنظیمات (پیکر media فیلدها) */
    public static function register_media(): void {
        if (self::is_options_screen() && function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }
    }

    public static function register_menu(): void {
        add_menu_page(
            __('داشبورد عرفان صنعت', 'erfansanat'),
            __('عرفان صنعت', 'erfansanat'),
            'manage_options',
            self::MENU_SLUG,
            [self::class, 'render_page'],
            'dashicons-admin-customizer',
            59
        );
        add_submenu_page(
            self::MENU_SLUG,
            __('تنظیمات قالب عرفان صنعت', 'erfansanat'),
            __('تنظیمات قالب', 'erfansanat'),
            'manage_options',
            self::MENU_SLUG,
            [self::class, 'render_page']
        );
    }

    /** آیا صفحه جاری، صفحه تنظیمات قالب است؟ */
    public static function is_options_screen(): bool {
        return isset($_GET['page']) && is_string($_GET['page']) && $_GET['page'] === self::MENU_SLUG;
    }

    /**
     * درخت پیش‌فرض‌ها از روی اسکیم فیلدها (منبع واحد حقیقت)
     *
     * @return array<string, array<string, mixed>>
     */
    public static function defaults(): array {
        $tree = [];
        foreach (self::tabs() as $tab_key => $tab) {
            foreach (($tab['fields'] ?? []) as $field) {
                if (!isset($field['key'])) {
                    continue;
                }
                $tree[(string) $tab_key][$field['key']] = $field['default'] ?? '';
            }
        }
        return $tree;
    }

    /** ادغام پیش‌فرض‌های اسکیم با آپشن ذخیره‌شده (فیلتر es_default_options) */
    public static function merge_defaults(array $raw): array {
        return array_replace_recursive(self::defaults(), $raw);
    }

    /**
     * تعریف تب‌های داشبورد
     *
     * @return array<string, array{label:string, icon:string, description:string, fields:list<array>}>
     */
    public static function tabs(): array {
        $tabs = [
            'header'   => [
                'label'       => __('هدر و لوگو', 'erfansanat'),
                'icon'        => '🖼',
                'description' => __('مدیریت لوگو، رفتار هدر چسبان و نوار اعلان', 'erfansanat'),
                'fields'      => self::header_fields(),
            ],
            'footer'   => [
                'label'       => __('فوتر و تماس', 'erfansanat'),
                'icon'        => '📞',
                'description' => __('اطلاعات تماس کارخانه، شبکه‌های اجتماعی و متن فوتر', 'erfansanat'),
                'fields'      => self::footer_fields(),
            ],
            'brand'    => [
                'label'       => __('برند و استایل', 'erfansanat'),
                'icon'        => '🎨',
                'description' => __('رنگ‌های برند (تزریق متغیر CSS فقط هنگام تغییر از پیش‌فرض)', 'erfansanat'),
                'fields'      => self::brand_fields(),
            ],
            'custom'   => [
                'label'       => __('اسکریپت‌های سفارشی', 'erfansanat'),
                'icon'        => '⌨️',
                'description' => __('تزریق کد سفارشی در head و footer — فقط برای مدیران با مجوز unfiltered_html', 'erfansanat'),
                'fields'      => self::custom_fields(),
            ],
            'archives' => [
                'label'       => __('آرشیوها', 'erfansanat'),
                'icon'        => '🗃',
                'description' => __('تعداد ستون‌ها و آیتم‌های هر صفحه در آرشیو محصولات، پروژه‌ها و وبلاگ', 'erfansanat'),
                'fields'      => self::archives_fields(),
            ],
        ];

        /**
         * فیلتر es_theme_options_tabs — توسعه تب‌ها از فازهای بعد
         */
        $tabs = apply_filters('es_theme_options_tabs', $tabs);

        return is_array($tabs) ? $tabs : [];
    }

    /** تب هدر: لوگو، رفتار هدر، CTA و نوار اعلان — پیش‌فرض‌ها عین میرور */
    private static function header_fields(): array {
        return [
            [
                'key' => 'logo', 'type' => 'media', 'label' => __('لوگوی سایت', 'erfansanat'),
                'default' => 'assets/images/ese-logo.svg',
                'help'    => __('آیدی رسانه یا آدرس تصویر؛ خالی بودن → لوگوی پیش‌فرض قالب', 'erfansanat'),
            ],
            [
                'key' => 'logo_alt', 'type' => 'text', 'label' => __('متن جایگزین لوگو', 'erfansanat'),
                'default' => __('لوگوی شرکت دانش‌بنیان عرفان صنعت اصفهان', 'erfansanat'),
            ],
            [
                'key' => 'sticky', 'type' => 'switch', 'label' => __('هدر چسبان هنگام اسکرول', 'erfansanat'),
                'default' => 1,
                'help'    => __('مطابق رفتار میرور (data-navbar-header)', 'erfansanat'),
            ],
            [
                'key' => 'search_toggle', 'type' => 'switch', 'label' => __('دکمه جستجوی هدر', 'erfansanat'),
                'default' => 1,
            ],
            [
                'key' => 'cart_toggle', 'type' => 'switch', 'label' => __('دکمه سبد خرید هدر', 'erfansanat'),
                'default' => 1,
                'help'    => __('با ووکامرس واقعی همگام می‌شود (فاز ۶)', 'erfansanat'),
            ],
            [
                'key' => 'cta_text', 'type' => 'text', 'label' => __('متن دکمه CTA هدر', 'erfansanat'),
                'default' => __('استعلام قیمت', 'erfansanat'),
                'placeholder' => __('مثلاً: استعلام قیمت', 'erfansanat'),
            ],
            [
                'key' => 'cta_url', 'type' => 'url', 'label' => __('لینک دکمه CTA', 'erfansanat'),
                'default' => '/contact/',
            ],
            [
                'key' => 'ann_enabled', 'type' => 'switch', 'label' => __('نمایش نوار اعلان بالای سایت', 'erfansanat'),
                'default' => 0,
                'help'    => __('میرور نوار اعلان ندارد؛ در صورت نیاز فعال کنید', 'erfansanat'),
            ],
            [
                'key' => 'ann_text', 'type' => 'textarea', 'label' => __('متن نوار اعلان', 'erfansanat'),
                'default' => '',
            ],
            [
                'key' => 'ann_url', 'type' => 'url', 'label' => __('لینک نوار اعلان', 'erfansanat'),
                'default' => '',
            ],
            [
                'key' => 'ann_bg', 'type' => 'color', 'label' => __('رنگ پس‌زمینه نوار اعلان', 'erfansanat'),
                'default' => '#065f46',
            ],
        ];
    }

    /** تب فوتر: تماس، سوشال، متن‌ها و لینک‌های دسترسی سریع — پیش‌فرض‌ها عین میرور */
    private static function footer_fields(): array {
        return [
            [
                'key' => 'contact_phone', 'type' => 'text', 'label' => __('تلفن کارخانه', 'erfansanat'),
                'default' => '031-91091011',
                'help'    => __('مطابق JSON-LD میرور', 'erfansanat'),
            ],
            [
                'key' => 'contact_phone_display', 'type' => 'text', 'label' => __('نمایش تلفن (فارسی)', 'erfansanat'),
                'default' => '۰۳۱ - ۹۱۰۹۱۰۱۱ (۱۰ خط ویژه)',
            ],
            [
                'key' => 'contact_sales_phone', 'type' => 'text', 'label' => __('تلفن مدیر فروش', 'erfansanat'),
                'default' => '09137976915',
            ],
            [
                'key' => 'contact_sales_display', 'type' => 'text', 'label' => __('نمایش تلفن مدیر فروش', 'erfansanat'),
                'default' => 'مدیر فروش (آقای محمدعلی قربانی): ۰۹۱۳۷۹۷۶۹۱۵',
            ],
            [
                'key' => 'contact_email', 'type' => 'text', 'label' => __('ایمیل', 'erfansanat'),
                'default' => 'info@erfansanat.com',
            ],
            [
                'key' => 'contact_address', 'type' => 'textarea', 'label' => __('آدرس کارخانه', 'erfansanat'),
                'default' => 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹',
            ],
            [
                'key' => 'contact_postal_code', 'type' => 'text', 'label' => __('کد پستی', 'erfansanat'),
                'default' => '۸۴۱۸۱۴۸۶۷۸',
            ],
            [
                'key' => 'working_hours', 'type' => 'text', 'label' => __('ساعات کاری', 'erfansanat'),
                'default' => 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر',
            ],
            [
                'key' => 'footer_about', 'type' => 'textarea', 'label' => __('متن معرفی فوتر', 'erfansanat'),
                'default' => 'طراح و تولیدکننده تجهیزات نورپردازی پیشرفته، المان‌های نوری تزئینی، درختان نوری هوشمند، خورشیدنماها، ریسه‌های LED (بلوطی، فندقی، سوزنی IP68) و پوینت لایت پیکسل با ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش.',
            ],
            [
                'key' => 'footer_copyright', 'type' => 'text', 'label' => __('متن کپی‌رایت', 'erfansanat'),
                'default' => '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.',
            ],
            [
                'key' => 'social_instagram', 'type' => 'url', 'label' => __('اینستاگرام', 'erfansanat'),
                'default' => 'https://instagram.com/erfan_sanat_esfahan',
            ],
            [
                'key' => 'social_telegram', 'type' => 'url', 'label' => __('تلگرام', 'erfansanat'),
                'default' => 'https://t.me/erfansanat',
            ],
            [
                'key' => 'social_linkedin', 'type' => 'url', 'label' => __('لینکدین', 'erfansanat'),
                'default' => 'https://linkedin.com/company/erfansanat',
            ],
            [
                'key' => 'social_whatsapp', 'type' => 'url', 'label' => __('واتساپ', 'erfansanat'),
                'default' => '',
                'help'    => __('خالی → آیکون نمایش داده نمی‌شود', 'erfansanat'),
            ],
            [
                'key' => 'social_aparat', 'type' => 'url', 'label' => __('آپارات', 'erfansanat'),
                'default' => '',
                'help'    => __('خالی → آیکون نمایش داده نمی‌شود', 'erfansanat'),
            ],
            [
                'key' => 'quick_links', 'type' => 'repeater', 'label' => __('لینک‌های دسترسی سریع فوتر', 'erfansanat'),
                'add_label' => __('افزودن لینک', 'erfansanat'),
                'help'      => __('Fallback وقتی منوی «تولیدات شرکت» تعریف نشده باشد', 'erfansanat'),
                'default'   => [
                    ['label' => 'فروشگاه محصولات', 'url' => '/shop/'],
                    ['label' => 'پروژه‌های اجراشده', 'url' => '/projects/'],
                    ['label' => 'خدمات نورپردازی', 'url' => '/services/'],
                    ['label' => 'مجوزها و افتخارات', 'url' => '/licenses-and-honors/'],
                    ['label' => 'سوالات متداول', 'url' => '/faq/'],
                    ['label' => 'شرایط گارانتی محصولات', 'url' => '/refund_returns/'],
                    ['label' => 'فرصت‌های شغلی و استخدام', 'url' => '/recruitment/'],
                    ['label' => 'اخبار و مقالات', 'url' => '/blog/'],
                ],
                'fields' => [
                    ['key' => 'label', 'type' => 'text', 'label' => __('عنوان', 'erfansanat')],
                    ['key' => 'url', 'type' => 'url', 'label' => __('آدرس', 'erfansanat')],
                ],
            ],
        ];
    }

    /** تب برند — رنگ‌های پایه از styles.css میرور (emerald) */
    private static function brand_fields(): array {
        return [
            [
                'key' => 'brand_primary', 'type' => 'color', 'label' => __('رنگ اصلی برند', 'erfansanat'),
                'default' => '#059669',
                'help'    => __('پیش‌فرض میرور (emerald-600)؛ تغییر فقط در صورت نیاز', 'erfansanat'),
            ],
            [
                'key' => 'brand_dark', 'type' => 'color', 'label' => __('رنگ تیره برند', 'erfansanat'),
                'default' => '#065f46',
            ],
            [
                'key' => 'brand_accent', 'type' => 'color', 'label' => __('رنگ مکمل', 'erfansanat'),
                'default' => '#10b981',
            ],
        ];
    }

    /** تب اسکریپت سفارشی — ورودی خام فقط برای کاربر با unfiltered_html */
    private static function custom_fields(): array {
        return [
            [
                'key' => 'head_js', 'type' => 'textarea', 'label' => __('کد سفارشی head', 'erfansanat'),
                'default' => '',
                'help'    => __('تگ‌های script/meta verifying و… — دقیقاً قبل از بسته شدن head چاپ می‌شود', 'erfansanat'),
            ],
            [
                'key' => 'footer_js', 'type' => 'textarea', 'label' => __('کد سفارشی footer', 'erfansanat'),
                'default' => '',
                'help'    => __('تگ‌های تحلیلی و… — دقیقاً قبل از بسته شدن body چاپ می‌شود', 'erfansanat'),
            ],
        ];
    }

    /** تب آرشیوها — مطابق گرید میرور */
    private static function archives_fields(): array {
        return [
            [
                'key' => 'products_per_page', 'type' => 'number', 'label' => __('محصولات در هر صفحه', 'erfansanat'),
                'default' => 20, 'min' => 1, 'max' => 100,
                'help'    => __('میرور: ۲۰ کارت در آرشیو فروشگاه', 'erfansanat'),
            ],
            [
                'key' => 'products_columns', 'type' => 'select', 'label' => __('ستون‌های گرید محصولات', 'erfansanat'),
                'default' => '4', 'options' => ['2' => '۲ ستون', '3' => '۳ ستون', '4' => '۴ ستون'],
            ],
            [
                'key' => 'projects_per_page', 'type' => 'number', 'label' => __('پروژه‌ها در هر صفحه', 'erfansanat'),
                'default' => 12, 'min' => 1, 'max' => 100,
            ],
            [
                'key' => 'posts_per_page', 'type' => 'number', 'label' => __('مقالات در هر صفحه', 'erfansanat'),
                'default' => 9, 'min' => 1, 'max' => 100,
            ],
        ];
    }

    /** همه فیلدهای همه تب‌ها (برای sanitize) */
    public static function all_fields(): array {
        $fields = [];
        foreach (self::tabs() as $tab_key => $tab) {
            foreach (($tab['fields'] ?? []) as $field) {
                if (!is_array($field) || !isset($field['key'])) {
                    continue;
                }
                $field['tab']            = (string) $tab_key;
                $fields[$field['key']] = $field;
            }
        }
        return $fields;
    }

    /** رندر صفحه تب‌دار (RTL) */
    public static function render_page(): void {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('شما اجازه دسترسی به این صفحه را ندارید.', 'erfansanat'), 403);
        }

        $tabs = self::tabs();

        echo '<div class="wrap es-admin" data-es-options>';
        printf('<header class="es-admin__header"><div><h1 class="es-admin__title">%s</h1><p class="es-admin__subtitle">%s</p></div>', esc_html__('داشبورد اختصاصی عرفان صنعت', 'erfansanat'), esc_html__('مدیریت تنظیمات قالب بدون صفحه‌ساز — ذخیره آنی با ایجکس', 'erfansanat'));
        printf('<button type="button" class="es-admin__save button button-primary button-hero" data-es-save>%s</button></header>', esc_html__('ذخیره تغییرات', 'erfansanat'));

        echo '<nav class="es-admin__nav" role="tablist">';
        $first = true;
        foreach ($tabs as $tab_key => $tab) {
            printf(
                '<button type="button" class="es-admin__nav-btn%1$s" role="tab" data-es-tab-btn="%2$s" aria-selected="%3$s"><span class="es-admin__nav-icon">%4$s</span>%5$s</button>',
                $first ? ' is-active' : '',
                esc_attr((string) $tab_key),
                $first ? 'true' : 'false',
                esc_html($tab['icon'] ?? '⚙'),
                esc_html($tab['label'] ?? (string) $tab_key)
            );
            $first = false;
        }
        echo '</nav>';

        echo '<form class="es-admin__form" data-es-options-form onsubmit="return false;">';
        wp_nonce_field(self::NONCE_ACTION, 'es_admin_nonce');

        $first = true;
        foreach ($tabs as $tab_key => $tab) {
            printf(
                '<section class="es-admin__panel%1$s" role="tabpanel" data-es-tab-panel="%2$s"%3$s>',
                $first ? ' is-active' : '',
                esc_attr((string) $tab_key),
                $first ? '' : ' hidden'
            );
            printf('<p class="es-admin__panel-desc">%s</p>', esc_html($tab['description'] ?? ''));
            foreach (($tab['fields'] ?? []) as $field) {
                if (!is_array($field) || !isset($field['key']) || !class_exists(FieldRenderer::class)) {
                    continue;
                }
                // مقدار جاری: آپشن ذخیره‌شده یا پیش‌فرض (از طریق es_option و درخت defaults)
                $current           = es_option($tab_key . '.' . $field['key'], $field['default'] ?? '');
                $field['name']     = sprintf('opt[%s][%s]', (string) $tab_key, (string) $field['key']);
                $field['value']    = $current;
                echo FieldRenderer::render($field, $current, 'opt');
            }
            echo '</section>';
            $first = false;
        }
        echo '</form>';

        printf('<footer class="es-admin__footer"><span>%s</span></footer>', esc_html__('قالب اختصاصی عرفان صنعت اصفهان — نسخه', 'erfansanat') . ' ' . esc_html(ERFANSANAT_VERSION));
        echo '</div>';
    }

    /**
     * ذخیره ایجکس: نانس → کاپابیلیتی → sanitize → آپشن واحد با autoload=false
     */
    public static function ajax_save(): void {
        // ۱) نانس (نامعتبر → die مانند هسته)
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        // ۲) کاپابیلیتی
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('دسترسی غیرمجاز.', 'erfansanat')], 403);
        }

        // ۳) پیلود تودرتو (opt[tab][field]) — پاکسازی به ازای اسکیم هر تب
        $old     = function_exists('get_option') ? (array) get_option(self::OPTION_KEY, []) : [];
        $raw     = isset($_POST['opt']) && is_array($_POST['opt']) ? wp_unslash($_POST['opt']) : [];
        $payload = [];

        foreach (self::tabs() as $tab_key => $tab) {
            if (!isset($raw[$tab_key]) || !is_array($raw[$tab_key])) {
                continue;
            }
            $payload[$tab_key] = Sanitizer::sanitize_fields(is_array($tab['fields'] ?? null) ? $tab['fields'] : [], $raw[$tab_key]);
        }

        if (array_key_exists('custom', $payload)) {
            $payload['custom'] = Sanitizer::sanitize_custom_scripts(
                is_array($payload['custom']) ? $payload['custom'] : [],
                is_array($old['custom'] ?? null) ? $old['custom'] : []
            );
        }

        // ۴) ذخیره — یک آپشن، بدون autoload
        $saved = update_option(self::OPTION_KEY, $payload, false);

        if (function_exists('es_option_reset_cache')) {
            es_option_reset_cache();
        }

        do_action('es_theme_options_saved', $payload);

        wp_send_json_success([
            'message' => $saved ? __('تنظیمات با موفقیت ذخیره شد.', 'erfansanat') : __('تنظیمات به‌روزرسانی شد (بدون تغییر).', 'erfansanat'),
            'data'    => $payload,
        ]);
    }
}
