<?php
/**
 * منطق وضعیت خرید محصول — true_sale (آنلاین/استعلامی)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Frontend;

final class Purchasable {

    /** آیا محصول قابل خرید آنلاین است؟ (true_sale=false → استعلامی) */
    public static function is_online(int $product_id): bool {
        $true_sale = (string) get_post_meta($product_id, 'true_sale', true);
        return $true_sale !== 'false';
    }

    /** برچسب فارسی وضعیت */
    public static function label(int $product_id): string {
        return self::is_online($product_id) ? 'آنلاین' : 'استعلامی';
    }
}
