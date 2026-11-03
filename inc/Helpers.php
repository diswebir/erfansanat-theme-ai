<?php
/**
 * توابع کمکی سراسری قالب عرفان صنعت
 * (فایل procedural — در functions.php پیش از همه ماژول‌ها require می‌شود)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('es_option_reset_cache')) {
    /**
     * خالی کردن کش استاتیک تنظیمات — پس از ذخیره ایجکس داشبورد (فاز ۲) در همان ریکوئست لازم می‌شود.
     */
    function es_option_reset_cache(): void {
        // کش داخل es_option استاتیک است؛ با بازسازی از طریق متغیر سراسری کنترل می‌شود
        $GLOBALS['__es_option_cache_epoch'] = ($GLOBALS['__es_option_cache_epoch'] ?? 0) + 1;
    }
}

if (!function_exists('es_option')) {
    /**
     * خواندن کش‌شده تنظیمات داشبورد اختصاصی (آپشن واحد erfansanat_theme_options)
     *
     * @param string $key     کلید ساده («contact_phone») یا مسیر تودرتو با نقطه («header.logo»)
     * @param mixed  $default مقدار جایگزین در نبود مقدار
     */
    function es_option(string $key, mixed $default = null): mixed {
        static $cache = null;
        static $epoch = 0;

        if (!is_array($cache) || $epoch !== ($GLOBALS['__es_option_cache_epoch'] ?? 0)) {
            $raw = function_exists('get_option') ? get_option('erfansanat_theme_options', []) : [];
            if (!is_array($raw)) {
                $raw = [];
            }
            /**
             * فیلتر es_default_options — در فاز ۲ (ThemeOptions) پیش‌فرض‌های میرور از همین نقطه ادغام می‌شوند.
             */
            $cache = function_exists('apply_filters') ? apply_filters('es_default_options', $raw) : $raw;
            if (!is_array($cache)) {
                $cache = $raw;
            }
            $epoch = $GLOBALS['__es_option_cache_epoch'] ?? 0;
        }

        $value = $cache;
        foreach (explode('.', $key) as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
                continue;
            }
            return $default;
        }

        return $value;
    }
}

if (!function_exists('es_template_path')) {
    /** resolve مسیر قالب با پشتیبانی override از چایلدتم */
    function es_template_path(string $template): string {
        if (!function_exists('locate_template')) {
            return is_file($template) ? $template : '';
        }
        return (string) locate_template($template);
    }
}

if (!function_exists('es_get_template')) {
    /**
     * لود امن template-parts با پاس‌دادن پارامترها (استخراج امن با EXTR_SKIP)
     *
     * @param string              $template مسیر نسبی مثل «template-parts/cards/card-product.php»
     * @param array<string,mixed> $args     متغیرهایی که داخل قالب در دسترس‌اند
     */
    function es_get_template(string $template, array $args = []): void {
        $file = es_template_path($template);
        if ($file === '' || !is_file($file)) {
            return;
        }
        if ($args !== []) {
            extract($args, EXTR_SKIP); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- الگوی استاندارد قالب‌های وردپرس
        }
        include $file;
    }
}
