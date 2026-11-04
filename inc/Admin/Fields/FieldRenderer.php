<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Fields;
final class FieldRenderer
{
    /** @param array{key:string,label:string,type?:string,description?:string,options?:array<string,string>,min?:int,max?:int} $field */
    public static function render(array $field, mixed $value): string {
        $key=(string)$field['key']; $type=(string)($field['type']??'text'); $name='options['.$key.']'; $id='es-field-'.$key;
        $html='<div class="es-field" data-es-field="'.esc_attr($key).'"><label for="'.esc_attr($id).'">'.esc_html((string)$field['label']).'</label>';
        switch($type){
            case 'textarea': case 'code':
                $codeAttributes = $type === 'code' ? ' class="es-code-editor" dir="ltr" spellcheck="false"' : '';
                $html.='<textarea id="'.esc_attr($id).'" name="'.esc_attr($name).'" rows="4"'.$codeAttributes.'>'.esc_textarea(is_scalar($value)?(string)$value:'').'</textarea>';
                break;
            case 'select': $html.='<select id="'.esc_attr($id).'" name="'.esc_attr($name).'">'; foreach(($field['options']??[]) as $v=>$label){ $html.='<option value="'.esc_attr((string)$v).'"'.selected((string)$value,(string)$v,false).'>'.esc_html((string)$label).'</option>'; } $html.='</select>'; break;
            case 'toggle': case 'checkbox': $html.='<input type="hidden" name="'.esc_attr($name).'" value="0"><input type="checkbox" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="1"'.checked((bool)$value,true,false).'>'; break;
            case 'number':
                $min = isset($field['min']) && is_numeric($field['min']) ? ' min="' . esc_attr((string) $field['min']) . '"' : '';
                $max = isset($field['max']) && is_numeric($field['max']) ? ' max="' . esc_attr((string) $field['max']) . '"' : '';
                $html.='<input type="number" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr(is_scalar($value)?(string)$value:'0'). '"' . $min . $max . '>';
                break;
            case 'color': $html.='<input type="color" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr(is_scalar($value)?(string)$value:'#000000').'">'; break;
            case 'email': $html.='<input type="email" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr(is_scalar($value)?(string)$value:'').'">'; break;
            case 'url': $html.='<input type="url" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr(is_scalar($value)?(string)$value:'').'">'; break;
            default: $html.='<input type="text" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr(is_scalar($value)?(string)$value:'').'">';
        }
        if(!empty($field['description'])) $html.='<p class="description">'.esc_html((string)$field['description']).'</p>';
        return $html.'</div>';
    }
    /** @param array<int,array<string,mixed>> $fields @param array<string,mixed> $values */
    public static function renderFields(array $fields, array $values): string { $out=''; foreach($fields as $f) $out.=self::render($f,$values[$f['key']]??null); return $out; }
}
