<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Metaboxes;
use ErfanSanat\Theme\Contracts\ServiceInterface;
use ErfanSanat\Theme\Helpers\LegacyMeta;
final class ProductCatTermFields implements ServiceInterface
{
    public function register(): void {
        add_action('product_cat_add_form_fields', [$this,'renderAdd']);
        add_action('product_cat_edit_form_fields', [$this,'renderEdit']);
        add_action('created_product_cat', [$this,'save']);
        add_action('edited_product_cat', [$this,'save']);
    }
    public function renderAdd(): void { $this->renderFields(0); }
    public function renderEdit(\WP_Term $term): void { $this->renderFields((int)$term->term_id); }
    private function renderFields(int $termId): void {
        $thumb=$termId>0?(int)get_term_meta($termId,'thumbnail',true):0;
        echo '<div class="form-field"><label>تصویر دسته (آیدی رسانه)</label><input type="number" name="es_term[thumbnail]" value="'.esc_attr((string)$thumb).'"></div>';
        $rows=$termId>0?LegacyMeta::parseRows(get_term_meta($termId,'faq',true)):[];
        if($rows===[]) $rows=[[]];
        echo '<div class="form-field"><label>سوالات متداول دسته</label>';
        foreach($rows as $i=>$r) printf('<p><input type="text" name="es_term[faq][%d][name_question]" value="%s" placeholder="سوال"><input type="text" name="es_term[faq][%d][answer_question]" value="%s" placeholder="پاسخ"></p>',$i,esc_attr((string)($r['name_question']??'')),$i,esc_attr((string)($r['answer_question']??'')));
        echo '</div>';
    }
    public function save(int $termId): void {
        if(!current_user_can('manage_product_terms')) { /* Woo capability may not exist without WooCommerce; fall back to manage_options */ if(!current_user_can('manage_options')) return; }
        $raw=isset($_POST['es_term'])&&is_array($_POST['es_term'])?wp_unslash($_POST['es_term']):[]; // phpcs:ignore
        self::saveValues($termId,$raw);
    }
    /** @param array<string,mixed> $raw */
    public static function saveValues(int $termId, array $raw): void {
        if(array_key_exists('thumbnail',$raw)) update_term_meta($termId,'thumbnail',absint($raw['thumbnail']));
        if(isset($raw['faq'])&&is_array($raw['faq'])){ $rows=[]; foreach($raw['faq'] as $r){ if(!is_array($r))continue; $row=['name_question'=>sanitize_text_field((string)($r['name_question']??'')),'answer_question'=>sanitize_textarea_field((string)($r['answer_question']??''))]; if($row['name_question']!==''||$row['answer_question']!=='') $rows[]=$row; } update_term_meta($termId,'faq',LegacyMeta::toJetEngineFormat($rows)); }
    }
}
