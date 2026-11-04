<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Taxonomies;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class ProductBrandTaxonomy implements ServiceInterface
{
    public function register(): void { add_action('init', [$this,'registerTaxonomy'], 20); }
    public function registerTaxonomy(): void {
        if (!taxonomy_exists('product_brand')) register_taxonomy('product_brand',['product'],['labels'=>['name'=>'برند محصولات','singular_name'=>'برند'],'public'=>true,'hierarchical'=>true,'show_in_rest'=>true,'show_admin_column'=>true,'rewrite'=>['slug'=>'product_brand','with_front'=>false]]);
    }
}
