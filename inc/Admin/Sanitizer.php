<?php
/**
 * پاکسازی امن مقادیر داشبورد — به ازای نوع هر فیلد اسکیم
 *
 * اصل: فقط کلیدهای شناخته‌شده اسکیم ذخیره می‌شوند؛ ورودی ناشناخته دور ریخته می‌شود.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

final class Sanitizer {

    /** پاکسازی کل پیلود بر اساس اسکیم فیلدها (فقط کلیدهای مجاز) */
    public static function sanitize_fields(array $fields_schema, array $payload): array {
        $out = [];
        foreach ($fields_schema as $field) {
            if (!is_array($field) || !isset($field['key'], $field['type'])) {
                continue;
            }
            $key = (string) $field['key'];
            if (!array_key_exists($key, $payload)) {
                continue; // کلید ارسال‌نشده → ذخیره نمی‌شود (مقدار پیش‌فرض از اسکیم می‌آید)
            }
            $out[$key] = self::field($field, $payload[$key]);
        }
        return $out;
    }

    /** پاکسازی یک مقدار بر اساس نوع فیلد */
    public static function field(array $field, mixed $value): mixed {
        $type = (string) ($field['type'] ?? 'text');

        return match ($type) {
            'text', 'textarea' => self::text($value, $type === 'textarea'),
            'url'       => esc_url_raw((string) (is_scalar($value) ? $value : '')),
            'number'    => self::number($value, $field),
            'color'     => sanitize_hex_color(trim((string) (is_scalar($value) ? $value : ''))) ?? '',
            'switch'    => (is_bool($value) || is_int($value)) ? (int) (bool) $value : ((string) $value === '1' ? 1 : 0),
            'select'    => self::select($value, $field),
            'media'     => self::media($value),
            'repeater'  => self::repeater($field, $value),
            default     => sanitize_text_field((string) (is_scalar($value) ? $value : '')),
        };
    }

    private static function text(mixed $value, bool $rich): string {
        $value = is_scalar($value) ? (string) $value : '';
        return $rich ? wp_kses_post($value) : sanitize_text_field($value);
    }

    private static function number(mixed $value, array $field): int {
        $num = (int) $value;
        if (isset($field['min']) && $num < (int) $field['min']) {
            $num = (int) $field['min'];
        }
        if (isset($field['max']) && $num > (int) $field['max']) {
            $num = (int) $field['max'];
        }
        return $num;
    }

    private static function select(mixed $value, array $field): string {
        $value   = (string) $value;
        $allowed = array_map('strval', array_keys((array) ($field['options'] ?? [])));
        return in_array($value, $allowed, true) ? $value : (string) ($field['default'] ?? '');
    }

    private static function media(mixed $value): string|int {
        $value = is_scalar($value) ? (string) $value : '';
        if ($value === '') {
            return '';
        }
        // مقدار عددی = آیدی رسانه؛ رشته http = آدرس فایل (مثل کاتالوگ PDF)
        if (ctype_digit($value)) {
            return (int) $value;
        }
        return esc_url_raw($value);
    }

    /** Repeater بازگشتی — نرمال‌سازی ردیف‌ها و sanitize زیرفیلدها */
    private static function repeater(array $field, mixed $value): array {
        $subfields = (array) ($field['fields'] ?? []);
        if ($subfields === [] || !is_array($value)) {
            return [];
        }

        $rows = array_values($value);
        $out  = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $clean_row = [];
            $has_data  = false;
            foreach ($subfields as $sub) {
                if (!is_array($sub) || !isset($sub['key'], $sub['type'])) {
                    continue;
                }
                $sub_key = (string) $sub['key'];
                if (!array_key_exists($sub_key, $row)) {
                    continue;
                }
                $clean_row[$sub_key] = self::field($sub, $row[$sub_key]);
                if ($clean_row[$sub_key] !== '' && $clean_row[$sub_key] !== 0 && $clean_row[$sub_key] !== []) {
                    $has_data = true;
                }
            }
            // ردیف کاملاً خالی دور ریخته می‌شود
            if ($has_data) {
                $out[] = $clean_row;
            }
        }
        return $out;
    }

    /**
     * اسکریپت‌های سفارشی head/footer — فقط برای کاربر با unfiltered_html؛
     * در نبود مجوز، مقدار قبلی حفظ می‌شود (بدون تغییر).
     */
    public static function sanitize_custom_scripts(array $new, array $old): array {
        $keys   = ['head_js', 'footer_js'];
        $can_raw = current_user_can('unfiltered_html');
        $out    = [];

        foreach ($keys as $key) {
            $out[$key] = '';
            if (isset($new[$key])) {
                $out[$key] = $can_raw ? wp_check_invalid_utf8((string) $new[$key], true) : '';
            }
            if (!$can_raw) {
                $out[$key] = (string) ($old[$key] ?? '');
            }
        }

        return $out;
    }
}
