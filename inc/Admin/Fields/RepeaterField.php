<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Fields;
final class RepeaterField
{
    /** @param array<int,array{key:string,label:string}> $subFields @param array<int,array<string,mixed>> $items */
    public static function render(string $key, string $label, array $subFields, array $items): string {
        $html='<div class="es-field es-repeater" data-es-repeater="'.esc_attr($key).'"><label>'.esc_html($label).'</label>';
        foreach(array_values($items) as $i=>$item){
            $html.='<div class="es-repeater-item" data-es-repeater-item>';
            foreach($subFields as $sub){ $n='options['.$key.']['.$i.']['.$sub['key'].']'; $v=$item[$sub['key']]??''; $html.='<label>'.esc_html($sub['label']).'<input type="text" name="'.esc_attr($n).'" value="'.esc_attr(is_scalar($v)?(string)$v:'').'"></label>'; }
            $html.='<button type="button" class="button" data-es-repeater-remove>'.esc_html__('حذف','erfansanat').'</button></div>';
        }
        return $html.'<button type="button" class="button" data-es-repeater-add="'.esc_attr($key).'">'.esc_html__('افزودن مورد','erfansanat').'</button></div>';
    }
}
