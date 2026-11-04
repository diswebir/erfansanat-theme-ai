<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\SectionManager;
final class SectionStorage
{
    public const META_KEY='_es_sections';
    /** @return array<int,array{id:string,type:string,enabled:bool,order:int,fields:array<string,mixed>}> */
    public static function get(int $postId): array {
        $raw=get_post_meta($postId,self::META_KEY,true);
        return is_array($raw)? self::sanitize($raw):[];
    }
    /** @param array<int,mixed> $sections */
    public static function save(int $postId, array $sections): bool { return update_post_meta($postId,self::META_KEY,self::sanitize($sections))!==false; }
    public static function delete(int $postId): bool { return delete_post_meta($postId,self::META_KEY); }
    /** @param array<int,mixed> $sections @return array<int,array{id:string,type:string,enabled:bool,order:int,fields:array<string,mixed>}> */
    public static function sanitize(array $sections): array {
        $out=[];
        foreach($sections as $i=>$s){ if(!is_array($s)) continue; $type=sanitize_key((string)($s['type']??'')); if($type==='') continue;
            $out[]=['id'=>sanitize_key((string)($s['id']??$type)),'type'=>$type,'enabled'=>(bool)filter_var($s['enabled']??true,FILTER_VALIDATE_BOOLEAN),'order'=>isset($s['order'])?(int)$s['order']:(int)$i,'fields'=>self::sanitizeFields(is_array($s['fields']??null)?$s['fields']:[])];
        }
        usort($out, static fn(array $a,array $b):int=>$a['order']<=>$b['order']);
        foreach($out as $i=>&$s) $s['order']=$i;
        return $out;
    }
    /** @param array<string,mixed> $fields @return array<string,mixed> */
    private static function sanitizeFields(array $fields): array {
        $out=[]; foreach($fields as $k=>$v){ $key=sanitize_key((string)$k); if(is_array($v)) $out[$key]=self::sanitizeFields($v); elseif(is_bool($v)||is_int($v)) $out[$key]=$v; else $out[$key]=sanitize_text_field((string)$v); } return $out;
    }
}
