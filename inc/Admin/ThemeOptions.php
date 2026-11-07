<?php
/**
 * ThemeOptions — custom admin dashboard «عرفان صنعت اصفهان».
 *
 * Modern, tabbed, fully RTL panel with AJAX save, no plugin dependency.
 * All settings persist in ONE option: erfansanat_theme_options (array).
 * Front-end reads via the cached es_option() helper, which falls back
 * to defaults() — defaults mirror the static mirror 1:1.
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
                'site_name'    => 'عرفان صنعت اصفهان (ESE)',
                'site_tagline' => 'نورپردازی شهری و المان‌های نوری',
            ],
            'header' => [
                'logo'          => 'assets/images/ese-logo.svg',
                'announcement'  => '', // static header has no announcement bar by default
                'cta_text'      => 'استعلام قیمت',
                'cta_link'      => 'contact.html',
            ],
            'footer' => [
                'email'       => 'info@erfansanat.com',
                'phone'       => '031-33333333',
                'address'     => 'اصفهان، شهرک صنعتی',
                'footer_text' => '© عرفان صنعت اصفهان — تمام حقوق محفوظ است.',
            ],
            'social' => [
                'instagram' => '',
                'telegram'  => '',
                'whatsapp'  => '',
                'linkedin'  => '',
                'aparat'    => '',
            ],
            'brand' => [
                'brand_color'   => '#059669', // emerald-600 used across static CSS
                'brand_color_dark' => '#047857',
                'custom_css'    => '',
                'header_script' => '',
                'footer_script' => '',
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
        self::persist(self::sanitize(wp_unslash($_POST)));
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
        wp_enqueue_media(); // media uploader for logo/images
        wp_enqueue_style('erfansanat-admin', ERFANSANAT_THEME_URI . 'assets/css/admin.css', [], ERFANSANAT_THEME_VERSION);
        wp_enqueue_script('erfansanat-admin', ERFANSANAT_THEME_URI . 'assets/js/admin.js', [], ERFANSANAT_THEME_VERSION, true);
        wp_localize_script('erfansanat-admin', 'ErfanSanatAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce(self::NONCE),
            'action'  => 'erfansanat_save_options',
        ]);
    }

    private static function field(string $name, string $label, $value, string $type = 'text'): void
    {
        printf(
            '<tr><th scope="row"><label for="%1$s">%2$s</label></th><td><input type="%3$s" id="%1$s" name="options[%4$s]" value="%5$s" class="regular-text"></td></tr>',
            esc_attr($name),
            esc_html($label),
            esc_attr($type),
            esc_attr($name),
            esc_attr((string) $value)
        );
    }

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
        ?>
        <div class="wrap erfansanat-admin" dir="rtl">
            <h1><?php echo esc_html__('داشبورد اختصاصی عرفان صنعت اصفهان', 'erfansanat'); ?></h1>
            <form id="erfansanat-options-form" method="post">
                <?php wp_nonce_field(self::NONCE, 'erfansanat_nonce'); ?>
                <input type="hidden" name="erfansanat_options_submit" value="1">
                <div class="erfansanat-tabs">
                    <nav class="erfansanat-tab-nav">
                        <button type="button" class="erfansanat-tab active" data-tab="general"><?php esc_html_e('عمومی', 'erfansanat'); ?></button>
                        <button type="button" class="erfansanat-tab" data-tab="header"><?php esc_html_e('هدر و مگامنو', 'erfansanat'); ?></button>
                        <button type="button" class="erfansanat-tab" data-tab="footer"><?php esc_html_e('فوتر و تماس', 'erfansanat'); ?></button>
                        <button type="button" class="erfansanat-tab" data-tab="brand"><?php esc_html_e('برند و رنگ‌ها', 'erfansanat'); ?></button>
                    </nav>

                    <section class="erfansanat-tab-panel active" data-panel="general">
                        <table class="form-table" role="presentation">
                            <?php
                            self::field('general[site_name]', __('نام سایت', 'erfansanat'), $get('general', 'site_name'));
                            self::field('general[site_tagline]', __('شعار', 'erfansanat'), $get('general', 'site_tagline'));
                            ?>
                        </table>
                    </section>

                    <section class="erfansanat-tab-panel" data-panel="header">
                        <table class="form-table" role="presentation">
                            <?php
                            self::field('header[logo]', __('لوگو (مسیر)', 'erfansanat'), $get('header', 'logo'));
                            self::field('header[announcement]', __('نوار اعلان', 'erfansanat'), $get('header', 'announcement'));
                            self::field('header[cta_text]', __('متن دکمه CTA', 'erfansanat'), $get('header', 'cta_text'));
                            self::field('header[cta_link]', __('لینک دکمه CTA', 'erfansanat'), $get('header', 'cta_link'));
                            ?>
                        </table>
                    </section>

                    <section class="erfansanat-tab-panel" data-panel="footer">
                        <table class="form-table" role="presentation">
                            <?php
                            self::field('footer[email]', __('ایمیل', 'erfansanat'), $get('footer', 'email'));
                            self::field('footer[phone]', __('تلفن', 'erfansanat'), $get('footer', 'phone'));
                            self::field('footer[address]', __('آدرس', 'erfansanat'), $get('footer', 'address'));
                            self::field('footer[footer_text]', __('متن کپی‌رایت فوتر', 'erfansanat'), $get('footer', 'footer_text'));
                            foreach (['instagram', 'telegram', 'whatsapp', 'linkedin', 'aparat'] as $soc) {
                                self::field("social[$soc]", __('شبکه ' . $soc, 'erfansanat'), $get('social', $soc));
                            }
                            ?>
                        </table>
                    </section>

                    <section class="erfansanat-tab-panel" data-panel="brand">
                        <table class="form-table" role="presentation">
                            <?php
                            self::field('brand[brand_color]', __('رنگ اصلی برند', 'erfansanat'), $get('brand', 'brand_color'), 'color');
                            self::field('brand[brand_color_dark]', __('رنگ تیره برند', 'erfansanat'), $get('brand', 'brand_color_dark'), 'color');
                            self::field('brand[custom_css]', __('CSS سفارشی', 'erfansanat'), $get('brand', 'custom_css'));
                            self::field('brand[header_script]', __('اسکریپت هدر', 'erfansanat'), $get('brand', 'header_script'));
                            self::field('brand[footer_script]', __('اسکریپت فوتر', 'erfansanat'), $get('brand', 'footer_script'));
                            ?>
                        </table>
                    </section>
                </div>
                <p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e('ذخیره', 'erfansanat'); ?></button></p>
            </form>
        </div>
        <?php
    }
}

