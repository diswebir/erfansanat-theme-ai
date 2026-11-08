<?php
/**
 * ThemeOptions — professional custom admin dashboard «عرفان صنعت اصفهان».
 *
 * Tabbed RTL panel (7 tabs), AJAX + classic save, media uploader, color
 * pickers, toggles, selects, textareas. All settings persist in ONE option:
 * erfansanat_theme_options (array). Front-end reads via es_option() which
 * falls back to defaults() — defaults mirror the static mirror 1:1.
 *
 * @package ErfanSanat\Theme\Admin
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

if (!defined('ABSPATH')) {
    exit;
}

final class ThemeOptions
{
    public const OPTION = 'erfansanat_theme_options';
    public const NONCE  = 'erfansanat_options_nonce';
    public const PAGE   = 'erfansanat-options';

    public static function hooks(): void
    {
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_init', [self::class, 'save']);
        add_action('wp_ajax_erfansanat_save_options', [self::class, 'ajax_save']);
        add_action('admin_enqueue_scripts', [self::class, 'assets']);
    }

    /**
     * Fallback values — exactly matching the static mirror's content.
     */
    public static function defaults(): array
    {
        return [
            'general' => [
                'site_name'        => 'عرفان صنعت اصفهان (ESE)',
                'site_tagline'     => 'نورپردازی شهری و المان‌های نوری',
                'meta_description' => 'تولیدکننده تجهیزات نورپردازی شهری و صنعتی — شرکت دانش‌بنیان عرفان صنعت اصفهان',
                'favicon'          => '',
            ],
            'header' => [
                'logo'          => 'assets/images/ese-logo.svg',
                'announcement'  => '',
                'cta_text'      => 'استعلام قیمت',
                'cta_link'      => 'contact.html',
                'sticky_header' => '1',
            ],
            'footer' => [
                'email'        => 'info@erfansanat.com',
                'phone'        => '031-91091011',
                'phone2'       => '09137976915',
                'address'      => 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹',
                'working_hours'=> 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر',
                'footer_text'  => '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.',
            ],
            'social' => [
                'instagram' => '',
                'telegram'  => '',
                'whatsapp'  => '',
                'linkedin'  => '',
                'aparat'    => '',
            ],
            'shop' => [
                'products_per_page' => '9',
                'projects_per_page' => '6',
                'currency'          => 'IRR',
            ],
            'forms' => [
                'notify_email'     => '', // empty = admin_email
                'success_message'  => 'پیام شما با موفقیت ثبت شد.',
            ],
            'seo' => [
                'og_image' => '',
            ],
            'brand' => [
                'brand_color'      => '#059669',
                'brand_color_dark' => '#047857',
                'custom_css'       => '',
                'header_script'    => '',
                'footer_script'    => '',
            ],
            'advanced' => [
                'maintenance_mode' => '0',
                'lazy_images'      => '1',
            ],
        ];
    }

    public static function menu(): void
    {
        add_menu_page(
            __('داشبورد عرفان صنعت', 'erfansanat'),
            __('عرفان صنعت', 'erfansanat'),
            'manage_options',
            self::PAGE,
            [self::class, 'render'],
            'dashicons-admin-site-alt3',
            2
        );
    }

    public static function save(): void
    {
        if (!isset($_POST['erfansanat_options_submit'])) {
            return;
        }
        if (!current_user_can('manage_options')) {
            wp_die(__('دسترسی غیرمجاز.', 'erfansanat'));
        }
        check_admin_referer(self::NONCE, 'erfansanat_nonce');
        $payload = isset($_POST['options']) && is_array($_POST['options']) ? wp_unslash($_POST['options']) : [];
        self::persist(self::sanitize($payload));
        add_action('admin_notices', function () {
            printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html__('تنظیمات ذخیره شد.', 'erfansanat'));
        });
    }

    public static function ajax_save(): void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'forbidden'], 403);
        }
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), self::NONCE)) {
            wp_send_json_error(['message' => 'bad nonce'], 403);
        }
        $payload = isset($_POST['options']) && is_array($_POST['options'])
            ? wp_unslash($_POST['options'])
            : [];
        // checkboxes/toggles are absent from POST when off — normalize known toggles
        foreach (['header[sticky_header]', 'advanced[maintenance_mode]', 'advanced[lazy_images]'] as $toggle) {
            $g = explode('[', trim($toggle, ']'));
            if (!isset($payload[$g[0]][$g[1]]) && isset($_POST['options'][$g[0]][$g[1]]) === false) {
                $payload[$g[0]][$g[1]] = '0';
            }
        }
        self::persist(self::sanitize($payload));
        wp_send_json_success(['message' => __('ذخیره شد', 'erfansanat')]);
    }

    private static function persist(array $clean): void
    {
        $existing = get_option(self::OPTION, []);
        update_option(self::OPTION, array_replace_recursive(is_array($existing) ? $existing : [], $clean), false);
    }

    private static function sanitize(array $raw): array
    {
        $out = [];
        foreach ($raw as $key => $value) {
            $key = sanitize_key((string) $key);
            if (is_array($value)) {
                $out[$key] = self::sanitize($value);
            } elseif (strpos((string) $key, 'script') !== false || $key === 'custom_css') {
                // admin-only raw fields (manage_options): keep code, strip tags that break out
                $out[$key] = wp_strip_all_tags((string) $value);
            } else {
                $out[$key] = sanitize_text_field((string) $value);
            }
        }
        return $out;
    }

    public static function assets(string $hook): void
    {
        if (strpos($hook, self::PAGE) === false) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style('erfansanat-admin', ERFANSANAT_THEME_URI . 'assets/css/admin.css', [], ERFANSANAT_THEME_VERSION);
        wp_enqueue_style('erfansanat-admin-font', ERFANSANAT_THEME_URI . 'assets/css/admin-font.css', [], ERFANSANAT_THEME_VERSION);
        wp_enqueue_script('erfansanat-admin', ERFANSANAT_THEME_URI . 'assets/js/admin.js', [], ERFANSANAT_THEME_VERSION, true);
        wp_localize_script('erfansanat-admin', 'ErfanSanatAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce(self::NONCE),
            'action'  => 'erfansanat_save_options',
        ]);
    }

    /* ---------- field renderers ---------- */

    private static function field(string $name, string $label, $value, string $type = 'text', string $desc = ''): void
    {
        printf(
            '<tr><th scope="row"><label for="%1$s">%2$s</label></th><td><input type="%3$s" id="%1$s" name="options[%4$s]" value="%5$s" class="regular-text" />%6$s</td></tr>',
            esc_attr($name), esc_html($label), esc_attr($type), esc_attr($name), esc_attr((string) $value),
            $desc !== '' ? '<p class="description">' . esc_html($desc) . '</p>' : ''
        );
    }

    private static function textarea(string $name, string $label, $value, string $desc = ''): void
    {
        printf(
            '<tr><th scope="row"><label for="%1$s">%2$s</label></th><td><textarea id="%1$s" name="options[%3$s]" rows="5" class="large-text code">%4$s</textarea>%5$s</td></tr>',
            esc_attr($name), esc_html($label), esc_attr($name), esc_textarea((string) $value),
            $desc !== '' ? '<p class="description">' . esc_html($desc) . '</p>' : ''
        );
    }

    private static function toggle(string $name, string $label, $value, string $desc = ''): void
    {
        printf(
            '<tr><th scope="row">%2$s</th><td><label class="erfansanat-toggle"><input type="checkbox" id="%1$s" name="options[%3$s]" value="1" %4$s /> <span></span> %5$s</label></td></tr>',
            esc_attr($name), esc_html($label), esc_attr($name), checked('1', (string) $value, false),
            $desc !== '' ? '<p class="description">' . esc_html($desc) . '</p>' : ''
        );
    }

    private static function select(string $name, string $label, $value, array $choices): void
    {
        $opts = '';
        foreach ($choices as $v => $l) {
            $opts .= sprintf('<option value="%s" %s>%s</option>', esc_attr((string) $v), selected((string) $value, (string) $v, false), esc_html($l));
        }
        printf(
            '<tr><th scope="row"><label for="%1$s">%2$s</label></th><td><select id="%1$s" name="options[%3$s]">%4$s</select></td></tr>',
            esc_attr($name), esc_html($label), esc_attr($name), $opts
        );
    }

    private static function media(string $name, string $label, $value, string $desc = ''): void
    {
        printf(
            '<tr><th scope="row"><label for="%1$s">%2$s</label></th><td>'
            . '<input type="text" id="%1$s" name="options[%3$s]" value="%4$s" class="regular-text erfansanat-media-url" />'
            . '<button type="button" class="button erfansanat-media-btn" data-target="%1$s">%5$s</button>'
            . '%6$s</td></tr>',
            esc_attr($name), esc_html($label), esc_attr($name), esc_attr((string) $value),
            esc_html__('انتخاب تصویر', 'erfansanat'),
            $desc !== '' ? '<p class="description">' . esc_html($desc) . '</p>' : ''
        );
    }

    /* ---------- page render ---------- */

    public static function render(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('دسترسی غیرمجاز.', 'erfansanat'));
        }
        $opt = get_option(self::OPTION, []);
        $d   = self::defaults();
        $get = function (string $group, string $key) use ($opt, $d) {
            return $opt[$group][$key] ?? $d[$group][$key] ?? '';
        };
        $tabs = [
            'general'  => 'عمومی',
            'header'   => 'هدر',
            'footer'   => 'فوتر و تماس',
            'shop'     => 'فروشگاه',
            'forms'    => 'فرم‌ها',
            'seo'      => 'سئو',
            'brand'    => 'برند و کدها',
            'advanced' => 'پیشرفته',
        ];
        ?>
        <div class="wrap erfansanat-admin theme-options-wrap" dir="rtl">
            <h1><?php echo esc_html__('داشبورد اختصاصی عرفان صنعت اصفهان', 'erfansanat'); ?></h1>
            <p class="description"><?php esc_html_e('تمام تنظیمات قالب از این پنل مدیریت می‌شوند و در یک option ذخیره می‌گردند.', 'erfansanat'); ?></p>
            <form id="erfansanat-options-form" method="post">
                <?php wp_nonce_field(self::NONCE, 'erfansanat_nonce'); ?>
                <input type="hidden" name="erfansanat_options_submit" value="1">
                <div class="erfansanat-tabs">
                    <nav class="erfansanat-tab-nav">
                        <?php foreach ($tabs as $id => $label) : ?>
                            <button type="button" class="erfansanat-tab<?php echo $id === 'general' ? ' active' : ''; ?>" data-tab="<?php echo esc_attr($id); ?>"><?php echo esc_html__($label, 'erfansanat'); ?></button>
                        <?php endforeach; ?>
                    </nav>

                    <section class="erfansanat-tab-panel active" data-panel="general">
                        <table class="form-table" role="presentation">
                            <?php
                            self::field('general[site_name]', __('نام سایت', 'erfansanat'), $get('general', 'site_name'));
                            self::field('general[site_tagline]', __('شعار', 'erfansanat'), $get('general', 'site_tagline'));
                            self::textarea('general[meta_description]', __('meta description پیش‌فرض', 'erfansanat'), $get('general', 'meta_description'), 'در صفحات بدون خلاصه استفاده می‌شود');
                            self::media('general[favicon]', __('فاوآیکون', 'erfansanat'), $get('general', 'favicon'), 'PNG/SVG — در <head> قرار می‌گیرد');
                            ?>
                        </table>
                    </section>

                    <section class="erfansanat-tab-panel" data-panel="header">
                        <table class="form-table" role="presentation">
                            <?php
                            self::media('header[logo]', __('لوگو', 'erfansanat'), $get('header', 'logo'));
                            self::field('header[announcement]', __('نوار اعلان', 'erfansanat'), $get('header', 'announcement'), 'خالی = بدون نوار اعلان');
                            self::field('header[cta_text]', __('متن دکمه CTA', 'erfansanat'), $get('header', 'cta_text'));
                            self::field('header[cta_link]', __('لینک دکمه CTA', 'erfansanat'), $get('header', 'cta_link'));
                            self::toggle('header[sticky_header]', __('هدر چسبان (sticky)', 'erfansanat'), $get('header', 'sticky_header'));
                            ?>
                        </table>
                    </section>

                    <section class="erfansanat-tab-panel" data-panel="footer">
                        <table class="form-table" role="presentation">
                            <?php
                            self::field('footer[email]', __('ایمیل', 'erfansanat'), $get('footer', 'email'));
                            self::field('footer[phone]', __('تلفن اصلی', 'erfansanat'), $get('footer', 'phone'));
                            self::field('footer[phone2]', __('تلفن دوم / مدیر فروش', 'erfansanat'), $get('footer', 'phone2'));
                            self::field('footer[address]', __('آدرس', 'erfansanat'), $get('footer', 'address'));
                            self::field('footer[working_hours]', __('ساعات کاری', 'erfansanat'), $get('footer', 'working_hours'));
                            self::field('footer[footer_text]', __('متن کپی‌رایت فوتر', 'erfansanat'), $get('footer', 'footer_text'));
                            foreach (['instagram', 'telegram', 'whatsapp', 'linkedin', 'aparat'] as $soc) {
                                self::field("social[$soc]", 'شبکه ' . $soc, $get('social', $soc), 'text', 'لینک کامل — خالی = نمایش داده نمی‌شود');
                            }
                            ?>
                        </table>
                    </section>

                    <section class="erfansanat-tab-panel" data-panel="shop">
                        <table class="form-table" role="presentation">
                            <?php
                            self::select('shop[products_per_page]', __('محصول در هر صفحه فروشگاه', 'erfansanat'), $get('shop', 'products_per_page'), ['6' => '۶', '9' => '۹', '12' => '۱۲', '24' => '۲۴']);
                            self::select('shop[projects_per_page]', __('پروژه در هر صفحه آرشیو', 'erfansanat'), $get('shop', 'projects_per_page'), ['6' => '۶', '9' => '۹', '12' => '۱۲']);
                            self::select('shop[currency]', __('واحد پول', 'erfansanat'), $get('shop', 'currency'), ['IRR' => 'ریال (IRR)', 'IRT' => 'تومان (IRT)']);
                            ?>
                        </table>
                    </section>

                    <section class="erfansanat-tab-panel" data-panel="forms">
                        <table class="form-table" role="presentation">
                            <?php
                            self::field('forms[notify_email]', __('ایمیل دریافت اعلان فرم‌ها', 'erfansanat'), $get('forms', 'notify_email'), 'خالی = ایمیل مدیر سایت');
                            self::field('forms[success_message]', __('پیام موفقیت پس از ارسال فرم', 'erfansanat'), $get('forms', 'success_message'));
                            ?>
                        </table>
                    </section>

                    <section class="erfansanat-tab-panel" data-panel="seo">
                        <table class="form-table" role="presentation">
                            <?php
                            self::media('seo[og_image]', __('تصویر پیش‌فرض Open Graph', 'erfansanat'), $get('seo', 'og_image'), '۱۲۰۰×۶۳۰ — در صورت نبودن تصویر صفحه استفاده می‌شود');
                            ?>
                        </table>
                    </section>

                    <section class="erfansanat-tab-panel" data-panel="brand">
                        <table class="form-table" role="presentation">
                            <?php
                            self::field('brand[brand_color]', __('رنگ اصلی برند', 'erfansanat'), $get('brand', 'brand_color'), 'color');
                            self::field('brand[brand_color_dark]', __('رنگ تیره برند', 'erfansanat'), $get('brand', 'brand_color_dark'), 'color');
                            self::textarea('brand[custom_css]', __('CSS سفارشی (wp_head)', 'erfansanat'), $get('brand', 'custom_css'));
                            self::textarea('brand[header_script]', __('اسکریپت هدر (قبل </head>)', 'erfansanat'), $get('brand', 'header_script'));
                            self::textarea('brand[footer_script]', __('اسکریپت فوتر (قبل </body>)', 'erfansanat'), $get('brand', 'footer_script'));
                            ?>
                        </table>
                    </section>

                    <section class="erfansanat-tab-panel" data-panel="advanced">
                        <table class="form-table" role="presentation">
                            <?php
                            self::toggle('advanced[maintenance_mode]', __('حالت نگهداری (Coming Soon)', 'erfansanat'), $get('advanced', 'maintenance_mode'), 'بازدیدکنندگان صفحه نگهداری را می‌بینند');
                            self::toggle('advanced[lazy_images]', __('lazy loading تصاویر', 'erfansanat'), $get('advanced', 'lazy_images'));
                            ?>
                        </table>
                    </section>
                </div>
                <p class="submit">
                    <button type="submit" class="button button-primary button-hero"><?php esc_html_e('ذخیره تنظیمات', 'erfansanat'); ?></button>
                    <span class="erfansanat-save-status" aria-live="polite"></span>
                </p>
            </form>
        </div>
        <?php
    }
}
