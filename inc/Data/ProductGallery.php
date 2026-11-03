<?php
/**
 * گالری محصول — خواندن چند-منبعی و همگام‌سازی با گالری استاندارد ووکامرس
 *
 * ترتیب منابع (بلوپرینت §۴):
 *   ۱) galerry_produ      (رشته کاما — املای تاریخی دیتابیس)
 *   ۲) _product_image_gallery (استاندارد ووکامرس)
 *   ۳) gallery-filed      (کلید قدیمی)
 *   ۴) image-2 … image-6  (کلیدهای قدیمی تکی)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Data;

final class ProductGallery {

    /** منابع گالری به ترتیب اولویت */
    public static function sources(): array {
        return ['galerry_produ', '_product_image_gallery', 'gallery-filed', 'image-2', 'image-3', 'image-4', 'image-5', 'image-6'];
    }

    /**
     * نرمال‌سازی ورودی → لیست int آیدی
     *
     * @param string|array $raw رشته کاما یا آرایه
     */
    public static function normalize(mixed $raw): array {
        if (is_array($raw)) {
            $parts = $raw;
        } elseif (is_string($raw) && $raw !== '') {
            $parts = explode(',', $raw);
        } else {
            return [];
        }
        $out = [];
        foreach ($parts as $part) {
            $part = is_scalar($part) ? trim((string) $part) : '';
            // فقط ارقام مثبت (منفی/متن دور ریخته می‌شود)
            if ($part !== '' && ctype_digit($part) && (int) $part > 0) {
                $out[] = (int) $part;
            }
        }
        return $out;
    }

    /**
     * ادغام همه منابع به ترتیب اولویت (dedup، حفظ ترتیب)
     *
     * @return list<int>
     */
    public static function get_ids(int $product_id): array {
        $out = [];
        foreach (self::sources() as $key) {
            $raw = (string) get_post_meta($product_id, $key, true);
            foreach (self::normalize($raw) as $id) {
                if (!in_array($id, $out, true)) {
                    $out[] = $id;
                }
            }
        }
        return $out;
    }

    /**
     * همگام‌سازی: آیدی‌های نهایی → galerry_produ (رشته کاما) + گالری استاندارد ووکامرس
     *
     * @param list<int> $ids
     */
    public static function sync(int $product_id, array $ids): bool {
        $ids  = array_values(array_unique(array_map('absint', $ids)));
        $ids  = array_values(array_filter($ids, static fn(int $id): bool => $id > 0));
        $text = implode(',', $ids);
        update_post_meta($product_id, 'galerry_produ', $text);
        update_post_meta($product_id, '_product_image_gallery', $text);
        return true;
    }
}
