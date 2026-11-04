<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin;
final class OptionsStore
{
    public const OPTION_NAME='erfansanat_theme_options';
    /** @var array<string,mixed>|null */
    private static ?array $cache=null;
    /** @return array<string,mixed> merged stored values over defaults (known keys only) */
    public static function all(): array {
        if (self::$cache!==null) return self::$cache;
        $stored=get_option(self::OPTION_NAME,[]); if(!is_array($stored)) $stored=[];
        $merged=Defaults::all();
        foreach ($stored as $k=>$v) if(array_key_exists($k,$merged)) $merged[$k]=$v;
        return self::$cache=$merged;
    }
    public static function get(string $key, mixed $default=null): mixed { $all=self::all(); return array_key_exists($key,$all)?$all[$key]:$default; }
    public static function set(string $key, mixed $value): bool {
        if(!array_key_exists($key,Defaults::all())) return false;
        $all=self::all(); $all[$key]=$value; return self::persist($all);
    }
    /** @param array<string,mixed> $values */
    public static function update(array $values): bool {
        $all=self::all(); foreach($values as $k=>$v) if(array_key_exists($k,$all)) $all[$k]=$v; return self::persist($all);
    }
    public static function reset(): bool { delete_option(self::OPTION_NAME); self::flushCache(); return true; }
    public static function flushCache(): void { self::$cache=null; }
    /** @param array<string,mixed> $all */
    private static function persist(array $all): bool {
        // Persist only keys that differ from / belong to defaults registry.
        $ok=update_option(self::OPTION_NAME,$all,false); self::flushCache(); return $ok || get_option(self::OPTION_NAME)===$all;
    }
}
