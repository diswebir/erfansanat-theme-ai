<?php
/**
 * راه‌اندازی ووکامرس — تنظیم خروجی‌های پیش‌فرض روی پوسته عرفان صنعت
 *
 * خروجی‌های تکراری ووکامرس (breadcrumb/سایدبار/related/عنوان پیش‌فرض) حذف می‌شوند؛
 * معادل‌های سفارشی میرور در قالب‌های اختصاصی (single-product.php، archive-product.php، …)
 * رندر می‌شوند. هیچ رفتار پرداختی ووکامرس تغییر نمی‌کند — فقط لایه نمایش.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Woo;

final class Setup {

    public static function init(): void {
        /**
         * این ماژول فقط پاکسازی لایه نمایش فرانت‌اند است؛ در ادمین/اجکس کاری نمی‌کند
         * (قرارداد فاز ۱: در ادمین هیچ remove_action/remove_filter از قالب اجرا نمی‌شود).
         */
        if (function_exists('is_admin') && is_admin()) {
            return;
        }

        /**
         * حذف خروجی‌های پیش‌فرض ووکامرس — breadcrumb سفارشی میرور در فاز ۶ رندر می‌شود.
         */
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

        /**
         * عنوان پیش‌فرض آرشیو («دسته محصول» و …) خاموش — هیرو سفارشی میرور جایگزین است.
         */
        add_filter('woocommerce_show_page_title', [self::class, 'hide_page_title'], 20);

        /**
         * استایل‌های پیش‌فرض ووکامرس با CSS کامل قالب تداخل دارند؛
         * فقط blockui و select2 حفظ می‌شوند (رفتار ایجکس سبد و انتخاب‌های فرم).
         */
        add_filter('woocommerce_enqueue_styles', [self::class, 'filter_styles'], 20);

        /**
         * تعداد ستون‌های گرید آرشیو از داشبورد (آرشیوها → ستون‌های گرید محصولات).
         */
        add_filter('loop_shop_columns', [self::class, 'loop_columns'], 20);

        /**
         * خالی‌کردن سبد با پارامتر استاندارد ?empty-cart (دکمه بازنشانی میرور سبد).
         */
        add_action('template_redirect', [self::class, 'handle_empty_cart'], 10);

        /**
         * پوسته صفحات حساب کاربری (فقط این صفحات — سبک میرور).
         */
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_account_styles'], 20);
    }

    /**
     * استایل override صفحات my-account ووکامرس — فقط در همین صفحات بارگذاری می‌شود.
     */
    public static function enqueue_account_styles(): void {
        if (!function_exists('is_account_page') || !is_account_page()) {
            return;
        }
        $es_rel = 'assets/css/wc-forms-override.css';
        $es_abs = function_exists('get_theme_file_path') ? (string) get_theme_file_path($es_rel) : '';
        if ($es_abs !== '' && is_file($es_abs) && function_exists('wp_enqueue_style')) {
            wp_enqueue_style(
                'es-wc-forms-override',
                ERFANSANAT_URI . $es_rel,
                [],
                (string) filemtime($es_abs)
            );
        }
    }

    /**
     * نگه‌داشتن فقط استایل‌های کاربردی از فهرست پیش‌فرض ووکامرس.
     *
     * @param array<string, mixed> $styles فهرست استایل‌های WC_Frontend_Scripts::get_styles()
     * @return array<string, mixed>
     */
    public static function filter_styles(array $styles): array {
        $keep = ['blockui', 'select2'];
        $out  = [];
        foreach ($keep as $key) {
            if (array_key_exists($key, $styles)) {
                $out[$key] = $styles[$key];
            }
        }
        return $out;
    }

    /**
     * ستون‌های گرید آرشیو — آپشن داشبورد با کلمپ بازه معتبر ۲ تا ۴.
     */
    public static function loop_columns(int $columns): int {
        $raw = function_exists('es_option') ? es_option('archives.products_columns', 4) : $columns;
        $n   = (int) $raw;
        if ($n < 2 || $n > 4) {
            return 4;
        }
        return $n;
    }

    /**
     * خاموش‌کردن عنوان پیش‌فرض آرشیو ووکامرس.
     */
    public static function hide_page_title(bool $show): bool {
        return false;
    }

    /**
     * اتریبیوت‌های <main> شل ووکامرس بر اساس صفحه جاری — عین کلاس‌های میرور
     * (cart.html: bg-slate-50/50 + data-cart-page · checkout.html: data-checkout-page).
     */
    public static function main_attrs(): string {
        if (function_exists('is_cart') && is_cart()) {
            return ' class="bg-slate-50/50" data-cart-page="true"';
        }
        if (function_exists('is_checkout') && is_checkout()) {
            return ' class="bg-slate-50/50" data-checkout-page="true"';
        }
        if (function_exists('is_account_page') && is_account_page()) {
            return ' class="bg-slate-50/50" data-account-page="true"';
        }
        return '';
    }

    /**
     * خالی‌کردن سبد با پارامور استاندارد ?empty-cart=1 — دکمه «بازنشانی» میرور cart.html.
     */
    public static function handle_empty_cart(): void {
        if (!function_exists('is_cart') || !is_cart() || !isset($_GET['empty-cart'])) {
            return;
        }
        if (!function_exists('WC') || !(WC()->cart ?? null)) {
            return;
        }
        WC()->cart->empty_cart();
        if (function_exists('wc_add_notice')) {
            wc_add_notice(__('سبد خرید خالی شد.', 'erfansanat'), 'notice');
        }
        if (function_exists('wc_get_cart_url')) {
            wp_safe_redirect(wc_get_cart_url());
            exit;
        }
    }
}
