<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Metaboxes;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class ProductMetabox implements ServiceInterface
{
    /** @return array<int,class-string> */
    public static function groups(): array {
        return array_values(array_filter([ProductIdentityFields::class,ProductSalesFields::class,ProductRelationFields::class,ProductRepeaters::class], 'class_exists'));
    }
    public function register(): void { add_action('add_meta_boxes_product', [$this,'addMetabox']); add_action('save_post_product', [$this,'save'], 10, 2); }
    public function addMetabox(): void { add_meta_box('erfansanat-product','اطلاعات محصولات عرفان صنعت',[$this,'render'],'product','normal','high'); }
    public function render(\WP_Post $post): void {
        wp_nonce_field('erfansanat_save_product','es_product_nonce');
        echo '<div class="es-product-metabox" data-es-product-metabox>';
        foreach(self::groups() as $g){ echo '<section class="es-metabox-group" data-es-group="'.esc_attr($g).'">'; $g::render($post); echo '</section>'; }
        echo '</div>';
    }
    public function save(int $postId, \WP_Post $post): void {
        if(!isset($_POST['es_product_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['es_product_nonce'])),'erfansanat_save_product')) return;
        if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE) return;
        if(!current_user_can('edit_post',$postId)) return;
        $raw=isset($_POST['es_product'])&&is_array($_POST['es_product'])?wp_unslash($_POST['es_product']):[];
        self::saveGroups($postId,$raw);
    }
    /** @param array<string,mixed> $raw */
    public static function saveGroups(int $postId, array $raw): void { foreach(self::groups() as $g) $g::saveValues($postId,$raw); }
}
