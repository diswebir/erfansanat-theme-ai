<?php
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin\Fields;

final class RepeaterField
{
    /** @param array<int,array{key:string,label:string}> $subFields @param array<int,array<string,mixed>> $items */
    public static function render(string $key, string $label, array $subFields, array $items): string
    {
        $items = array_values($items);
        $nextIndex = count($items);
        $html = '<div class="es-field es-repeater" data-es-repeater="' . esc_attr($key) . '" data-es-repeater-next-index="' . esc_attr((string) $nextIndex) . '">';
        $html .= '<div class="es-repeater__heading"><span class="es-field__label">' . esc_html($label) . '</span><span class="es-repeater__count" role="status" aria-live="polite" aria-atomic="true" data-es-repeater-count>' . esc_html((string) count($items)) . '</span></div>';

        foreach ($items as $index => $item) {
            $html .= self::renderItem($key, $subFields, $item, (string) $index);
        }

        $html .= '<template data-es-repeater-template>' . self::renderItem($key, $subFields, [], '__INDEX__') . '</template>';
        $html .= '<button type="button" class="button" data-es-repeater-add>' . esc_html__('افزودن مورد', 'erfansanat') . '</button>';
        $html .= '</div>';

        return $html;
    }

    /** @param array<int,array{key:string,label:string}> $subFields @param array<string,mixed> $item */
    private static function renderItem(string $key, array $subFields, array $item, string $index): string
    {
        $html = '<div class="es-repeater-item" data-es-repeater-item data-es-repeater-index="' . esc_attr($index) . '">';
        foreach ($subFields as $subField) {
            $fieldKey = (string) $subField['key'];
            $name = 'options[' . $key . '][' . $index . '][' . $fieldKey . ']';
            $value = $item[$fieldKey] ?? '';
            $html .= '<label>' . esc_html((string) $subField['label'])
                . '<input type="text" name="' . esc_attr($name) . '" value="' . esc_attr(is_scalar($value) ? (string) $value : '') . '"></label>';
        }
        $html .= '<div class="es-repeater-item__controls">'
            . '<button type="button" class="button es-repeater-move" data-es-repeater-move-up aria-label="' . esc_attr__('انتقال مورد به بالا', 'erfansanat') . '" title="' . esc_attr__('انتقال به بالا', 'erfansanat') . '">↑</button>'
            . '<button type="button" class="button es-repeater-move" data-es-repeater-move-down aria-label="' . esc_attr__('انتقال مورد به پایین', 'erfansanat') . '" title="' . esc_attr__('انتقال به پایین', 'erfansanat') . '">↓</button>'
            . '<button type="button" class="button es-repeater-remove" data-es-repeater-remove aria-label="' . esc_attr__('حذف این مورد', 'erfansanat') . '">' . esc_html__('حذف', 'erfansanat') . '</button></div></div>';

        return $html;
    }
}
