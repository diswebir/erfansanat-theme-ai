<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Taxonomies;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class ProjectTaxonomies implements ServiceInterface
{
    public function register(): void { add_action('init', [$this,'registerTaxonomies']); }
    public function registerTaxonomies(): void {
        if (!taxonomy_exists('project_type')) register_taxonomy('project_type',['projects'],['labels'=>['name'=>'نوع پروژه','singular_name'=>'نوع پروژه'],'public'=>true,'hierarchical'=>true,'show_in_rest'=>true,'show_admin_column'=>true,'rewrite'=>['slug'=>'project_type','with_front'=>false,'hierarchical'=>false]]);
        if (!taxonomy_exists('city')) register_taxonomy('city',['projects'],['labels'=>['name'=>'محل پروژه (شهر)','singular_name'=>'شهر'],'public'=>true,'hierarchical'=>true,'show_in_rest'=>true,'show_admin_column'=>true,'rewrite'=>['slug'=>'city','with_front'=>false,'hierarchical'=>false]]);
    }
}
