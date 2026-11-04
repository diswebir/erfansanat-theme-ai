<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Helpers;
final class LegacyMeta
{
    /** Parse any legacy storage format into a list of rows. @return array<int,array<string,mixed>> */
    public static function parseRows(mixed $raw): array {
        $data=self::decode($raw);
        if($data===null||$data==='' ) return [];
        if(is_scalar($data)) return [['value'=>(string)$data]];
        if(!is_array($data)) return [];
        // JetEngine {"item-0": {...}} format
        if(array_filter(array_keys($data), static fn($k)=>is_string($k)&&str_starts_with($k,'item-'))){
            uksort($data, static fn($a,$b)=> (int)str_replace('item-','',(string)$a) <=> (int)str_replace('item-','',(string)$b));
            return array_values(array_map(static fn($v)=>is_array($v)?$v:['value'=>$v], $data));
        }
        if(array_is_list($data)) return array_values(array_map(static fn($v)=>is_array($v)?$v:['value'=>$v], $data));
        return [$data]; // single associative row
    }
    /** @return array<int,int> */
    public static function parseIds(mixed $raw): array {
        $data=self::decode($raw);
        if(is_string($data)) $data=preg_split('/[,\s]+/',trim($data))?:[];
        if(!is_array($data)) return $data===null||$data===''?[]:[(int)$data];
        $ids=[]; foreach($data as $v){ if(is_array($v)) $v=$v['id']??$v['value']??null; $id=(int)$v; if($id>0) $ids[]=$id; } return array_values(array_unique($ids));
    }
    public static function decode(mixed $raw): mixed {
        if(!is_string($raw)) return $raw;
        $t=trim($raw); if($t==='') return '';
        if(str_starts_with($t,'{')||str_starts_with($t,'[')){ $j=json_decode($t,true); if(json_last_error()===JSON_ERROR_NONE) return $j; }
        $u=maybe_unserialize($t); return $u!==false||$t==='b:0;' ? $u : $raw;
    }
    /** @param array<int,array<string,mixed>> $rows */
    public static function toJetEngineFormat(array $rows): array { $out=[]; foreach(array_values($rows) as $i=>$row) $out['item-'.$i]=$row; return $out; }
    /** @return array<int,array<string,mixed>> */
    public static function getRows(int $postId,string $key): array { return self::parseRows(get_post_meta($postId,$key,true)); }
    /** @param array<int,array<string,mixed>> $rows */
    public static function saveRows(int $postId,string $key,array $rows): bool { return update_post_meta($postId,$key,self::toJetEngineFormat($rows))!==false; }
    /** @return array<int,int> */
    public static function getIds(int $postId,string $key): array { return self::parseIds(get_post_meta($postId,$key,true)); }
    /** @param array<int,int> $ids */
    public static function saveIds(int $postId,string $key,array $ids): bool { return update_post_meta($postId,$key,array_values(array_map('intval',$ids)))!==false; }
    /** Gallery fallback chain: galerry_produ -> _product_image_gallery -> gallery-filed -> image-2..6 @return array<int,int> */
    public static function productGalleryIds(int $postId): array {
        $ids=self::parseIds(get_post_meta($postId,'galerry_produ',true));
        if($ids!==[]) return $ids;
        $ids=self::parseIds(get_post_meta($postId,'_product_image_gallery',true));
        if($ids!==[]) return $ids;
        $ids=self::parseIds(get_post_meta($postId,'gallery-filed',true));
        if($ids!==[]) return $ids;
        foreach(range(2,6) as $i){ $id=(int)get_post_meta($postId,'image-'.$i,true); if($id>0) $ids[]=$id; }
        return $ids;
    }
}
