<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Metaboxes;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class ProductIdentityFields implements ServiceInterface
{
    /** @return array<string,string> */
    public static function fields(): array { return ['gs1_id'=>'شناسه بین‌المللی GS1 (GTIN)','national_code'=>'کد ملی کالا (ایران‌کد)','hs_id'=>'کد تعرفه گمرکی HS','isic_id'=>'کد صنعتی ISIC','cpc_id'=>'کد طبقه‌بندی CPC','product_id'=>'شناسه اختصاصی محصول']; }
    public function register(): void { add_action('quick_edit_custom_box', [$this,'quickEdit'], 10, 2); add_filter('manage_product_posts_columns', [$this,'columns']); }
    /** @param array<string,string> $cols @return array<string,string> */
    public function columns(array $cols): array { $cols['product_id']='شناسه محصول'; $cols['gs1_id']='GS1'; return $cols; }
    public function quickEdit(string $column, string $postType): void { if($postType!=='product'||!in_array($column,['product_id','gs1_id'],true)) return; echo '<fieldset class="inline-edit-col-right"><label>'.esc_html(self::fields()[$column]).'<input type="text" name="es_product['.esc_attr($column).']" value=""></label></fieldset>'; }
    public static function render(\WP_Post $post): void {
        echo '<h3>شناسه‌های صنعتی و قانونی</h3>';
        foreach(self::fields() as $k=>$label) printf('<p><label>%s<input type="text" class="widefat" name="es_product[%s]" value="%s"></label></p>',esc_html($label),esc_attr($k),esc_attr((string)get_post_meta($post->ID,$k,true)));
    }
    /** @param array<string,mixed> $raw */
    public static function saveValues(int $postId, array $raw): void { foreach(array_keys(self::fields()) as $k) if(array_key_exists($k,$raw)) update_post_meta($postId,$k,sanitize_text_field((string)$raw[$k])); }
}
