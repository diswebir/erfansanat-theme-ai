<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Metaboxes;
use ErfanSanat\Theme\Helpers\LegacyMeta;
final class ProductRepeaters
{
    /** @return array<string,array<int,string>> meta key => subfield keys */
    public static function definitions(): array { return ['product-feature'=>['add-new-feature'],'product-feature2_copy'=>['add-new-feature','vaule_of_variable'],'usage_product'=>['add-new-usage'],'faq'=>['name_question','answer_question']]; }
    public static function render(\WP_Post $post): void {
        echo '<h3>فیلدهای تکرارشونده</h3>';
        foreach(self::definitions() as $key=>$subs){ $rows=LegacyMeta::getRows($post->ID,$key); if($rows===[]) $rows=[[]];
            echo '<div data-es-repeater="'.esc_attr($key).'"><strong>'.esc_html($key).'</strong>';
            foreach($rows as $i=>$row){ echo '<div>'; foreach($subs as $sub) printf('<input type="text" name="es_product[%s][%d][%s]" value="%s" placeholder="%s">',esc_attr($key),$i,esc_attr($sub),esc_attr((string)($row[$sub]??'')),esc_attr($sub)); echo '</div>'; }
            echo '</div>';
        }
    }
    /** @param array<string,mixed> $raw */
    public static function saveValues(int $postId, array $raw): void {
        foreach(self::definitions() as $key=>$subs){ if(!isset($raw[$key])||!is_array($raw[$key])) continue;
            $rows=[]; foreach($raw[$key] as $row){ if(!is_array($row)) continue; $clean=[]; foreach($subs as $sub) $clean[$sub]=sanitize_text_field((string)($row[$sub]??'')); if(array_filter($clean)!==[]) $rows[]=$clean; }
            LegacyMeta::saveRows($postId,$key,$rows);
        }
    }
}
