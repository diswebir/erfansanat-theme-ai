<?php
/**
 * مدیریت دارایی‌های فرانت — Enqueue شرطی با نسخه‌گذاری filemtime و Preload فونت
 *
 * نگاشت قالب→دارایی بر اساس temp/STATIC_TEMPLATE_MAP.md؛
 * CSS/JS میرور سراسری است و دارایی‌های اختصاصی هر فاز از نقشه فیلتر es_asset_map می‌آید.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

final class AssetManager {

    private const HANDLE = 'erfansanat-main';

    public static function init(): void {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue']);
        add_action('wp_head', [self::class, 'preload_fonts'], 1);
        add_filter('script_loader_tag', [self::class, 'add_defer_to_main_js'], 10, 3);
    }

    /**
     * نسخه کش‌باستینگ: mtime فایل؛ در نبود فایل، نسخه قالب
     *
     * @param string $relative_path مسیر نسبی از ریشه قالب مثل «assets/css/styles.css»
     */
    public static function version(string $relative_path): string {
        $file = ERFANSANAT_DIR . ltrim($relative_path, '/');
        if (is_file($file)) {
            $mtime = filemtime($file);
            if (is_int($mtime) && $mtime > 0) {
                return (string) $mtime;
            }
        }
        return ERFANSANAT_VERSION;
    }

    /**
     * نقشه دارایی‌های شرطی قالب→فایل (بر اساس STATIC_TEMPLATE_MAP).
     * فازهای بعدی (۵ تا ۸) دارایی‌های خود را با فیلتر es_asset_map اضافه می‌کنند.
     *
     * @return array<string, array{css?: string, js?: string}>
     */
    public static function assets_map(): array {
        $map = [
            // 'product'      => ['js' => 'assets/js/single-product.js'],
            // 'archive-project' => ['js' => 'assets/js/archive-projects.js'],
        ];

        /**
         * فیلتر es_asset_map — کلید = زمینه (context)، مقدار = مسیرهای نسبی دارایی
         */
        $map = apply_filters('es_asset_map', $map);

        return is_array($map) ? $map : [];
    }

    /** آیا زمینه جاری با شرط قالب وردپرس مطابقت دارد؟ */
    private static function is_context_active(string $context): bool {
        switch ($context) {
            case 'home':
                return function_exists('is_front_page') && (is_front_page() || (function_exists('is_home') && is_home()));
            case 'product':
                return function_exists('is_product') && is_product();
            case 'archive-product':
                return function_exists('is_woocommerce') && is_woocommerce();
            case 'projects':
                return function_exists('is_post_type_archive') && (is_post_type_archive('projects') || (function_exists('is_tax') && is_tax(['project_type', 'city'])));
            default:
                return (bool) apply_filters('es_asset_context_active', false, $context);
        }
    }

    public static function enqueue(): void {
        wp_enqueue_style(self::HANDLE, ERFANSANAT_URI . 'assets/css/styles.css', [], self::version('assets/css/styles.css'));

        // main.js میرور با defer در head لود می‌شود (مطابق میرور) — defer با فیلتر script_loader_tag اضافه می‌شود
        wp_enqueue_script(self::HANDLE, ERFANSANAT_URI . 'assets/js/main.js', [], self::version('assets/js/main.js'), false);

        wp_localize_script(self::HANDLE, 'ES', self::runtime_data());

        // دارایی‌های شرطی زمینه‌ها
        foreach (self::assets_map() as $context => $assets) {
            if (!self::is_context_active((string) $context) || !is_array($assets)) {
                continue;
            }
            foreach (['css', 'js'] as $type) {
                $relative = (string) ($assets[$type] ?? '');
                if ($relative === '') {
                    continue;
                }
                $handle   = self::HANDLE . '-' . sanitize_key((string) $context) . '-' . $type;
                $url      = ERFANSANAT_URI . ltrim($relative, '/');
                $ver      = self::version($relative);
                if ($type === 'css') {
                    wp_enqueue_style($handle, $url, [self::HANDLE], $ver);
                } else {
                    wp_enqueue_script($handle, $url, [self::HANDLE], $ver, true);
                }
            }
        }
    }

    /** داده‌های زمان اجرا برای JS قالب — nonce ایجکس در فاز ۸ استفاده می‌شود */
    private static function runtime_data(): array {
        $shop_url = home_url('/shop/');
        if (function_exists('wc_get_page_id')) {
            $shop_id = wc_get_page_id('shop');
            if (is_int($shop_id) && $shop_id > 0 && function_exists('get_permalink')) {
                $permalink = get_permalink($shop_id);
                if (is_string($permalink) && $permalink !== '') {
                    $shop_url = $permalink;
                }
            }
        }

        return [
            'ajaxUrl'      => admin_url('admin-ajax.php'),
            'nonce'        => wp_create_nonce('es_ajax'),
            'homeUrl'      => home_url('/'),
            'shopUrl'      => $shop_url,
            'themeVersion' => ERFANSANAT_VERSION,
        ];
    }

    /** Preload سه ساب‌ست فونت Vazirmatn (قبل از استایل، با crossorigin) */
    public static function preload_fonts(): void {
        foreach (['arabic', 'latin', 'latin-ext'] as $subset) {
            printf(
                '<link rel="preload" href="%1$s" as="font" type="font/woff2" crossorigin>' . "\n",
                esc_url(ERFANSANAT_URI . 'assets/fonts/vazirmatn-' . $subset . '.woff2')
            );
        }
    }

    /** افزودن defer به main.js (سازگار با WP قدیمی که strategy defer ندارد) */
    public static function add_defer_to_main_js(string $tag, string $handle, string $src): string {
        unset($src);
        if ($handle !== self::HANDLE) {
            return $tag;
        }
        if (stripos($tag, ' defer') !== false || stripos($tag, 'defer=') !== false) {
            return $tag;
        }
        return str_replace('<script ', '<script defer ', $tag);
    }
}
