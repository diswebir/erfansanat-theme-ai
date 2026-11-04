<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Fields;
final class MediaField
{
    public static function render(string $key, string $label, int $attachmentId): string {
        $url=$attachmentId>0?(string)wp_get_attachment_url($attachmentId):'';
        return '<div class="es-field es-media-field" data-es-media-field="'.esc_attr($key).'"><label>'.esc_html($label).'</label>'
        .'<input type="hidden" name="options['.esc_attr($key).']" value="'.esc_attr((string)$attachmentId).'">'
        .($url!==''?'<img class="es-media-preview" src="'.esc_url($url).'" alt="" style="max-width:160px;display:block">':'')
        .'<button type="button" class="button" data-es-media-select="'.esc_attr($key).'">'.esc_html__('انتخاب رسانه','erfansanat').'</button> '
        .'<button type="button" class="button" data-es-media-remove="'.esc_attr($key).'">'.esc_html__('حذف','erfansanat').'</button></div>';
    }
}
