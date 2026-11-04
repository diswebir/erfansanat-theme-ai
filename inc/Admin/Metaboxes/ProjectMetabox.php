<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Metaboxes;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class ProjectMetabox implements ServiceInterface
{
    /** @return array<string,string> exact legacy keys (note foure-image typo) */
    public static function galleryKeys(): array { return ['second-image'=>'تصویر ۲','three-image'=>'تصویر ۳','foure-image'=>'تصویر ۴','five-image'=>'تصویر ۵','six-image'=>'تصویر ۶']; }
    public function register(): void { add_action('add_meta_boxes_projects', [$this,'addMetabox']); add_action('save_post_projects', [$this,'save'], 10, 2); }
    public function addMetabox(): void { add_meta_box('erfansanat-project','اطلاعات پروژه',[$this,'render'],'projects','normal','high'); }
    public function render(\WP_Post $post): void {
        wp_nonce_field('erfansanat_save_project','es_project_nonce');
        echo '<div data-es-project-metabox><h3>گالری پروژه</h3>';
        foreach(self::galleryKeys() as $k=>$label) printf('<p><label>%s (آیدی رسانه)<input type="number" class="widefat" name="es_project[%s]" value="%s"></label></p>',esc_html($label),esc_attr($k),esc_attr((string)(int)get_post_meta($post->ID,$k,true)));
        printf('<p><label>تاریخ تکمیل پروژه<input type="text" class="widefat" name="es_project[finish-pro-time]" value="%s" placeholder="1404/01/01"></label></p>',esc_attr((string)get_post_meta($post->ID,'finish-pro-time',true)));
        if(class_exists(ProjectProductsField::class)) ProjectProductsField::render($post);
        echo '</div>';
    }
    public function save(int $postId, \WP_Post $post): void {
        if(!isset($_POST['es_project_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['es_project_nonce'])),'erfansanat_save_project')) return;
        if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE) return;
        if(!current_user_can('edit_post',$postId)) return;
        $raw=isset($_POST['es_project'])&&is_array($_POST['es_project'])?wp_unslash($_POST['es_project']):[];
        self::saveValues($postId,$raw);
    }
    /** @param array<string,mixed> $raw */
    public static function saveValues(int $postId, array $raw): void {
        foreach(array_keys(self::galleryKeys()) as $k) if(array_key_exists($k,$raw)) update_post_meta($postId,$k,absint($raw[$k]));
        if(array_key_exists('finish-pro-time',$raw)) update_post_meta($postId,'finish-pro-time',sanitize_text_field((string)$raw['finish-pro-time']));
        if(class_exists(ProjectProductsField::class)) ProjectProductsField::saveValues($postId,$raw);
    }
    /** @return array<int,int> featured image + legacy gallery ids */
    public static function galleryIds(int $postId): array { $ids=[]; $thumb=(int)get_post_thumbnail_id($postId); if($thumb>0)$ids[]=$thumb; foreach(array_keys(self::galleryKeys()) as $k){ $id=(int)get_post_meta($postId,$k,true); if($id>0)$ids[]=$id; } return $ids; }
}
