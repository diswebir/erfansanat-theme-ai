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
}
