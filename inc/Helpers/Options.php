<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Helpers;
use ErfanSanat\Theme\Admin\OptionsStore;
final class Options
{
    public static function get(string $key, mixed $default = null): mixed { return OptionsStore::get($key, $default); }
    public static function flush(): void { OptionsStore::flushCache(); }
}
