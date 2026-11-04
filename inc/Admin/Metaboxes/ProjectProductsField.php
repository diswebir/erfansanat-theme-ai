<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Metaboxes;
use ErfanSanat\Theme\Helpers\LegacyMeta;
final class ProjectProductsField
{
    public const KEY='mortabet_mahsole_2';
    public static function render(\WP_Post $post): void { printf('<h3>محصولات استفاده‌شده در پروژه</h3><p><label>آیدی محصولات (با کاما)<input type="text" class="widefat" name="es_project[mortabet_mahsole_2]" value="%s"></label></p>',esc_attr(implode(',',LegacyMeta::getIds($post->ID,self::KEY)))); }
    /** @param array<string,mixed> $raw */
    public static function saveValues(int $postId, array $raw): void { if(array_key_exists(self::KEY,$raw)) LegacyMeta::saveIds($postId,self::KEY,LegacyMeta::parseIds($raw[self::KEY])); }
}
