<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Columns;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class ProjectColumns implements ServiceInterface
{
    public function register(): void {
        add_filter('manage_projects_posts_columns', [$this,'columns']);
        add_action('manage_projects_posts_custom_column', [$this,'renderColumn'], 10, 2);
        add_action('restrict_manage_posts', [$this,'filters']);
    }
    /** @param array<string,string> $cols @return array<string,string> */
    public function columns(array $cols): array {
        $out=[]; foreach($cols as $k=>$v){ $out[$k]=$v; if($k==='title'){ $out['es_thumb']='تصویر'; $out['city']='شهر'; $out['project_type']='نوع پروژه'; } } return $out;
    }
    public function renderColumn(string $col, int $postId): void {
        if($col==='es_thumb'){ echo has_post_thumbnail($postId)?get_the_post_thumbnail($postId,[48,48]):'—'; return; }
        if(in_array($col,['city','project_type'],true)){ $terms=wp_get_post_terms($postId,$col,['fields'=>'names']); echo esc_html(is_array($terms)&&$terms!==[]?implode('، ',$terms):'—'); }
    }
    public function filters(string $postType): void {
        if($postType!=='projects') return;
        foreach(['city'=>'همه شهرها','project_type'=>'همه انواع پروژه'] as $tax=>$label){
            $selected=isset($_GET[$tax])?sanitize_text_field(wp_unslash($_GET[$tax])):''; // phpcs:ignore
            wp_dropdown_categories(['taxonomy'=>$tax,'name'=>$tax,'hide_empty'=>false,'show_option_all'=>$label,'selected'=>$selected,'value_field'=>'slug']);
        }
    }
}
