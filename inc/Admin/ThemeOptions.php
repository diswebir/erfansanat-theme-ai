<?php
/**
 * داشبورد اختصاصی عرفان صنعت — صفحه تنظیمات تب‌دار با ذخیره ایجکس امن
 *
 * ذخیره‌سازی در آپشن واحد erfansanat_theme_options (autoload=false)؛
 * خواندن فرانت از طریق es_option() (کش‌شده — inc/Helpers.php).
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

final class ThemeOptions {

    public const OPTION_KEY  = 'erfansanat_theme_options';
    public const NONCE_ACTION = 'es_admin';
    public const MENU_SLUG    = 'erfansanat-theme';
    public const AJAX_ACTION  = 'es_theme_options_save';

    public static function init(): void {
        add_action('admin_menu', [self::class, 'register_menu']);
        add_action('admin_enqueue_scripts', [AdminAssets::class, 'enqueue']);
        add_action('wp_ajax_' . self::AJAX_ACTION, [self::class, 'ajax_save']);
        add_action('admin_init', [self::class, 'register_media']);
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
     * تعریف تب‌های داشبورد — فیلدها در تسک‌های 2.3 تا 2.5 به این اسکیم اضافه می‌شوند.
     *
     * @return array<string, array{label:string, icon:string, description:string, fields:list<array>}>
     */
    public static function tabs(): array {
        $tabs = [
            'header'   => [
                'label'       => __('هدر و لوگو', 'erfansanat'),
                'icon'        => '🖼',
                'description' => __('مدیریت لوگو، رفتار هدر چسبان و نوار اعلان', 'erfansanat'),
                'fields'      => [],
            ],
            'footer'   => [
                'label'       => __('فوتر و تماس', 'erfansanat'),
                'icon'        => '📞',
                'description' => __('اطلاعات تماس کارخانه، شبکه‌های اجتماعی و متن فوتر', 'erfansanat'),
                'fields'      => [],
            ],
            'brand'    => [
                'label'       => __('برند و استایل', 'erfansanat'),
                'icon'        => '🎨',
                'description' => __('رنگ‌های برند و تزریق متغیرهای CSS', 'erfansanat'),
                'fields'      => [],
            ],
            'custom'   => [
                'label'       => __('اسکریپت‌های سفارشی', 'erfansanat'),
                'icon'        => '⌨️',
                'description' => __('تزریق کد سفارشی در head و footer سایت', 'erfansanat'),
                'fields'      => [],
            ],
            'archives' => [
                'label'       => __('آرشیوها', 'erfansanat'),
                'icon'        => '🗃',
                'description' => __('تعداد ستون‌ها و آیتم‌های هر صفحه در آرشیو محصولات، پروژه‌ها و وبلاگ', 'erfansanat'),
                'fields'      => [],
            ],
        ];

        /**
         * فیلتر es_theme_options_tabs — توسعه تب‌ها از فازهای بعد
         */
        $tabs = apply_filters('es_theme_options_tabs', $tabs);

        return is_array($tabs) ? $tabs : [];
    }

    /** همه فیلدهای همه تب‌ها (برای sanitize و defaults) */
    public static function all_fields(): array {
        $fields = [];
        foreach (self::tabs() as $tab_key => $tab) {
            foreach (($tab['fields'] ?? []) as $field) {
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
                if (class_exists(FieldRenderer::class)) {
                    echo FieldRenderer::render(is_array($field) ? $field : [], null, 'opt');
                }
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

        // ۳) پیلود و پاکسازی
        $old      = function_exists('get_option') ? (array) get_option(self::OPTION_KEY, []) : [];
        $payload  = isset($_POST['opt']) && is_array($_POST['opt']) ? wp_unslash($_POST['opt']) : [];
        $payload  = Sanitizer::sanitize_fields(self::all_fields(), is_array($payload) ? $payload : []);

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
