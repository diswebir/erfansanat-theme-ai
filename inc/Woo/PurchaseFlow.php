<?php
/**
 * جریان خرید — بستن خرید آنلاین برای محصولات استعلامی (true_sale=false)
 *
 * فیلترهای ووکامرس: is_purchasable → false و متن دکمه‌ها → «استعلام قیمت».
 * هیچ رفتار پرداختی دیگری تغییر نمی‌کند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Woo;

use ErfanSanat\Theme\Frontend\Purchasable;

final class PurchaseFlow {

    public static function init(): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        add_filter('woocommerce_is_purchasable', [self::class, 'is_purchasable'], 10, 2);
        add_filter('woocommerce_product_add_to_cart_text', [self::class, 'button_text'], 10, 2);
        add_filter('woocommerce_product_single_add_to_cart_text', [self::class, 'button_text'], 10, 2);
    }

    /** محصول استعلامی (true_sale=false) قابل خرید آنلاین نیست */
    public static function is_purchasable(bool $purchasable, object $product): bool {
        $pid = method_exists($product, 'get_id') ? (int) $product->get_id() : (int) ($product->ID ?? 0);
        if ($pid > 0 && !Purchasable::is_online($pid)) {
            return false;
        }
        return $purchasable;
    }

    /** متن دکمه آرشیو/سینگل برای استعلامی */
    public static function button_text(string $text, object $product): string {
        $pid = method_exists($product, 'get_id') ? (int) $product->get_id() : (int) ($product->ID ?? 0);
        if ($pid > 0 && !Purchasable::is_online($pid)) {
            return 'استعلام قیمت';
        }
        return $text;
    }
}
