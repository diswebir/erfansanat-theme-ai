<?php
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin\Fields;

final class MediaField
{
    public static function render(string $key, string $label, int $attachmentId): string
    {
        $url = $attachmentId > 0 ? (string) wp_get_attachment_url($attachmentId) : '';
        $preview = $url !== ''
            ? '<img class="es-media-preview" data-es-media-preview src="' . esc_url($url) . '" alt="' . esc_attr__('پیش‌نمایش رسانه انتخاب‌شده', 'erfansanat') . '">'
            : '';
        $labelId = 'es-media-label-' . sanitize_key($key);

        return '<div class="es-field es-media-field" data-es-media-field="' . esc_attr($key) . '">'
            . '<span class="es-field__label" id="' . esc_attr($labelId) . '">' . esc_html($label) . '</span>'
            . '<input type="hidden" data-es-media-value name="options[' . esc_attr($key) . ']" value="' . esc_attr((string) $attachmentId) . '">'
            . '<div class="es-media-preview-slot" data-es-media-preview-slot>' . $preview . '</div>'
            . '<div class="es-media-actions">'
            . '<button type="button" class="button" aria-describedby="' . esc_attr($labelId) . '" data-es-media-select="' . esc_attr($key) . '">' . esc_html__('انتخاب تصویر', 'erfansanat') . '</button>'
            . '<button type="button" class="button" aria-describedby="' . esc_attr($labelId) . '" data-es-media-remove="' . esc_attr($key) . '">' . esc_html__('حذف تصویر', 'erfansanat') . '</button>'
            . '</div></div>';
    }
}
