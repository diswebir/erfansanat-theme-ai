<?php
/**
 * موتور فیلدهای تکرارشونده سازگار با دیتای JetEngine — دو-مد (serial/JSON)
 *
 * ⚠️ قانون حیاتی: فرمت داده موجود هرگز تغییر نمی‌کند —
 *   اگر دیتای فعلی JSON باشد، با کلیدهای item-N و JSON ذخیره می‌شود؛
 *   اگر سریالایز PHP باشد، سریالایز می‌ماند؛ اگر آرایه خام باشد، آرایه.
 *
 * ساختار ردیف فعلی دیتابیس: {"item-0": {"sub_key": "value"}, "item-1": {…}}
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Data;

final class RepeaterEngine {

    public const FORMAT_JSON   = 'json';
    public const FORMAT_SERIAL = 'serial';
    public const FORMAT_ARRAY  = 'array';
    public const FORMAT_EMPTY  = 'empty';

    /**
     * خواندن ردیف‌های متای پست + تشخیص فرمت
     *
     * @return array{format:string, rows:list<array>}
     */
    public static function read(int $post_id, string $key): array {
        $raw = get_post_meta($post_id, $key, true);
        return self::parse($raw);
    }

    /**
     * خواندن ردیف‌های متای ترم
     *
     * @return array{format:string, rows:list<array>}
     */
    public static function read_term(int $term_id, string $key): array {
        $raw = get_term_meta($term_id, $key, true);
        return self::parse($raw);
    }

    /** پارس مقدار خام → فرمت + ردیف‌های نرمال‌شده */
    public static function parse(mixed $raw): array {
        if ($raw === null || $raw === '' || $raw === false || $raw === []) {
            return ['format' => self::FORMAT_EMPTY, 'rows' => []];
        }

        if (is_array($raw)) {
            return ['format' => self::FORMAT_ARRAY, 'rows' => self::normalize_rows($raw)];
        }

        if (is_string($raw)) {
            // ۱) JSON؟
            if (str_starts_with(ltrim($raw), '{') || str_starts_with(ltrim($raw), '[')) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return ['format' => self::FORMAT_JSON, 'rows' => self::normalize_rows($decoded)];
                }
            }
            // ۲) سریالایز PHP؟
            if (function_exists('is_serialized') && is_serialized($raw)) {
                $decoded = @unserialize($raw, ['allowed_classes' => false]);
                if (is_array($decoded)) {
                    return ['format' => self::FORMAT_SERIAL, 'rows' => self::normalize_rows($decoded)];
                }
            }
        }

        return ['format' => self::FORMAT_EMPTY, 'rows' => []];
    }

    /**
     * نرمال‌سازی: نگه‌داشتن ترتیب کلیدها، بازچینش به لیست عددی
     * (کلیدهای item-0/item-1/… یا هر کلید رشته‌ای پذیرفته می‌شود)
     */
    public static function normalize_rows(array $data): array {
        $rows = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $rows[] = $value;
            } elseif (is_scalar($value)) {
                $rows[] = ['value' => $value]; // ردیف اسکالر → در کانتینر value
            }
        }
        return $rows;
    }

    /**
     * نوشتن ردیف‌ها با همان فرمت اصلی
     *
     * @param list<array> $rows
     * @param string|null $format null → آرایه (رفتار پیش‌فرض JetEngine)
     */
    public static function write(int $post_id, string $key, array $rows, ?string $format = null): bool {
        $storage = self::to_storage($rows, $format);
        return (bool) update_post_meta($post_id, $key, $storage);
    }

    /** نوشتن روی ترم با حفظ فرمت */
    public static function write_term(int $term_id, string $key, array $rows, ?string $format = null): bool {
        $storage = self::to_storage($rows, $format);
        return (bool) update_term_meta($term_id, $key, $storage);
    }

    /** تبدیل ردیف‌ها به ساختار ذخیره‌سازی بر اساس فرمت */
    public static function to_storage(array $rows, ?string $format = null): array|string {
        $format = $format ?: self::FORMAT_ARRAY;
        $assoc  = self::to_assoc($rows);

        return match ($format) {
            self::FORMAT_JSON   => json_encode($assoc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            self::FORMAT_SERIAL => serialize($assoc),
            self::FORMAT_ARRAY  => $assoc,
            default             => $assoc,
        };
    }

    /** بازچینش به کلیدهای item-0/item-1/… حفظ ترتیب */
    public static function to_assoc(array $rows): array {
        $assoc = [];
        foreach (array_values($rows) as $i => $row) {
            $assoc['item-' . $i] = $row;
        }
        return $assoc;
    }
}
