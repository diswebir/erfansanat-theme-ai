<?php
/**
 * موتور رندر فیلدهای داشبورد — ۹ نوع فیلد + Repeater با نام‌گذاری تودرتو
 *
 * خروجی کاملاً escaping شده؛ نام‌ها به فرمت PHP nesting (opt[a][b]) تولید می‌شوند
 * تا FormData بومی مرورگر ساختار آرایه‌ای را به سرور برساند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

final class FieldRenderer {

    /**
     * رندر یک فیلد
     *
     * @param array{key:string,type:string,label?:string,help?:string,default?:mixed,value?:mixed,options?:array,placeholder?:string,min?:float,max?:float,fields?:array,name?:string,add_label?:string} $field
     * @param mixed $value مقدار جاری؛ null → value/default اسکیم
     * @param string $prefix پیشوند نام (opt)
     */
    public static function render(array $field, mixed $value = null, string $prefix = 'opt'): string {
        if (!isset($field['key'], $field['type'])) {
            return '';
        }

        $type  = (string) $field['type'];
        $key   = (string) $field['key'];
        $name  = (string) ($field['name'] ?? $prefix . '[' . $key . ']');
        $id    = 'es-f-' . substr(md5($name), 0, 10);
        $label = (string) ($field['label'] ?? $key);
        $help  = (string) ($field['help'] ?? '');

        if ($value === null) {
            $value = array_key_exists('value', $field) ? $field['value'] : ($field['default'] ?? '');
        }

        $html  = '<div class="es-field es-field--' . esc_attr($type) . '">';
        $html .= '<label class="es-field__label" for="' . esc_attr($id) . '">' . esc_html($label) . '</label>';
        $html .= '<div class="es-field__control">' . self::control($type, $name, $id, $value, $field) . '</div>';
        if ($help !== '') {
            $html .= '<p class="es-field__help">' . esc_html($help) . '</p>';
        }
        $html .= '</div>';

        return $html;
    }

    private static function control(string $type, string $name, string $id, mixed $value, array $field): string {
        return match ($type) {
            'text', 'url' => self::text_control($type, $name, $id, $value, $field),
            'number'      => self::number_control($name, $id, $value, $field),
            'textarea'    => self::textarea_control($name, $id, $value, $field),
            'color'       => self::color_control($name, $id, $value, $field),
            'switch'      => self::switch_control($name, $id, $value, $field),
            'select'      => self::select_control($name, $id, $value, $field),
            'media'       => self::media_control($name, $id, $value, $field),
            'repeater'    => self::repeater_control($name, $value, $field),
            default       => self::text_control('text', $name, $id, $value, $field),
        };
    }

    private static function text_control(string $type, string $name, string $id, mixed $value, array $field): string {
        return sprintf(
            '<input type="%1$s" class="es-input" id="%2$s" name="%3$s" value="%4$s"%5$s>',
            esc_attr($type),
            esc_attr($id),
            esc_attr($name),
            esc_attr((string) $value),
            isset($field['placeholder']) ? ' placeholder="' . esc_attr((string) $field['placeholder']) . '"' : ''
        );
    }

    private static function number_control(string $name, string $id, mixed $value, array $field): string {
        $attrs = '';
        if (isset($field['min'])) {
            $attrs .= ' min="' . esc_attr((string) $field['min']) . '"';
        }
        if (isset($field['max'])) {
            $attrs .= ' max="' . esc_attr((string) $field['max']) . '"';
        }
        return sprintf(
            '<input type="number" class="es-input es-input--number" id="%s" name="%s" value="%s"%s>',
            esc_attr($id),
            esc_attr($name),
            esc_attr((string) $value),
            $attrs
        );
    }

    private static function textarea_control(string $name, string $id, mixed $value, array $field): string {
        return sprintf(
            '<textarea class="es-input es-input--textarea" id="%s" name="%s" rows="5"%s>%s</textarea>',
            esc_attr($id),
            esc_attr($name),
            isset($field['placeholder']) ? ' placeholder="' . esc_attr((string) $field['placeholder']) . '"' : '',
            esc_textarea((string) $value)
        );
    }

    private static function color_control(string $name, string $id, mixed $value, array $field): string {
        $hex = (string) ($value ?: '#000000');
        return sprintf(
            '<span class="es-color"><input type="color" class="es-color__picker" id="%1$s" name="%2$s" value="%3$s" data-es-color><code class="es-color__code" data-es-color-code>%3$s</code></span>',
            esc_attr($id),
            esc_attr($name),
            esc_attr($hex)
        );
    }

    private static function switch_control(string $name, string $id, mixed $value, array $field): string {
        $checked = (bool) $value;
        return sprintf(
            '<input type="hidden" name="%1$s" value="0"><input type="checkbox" class="es-switch" id="%2$s" name="%1$s" value="1"%3$s>',
            esc_attr($name),
            esc_attr($id),
            $checked ? ' checked' : ''
        );
    }

    private static function select_control(string $name, string $id, mixed $value, array $field): string {
        $options = (array) ($field['options'] ?? []);
        $html    = sprintf('<select class="es-input es-input--select" id="%s" name="%s">', esc_attr($id), esc_attr($name));
        foreach ($options as $opt_value => $opt_label) {
            $html .= sprintf(
                '<option value="%s"%s>%s</option>',
                esc_attr((string) $opt_value),
                ((string) $value === (string) $opt_value) ? ' selected' : '',
                esc_html((string) $opt_label)
            );
        }
        return $html . '</select>';
    }

    private static function media_control(string $name, string $id, mixed $value, array $field): string {
        $src = '';
        if (is_numeric($value) && (int) $value > 0 && function_exists('wp_get_attachment_url')) {
            $src = (string) wp_get_attachment_url((int) $value);
        } elseif (is_string($value) && str_starts_with($value, 'http')) {
            $src = $value;
        }

        $html  = '<div class="es-media" data-es-media>';
        $html .= sprintf('<input type="hidden" class="es-media__value" id="%s" name="%s" value="%s" data-es-media-value>', esc_attr($id), esc_attr($name), esc_attr((string) $value));
        $html .= '<div class="es-media__preview-wrap"><img class="es-media__preview" src="' . esc_url($src) . '" alt="" data-es-media-preview' . ($src === '' ? ' hidden' : '') . '></div>';
        $html .= '<div class="es-media__actions">';
        $html .= '<button type="button" class="button" data-es-media-select>' . esc_html__('انتخاب رسانه', 'erfansanat') . '</button>';
        $html .= '<button type="button" class="button-link" data-es-media-clear>' . esc_html__('حذف', 'erfansanat') . '</button>';
        $html .= '</div></div>';

        return $html;
    }

    private static function repeater_control(string $name, mixed $value, array $field): string {
        $subfields = (array) ($field['fields'] ?? []);
        $rows      = is_array($value) ? array_values($value) : [];
        $add_label = (string) ($field['add_label'] ?? __('افزودن ردیف', 'erfansanat'));

        $html  = '<div class="es-repeater" data-es-repeater>';
        $html .= '<div class="es-repeater__rows" data-es-repeater-rows>';

        foreach ($rows as $row_index => $row) {
            $html .= self::repeater_row($name, $subfields, is_array($row) ? $row : [], (int) $row_index);
        }
        $html .= '</div>';
        $html .= '<button type="button" class="button es-repeater__add" data-es-repeater-add data-name="' . esc_attr($name) . '">' . esc_html($add_label) . '</button>';
        $html .= '</div>';

        return $html;
    }

    private static function repeater_row(string $name, array $subfields, array $row, int $index): string {
        $html  = '<div class="es-repeater__row" data-es-repeater-row data-index="' . esc_attr((string) $index) . '">';
        $html .= '<div class="es-repeater__row-head"><span class="es-repeater__drag" data-es-repeater-move title="' . esc_attr__('جابه‌جایی', 'erfansanat') . '">⠿</span>';
        $html .= '<span class="es-repeater__row-num">' . esc_html((string) ($index + 1)) . '</span>';
        $html .= '<button type="button" class="es-repeater__remove" data-es-repeater-remove title="' . esc_attr__('حذف ردیف', 'erfansanat') . '">✕</button></div>';
        $html .= '<div class="es-repeater__row-fields">';

        foreach ($subfields as $sub) {
            if (!is_array($sub) || !isset($sub['key'])) {
                continue;
            }
            $sub_field           = $sub;
            $sub_field['name']   = sprintf('%s[%d][%s]', $name, $index, (string) $sub['key']);
            $sub_field['value']  = $row[$sub['key']] ?? ($sub['default'] ?? '');
            $html               .= self::render($sub_field, $sub_field['value'], '__raw__');
        }

        $html .= '</div></div>';

        return $html;
    }
}
