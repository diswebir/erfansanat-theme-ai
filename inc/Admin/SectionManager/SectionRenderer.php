<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\SectionManager;
final class SectionRenderer
{
    /** @return array<int,array{id:string,type:string,enabled:bool,order:int,fields:array<string,mixed>}> */
    public static function getSections(int $postId): array {
        $sections=SectionStorage::get($postId);
        if($sections===[]) $sections=SectionStorage::sanitize((array)apply_filters('erfansanat_default_sections',[],$postId));
        return $sections;
    }
    public static function render(int $postId): string {
        $out='';
        foreach(self::getSections($postId) as $section){
            if(empty($section['enabled'])) continue;
            $type=$section['type'];
            $file=ERFANSANAT_THEME_DIR.'/template-parts/sections/'.$type.'.php';
            if(is_file($file)){ ob_start(); $GLOBALS['es_section']=$section; include $file; $out.=(string)ob_get_clean(); unset($GLOBALS['es_section']); continue; }
            $out.=(string)apply_filters('erfansanat_render_section_html','',$section,$postId);
        }
        return $out;
    }
}
