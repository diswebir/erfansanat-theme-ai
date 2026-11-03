<?php
/**
 * خروجی تنظیمات در فرانت — اسکریپت سفارشی، متغیرهای برند و آرگومان‌های تماس/اعلان
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

final class OptionsOutput {

    public static function init(): void {
        add_action('wp_head', [self::class, 'print_head'], 99);
        add_action('wp_footer', [self::class, 'print_footer'], 99);
    }

    /** چاپ کد سفارشی head + متغیرهای CSS برند (فقط هنگام تغییر از پیش‌فرض) */
    public static function print_head(): void {
        $brand_vars = self::brand_css_vars();
        if ($brand_vars !== '') {
            echo '<style id="es-brand-vars">' . $brand_vars . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- مقادیر hex پس از sanitize
        }

        // کد سفارشی در لحظه ذخیره فقط برای کاربر با unfiltered_html پذیرفته می‌شود (Sanitizer::sanitize_custom_scripts)
        $head_js = (string) es_option('custom.head_js', '');
        if ($head_js !== '') {
            echo $head_js . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- ورودی ویژه کاربر مجاز در لحظه ذخیره
        }
    }

    /** چاپ کد سفارشی footer */
    public static function print_footer(): void {
        $footer_js = (string) es_option('custom.footer_js', '');
        if ($footer_js === '') {
            return;
        }
        echo $footer_js . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- ورودی ویژه کاربر مجاز در لحظه ذخیره
    }

    /** متغیرهای CSS برند — فقط مقادیر متفاوت از پیش‌فرض میرور */
    public static function brand_css_vars(): string {
        $defaults = ThemeOptions::defaults()['brand'] ?? [];
        $vars     = '';
        foreach (['brand_primary' => 'es-brand-primary', 'brand_dark' => 'es-brand-dark', 'brand_accent' => 'es-brand-accent'] as $key => $css_var) {
            $value    = (string) es_option('brand.' . $key, (string) ($defaults[$key] ?? ''));
            $fallback = (string) ($defaults[$key] ?? '');
            if ($value === '' || $value === $fallback) {
                continue; // پیش‌فرض میرور → CSS خود قالب کافی است
            }
            $vars .= sprintf('--%s:%s;', $css_var, $value);
        }
        return $vars !== '' ? ':root{' . $vars . '}' : '';
    }

    /** آرگومان‌های تماس برای فوتر/سکشن تماس (پیش‌فرض = میرور) */
    public static function contact_args(): array {
        $socials = [];
        foreach (['instagram' => 'اینستاگرام', 'telegram' => 'تلگرام', 'linkedin' => 'لینکدین', 'whatsapp' => 'واتساپ', 'aparat' => 'آپارات'] as $key => $label) {
            $url = (string) es_option('footer.social_' . $key, '');
            if ($url !== '') {
                $socials[$key] = ['url' => $url, 'label' => $label];
            }
        }

        return [
            'phone'         => (string) es_option('footer.contact_phone', '031-91091011'),
            'phone_display' => (string) es_option('footer.contact_phone_display', '۰۳۱ - ۹۱۰۹۱۰۱۱ (۱۰ خط ویژه)'),
            'sales_phone'   => (string) es_option('footer.contact_sales_phone', '09137976915'),
            'sales_display' => (string) es_option('footer.contact_sales_display', 'مدیر فروش (آقای محمدعلی قربانی): ۰۹۱۳۷۹۷۶۹۱۵'),
            'email'         => (string) es_option('footer.contact_email', 'info@erfansanat.com'),
            'address'       => (string) es_option('footer.contact_address', ''),
            'postal_code'   => (string) es_option('footer.contact_postal_code', '۸۴۱۸۱۴۸۶۷۸'),
            'hours'         => (string) es_option('footer.working_hours', 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر'),
            'socials'       => $socials,
        ];
    }

    /** آرگومان‌های نوار اعلان */
    public static function announcement_args(): array {
        return [
            'enabled' => (bool) es_option('header.ann_enabled', 0),
            'text'    => (string) es_option('header.ann_text', ''),
            'url'     => (string) es_option('header.ann_url', ''),
            'bg'      => (string) es_option('header.ann_bg', '#065f46'),
        ];
    }
}
