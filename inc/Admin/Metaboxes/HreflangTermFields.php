<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Metaboxes;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class HreflangTermFields implements ServiceInterface
{
    /** @return array<int,string> */
    public static function taxonomies(): array { return ['category','product_cat','product_tag','product_brand','project_type','city']; }
    public function register(): void {
        foreach(self::taxonomies() as $tax){
            add_action($tax.'_add_form_fields', static function() use ($tax): void { self::renderFields(0); });
            add_action($tax.'_edit_form_fields', static function(\WP_Term $term): void { self::renderFields((int)$term->term_id); });
            add_action('created_'.$tax, [self::class,'saveTerm']);
            add_action('edited_'.$tax, [self::class,'saveTerm']);
        }
    }
    public static function renderFields(int $termId): void {
        foreach(HreflangMetabox::fields() as $k=>$label){ $v=$termId>0?(string)get_term_meta($termId,$k,true):''; printf('<div class="form-field"><label>%s</label><input type="text" dir="ltr" name="es_hreflang_term[%s]" value="%s"></div>',esc_html($label),esc_attr($k),esc_attr($v)); }
    }
    public static function saveTerm(int $termId): void {
        if(!current_user_can('manage_options')&&!current_user_can('manage_categories')) return;
        $raw=isset($_POST['es_hreflang_term'])&&is_array($_POST['es_hreflang_term'])?wp_unslash($_POST['es_hreflang_term']):[]; // phpcs:ignore
        self::saveValues($termId,$raw);
    }
    /** @param array<string,mixed> $raw */
    public static function saveValues(int $termId, array $raw): void { foreach(array_keys(HreflangMetabox::fields()) as $k) if(array_key_exists($k,$raw)) update_term_meta($termId,$k,$k==='same_key_hrefl'?sanitize_text_field((string)$raw[$k]):esc_url_raw((string)$raw[$k])); }
}
