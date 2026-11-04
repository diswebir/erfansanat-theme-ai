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

if (!function_exists('es_render_sections')) {
    /**
     * رندر سکشن‌های فعال یک برگه به ترتیب ادمین (موتور SectionManager)
     *
     * @param int                  $post_id   شناسه برگه (مثلاً صفحه اصلی ۸۱۴۶)
     * @param array<string,mixed>  $extra_args داده‌های اضافی برای همه سکشن‌ها
     */
    function es_render_sections(int $post_id, array $extra_args = []): void {
        if (!class_exists(\ErfanSanat\Theme\Admin\SectionManager::class) || !method_exists(\ErfanSanat\Theme\Admin\SectionManager::class, 'get_sections')) {
            return;
        }

        $sections = \ErfanSanat\Theme\Admin\SectionManager::get_sections($post_id);

        /**
         * فیلتر es_sections_render_args — توسعه‌پذیری خروجی
         */
        $extra_args = apply_filters('es_sections_render_args', $extra_args, $post_id);
        if (!is_array($extra_args)) {
            $extra_args = [];
        }

        foreach ($sections as $section) {
            if ((int) ($section['enabled'] ?? 0) !== 1) {
                continue; // سکشن غیرفعال رندر نمی‌شود
            }

            $section_id = (string) ($section['id'] ?? '');
            $fields     = is_array($section['fields'] ?? null) ? $section['fields'] : [];

            // ادغام پیش‌فرض‌های اسکیم → حالت بدون تنظیم ادمین = ظاهر عین میرور
            foreach (\ErfanSanat\Theme\Admin\SectionManager::schema_of($section_id) as $schema_field) {
                if (is_array($schema_field) && isset($schema_field['key']) && !array_key_exists($schema_field['key'], $fields)) {
                    $fields[$schema_field['key']] = $schema_field['default'] ?? '';
                }
            }

            $args = [
                'section_id' => $section_id,
                'fields'     => $fields,
                'post_id'    => $post_id,
            ];
            es_get_template('template-parts/sections/' . $section_id . '.php', array_merge($args, $extra_args));
        }
    }
}

if (!function_exists('es_fa_num')) {
    /** تبدیل ارقام لاتین به فارسی — برای شمارنده‌ها و اعداد UI */
    function es_fa_num(int|string $value): string {
        return strtr((string) $value, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }
}

if (!function_exists('es_page_url')) {
    /**
     * URL صفحه از اسلاگ میرور — نگاشت صفحات ثابت سایت
     * shop → آرشیو ووکامرس؛ بقیه → صفحه وردپرسی با همان اسلاگ.
     */
    function es_page_url(string $slug): string {
        $map = [
            'shop'                => function_exists('wc_get_page_id') ? get_permalink((int) wc_get_page_id('shop')) : home_url('/shop/'),
            'cart'                => function_exists('wc_get_page_id') ? get_permalink((int) wc_get_page_id('cart')) : home_url('/cart/'),
            'checkout'            => function_exists('wc_get_page_id') ? get_permalink((int) wc_get_page_id('checkout')) : home_url('/checkout/'),
            'my-account'          => function_exists('wc_get_page_id') ? get_permalink((int) wc_get_page_id('myaccount')) : home_url('/my-account/'),
            'refund_returns'      => home_url('/refund_returns/'),
            'projects'            => home_url('/projects/'),
            'services'            => home_url('/services/'),
        ];
        $map = (array) apply_filters('es_page_urls', $map);
        if (isset($map[$slug]) && is_string($map[$slug]) && $map[$slug] !== '') {
            return $map[$slug];
        }
        return home_url('/' . str_replace('_', '-', $slug) . '/');
    }
}

if (!function_exists('es_logo_url')) {
    /** لوگو: custom-logo → آپشن header.logo → خالی (قالب باکس متنی ESE میرور را رندر می‌کند) */
    function es_logo_url(): string {
        if (function_exists('has_custom_logo') && has_custom_logo() && function_exists('get_custom_logo')) {
            $html = (string) get_custom_logo();
            if (preg_match('/src="([^"]+)"/', $html, $m) && $m[1] !== '') {
                return (string) $m[1];
            }
        }
        $option = es_option('header.logo', 'assets/images/ese-logo.svg');
        // مقدار placeholder پیش‌فرض (فاز ۲) لوگوی واقعی نیست — میرور باکس متنی ESE دارد
        if (is_string($option) && $option !== '' && $option !== 'assets/images/ese-logo.svg') {
            return $option;
        }
        return '';
    }
}

if (!function_exists('es_logo_alt')) {
    /** متن alt لوگو */
    function es_logo_alt(): string {
        $alt = es_option('header.logo_alt', '');
        return is_string($alt) && $alt !== '' ? $alt : (function_exists('get_bloginfo') ? get_bloginfo('name') : 'عرفان صنعت اصفهان');
    }
}

if (!function_exists('es_cart_badge')) {
    /**
     * شمارنده سبد هدر: تعداد ووکامرس (ارقام فارسی) فقط وقتی WC فعال و سبد موجود؛ وگرنه مقدار میرور (۲).
     */
    function es_cart_badge(): string {
        if (function_exists('WC')) {
            $cart = \WC();
            if (is_object($cart) && method_exists($cart, 'get_cart_contents_count')) {
                return es_fa_num((int) $cart->get_cart_contents_count());
            }
        }
        return '۲';
    }
}

if (!function_exists('es_cta_text')) {
    /** متن دکمه CTA هدر — fallback میرور: استعلام قیمت */
    function es_cta_text(): string {
        $text = es_option('header.cta_text', 'استعلام قیمت');
        return is_string($text) && $text !== '' ? $text : 'استعلام قیمت';
    }
}

if (!function_exists('es_cta_url')) {
    /** لینک دکمه CTA هدر — fallback میرور: صفحه تماس */
    function es_cta_url(): string {
        $url = es_option('header.cta_url', '');
        return is_string($url) && $url !== '' ? $url : es_page_url('contact');
    }
}
