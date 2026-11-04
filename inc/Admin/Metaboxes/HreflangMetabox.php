<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Metaboxes;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class HreflangMetabox implements ServiceInterface
{
    /** @return array<string,string> */
    public static function fields(): array { return ['hrefmeta_fa'=>'نشانی نسخه فارسی','hrefmeta_en'=>'نشانی نسخه انگلیسی','hrefmeta_ar'=>'نشانی نسخه عربی','same_key_hrefl'=>'کلید مشترک ترجمه']; }
    /** @return array<int,string> */
    public static function postTypes(): array { return ['post','page','product','projects']; }
    public function register(): void {
        foreach(self::postTypes() as $pt) add_action('add_meta_boxes_'.$pt, [$this,'addMetabox']);
        add_action('save_post', [$this,'save'], 20, 2);
        add_filter('manage_posts_columns', [$this,'column']); add_filter('manage_edit-post_sortable_columns', [$this,'sortable']);
    }
    public function addMetabox(): void { add_meta_box('erfansanat-hreflang','ترجمه‌های این محتوا (Hreflang)',[$this,'render'],self::postTypes(),'side','default'); }
    /** @param array<string,string> $cols @return array<string,string> */
    public function column(array $cols): array { $cols['same_key_hrefl']='کلید ترجمه'; return $cols; }
    /** @param array<string,string> $cols @return array<string,string> */
    public function sortable(array $cols): array { $cols['same_key_hrefl']='same_key_hrefl'; return $cols; }
    public function render(\WP_Post $post): void {
        wp_nonce_field('erfansanat_save_hreflang','es_hreflang_nonce');
        foreach(self::fields() as $k=>$label) printf('<p><label>%s<input type="text" class="widefat" dir="ltr" name="es_hreflang[%s]" value="%s"></label></p>',esc_html($label),esc_attr($k),esc_attr((string)get_post_meta($post->ID,$k,true)));
    }
    public function save(int $postId, \WP_Post $post): void {
        if(!in_array($post->post_type,self::postTypes(),true)) return;
        if(!isset($_POST['es_hreflang_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['es_hreflang_nonce'])),'erfansanat_save_hreflang')) return;
        if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE) return;
        if(!current_user_can('edit_post',$postId)) return;
        $raw=isset($_POST['es_hreflang'])&&is_array($_POST['es_hreflang'])?wp_unslash($_POST['es_hreflang']):[];
        self::saveValues($postId,$raw);
    }
    /** @param array<string,mixed> $raw */
    public static function saveValues(int $postId, array $raw): void { foreach(array_keys(self::fields()) as $k) if(array_key_exists($k,$raw)) update_post_meta($postId,$k,$k==='same_key_hrefl'?sanitize_text_field((string)$raw[$k]):esc_url_raw((string)$raw[$k])); }
}
