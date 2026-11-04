<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Metaboxes;
final class ProductSalesFields
{
    /** @return array<string,string> */
    public static function textFields(): array { return ['zireonvan'=>'زیرعنوان محصول','english_titl'=>'عنوان انگلیسی محصول','before-sale-c'=>'اطلاعیه قبل از خرید','product-catalog-file'=>'آدرس فایل کاتالوگ']; }
    public static function render(\WP_Post $post): void {
        echo '<h3>عناوین، وضعیت فروش و فایل‌ها</h3>';
        foreach(self::textFields() as $k=>$label) printf('<p><label>%s<input type="text" class="widefat" name="es_product[%s]" value="%s"></label></p>',esc_html($label),esc_attr($k),esc_attr((string)get_post_meta($post->ID,$k,true)));
        $sale=(string)get_post_meta($post->ID,'true_sale',true);
        echo '<p><label>وضعیت خرید آنلاین <select name="es_product[true_sale]"><option value="true"'.selected($sale,'true',false).'>قابل خرید آنلاین</option><option value="false"'.selected($sale,'false',false).'>استعلامی</option></select></label></p>';
        printf('<p><label>گالری محصول (آیدی‌ها با کاما)<input type="text" class="widefat" name="es_product[galerry_produ]" value="%s"></label></p>',esc_attr((string)get_post_meta($post->ID,'galerry_produ',true)));
    }
    /** @param array<string,mixed> $raw */
    public static function saveValues(int $postId, array $raw): void {
        foreach(array_keys(self::textFields()) as $k) if(array_key_exists($k,$raw)) update_post_meta($postId,$k,$k==='product-catalog-file'?esc_url_raw((string)$raw[$k]):sanitize_text_field((string)$raw[$k]));
        if(array_key_exists('true_sale',$raw)) update_post_meta($postId,'true_sale',(string)$raw['true_sale']==='false'?'false':'true');
        if(array_key_exists('galerry_produ',$raw)) update_post_meta($postId,'galerry_produ',implode(',', \ErfanSanat\Theme\Helpers\LegacyMeta::parseIds((string)$raw['galerry_produ'])));
    }
}
