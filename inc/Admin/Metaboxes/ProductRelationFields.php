<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Metaboxes;
use ErfanSanat\Theme\Helpers\LegacyMeta;
final class ProductRelationFields
{
    public static function render(\WP_Post $post): void {
        echo '<h3>روابط محصول</h3>';
        printf('<p><label>پروژه‌های مرتبط (آیدی با کاما)<input type="text" class="widefat" name="es_product[project-ids]" value="%s"></label></p>',esc_attr(implode(',',LegacyMeta::getIds($post->ID,'project-ids'))));
        printf('<p><label>کاربردها بر اساس نوع پروژه (آیدی ترم با کاما)<input type="text" class="widefat" name="es_product[usage_countr]" value="%s"></label></p>',esc_attr(implode(',',LegacyMeta::getIds($post->ID,'usage_countr'))));
    }
    /** @param array<string,mixed> $raw */
    public static function saveValues(int $postId, array $raw): void {
        if(array_key_exists('project-ids',$raw)) LegacyMeta::saveIds($postId,'project-ids',LegacyMeta::parseIds($raw['project-ids']));
        if(array_key_exists('usage_countr',$raw)) LegacyMeta::saveIds($postId,'usage_countr',LegacyMeta::parseIds($raw['usage_countr']));
    }
}
