<?php
/**
 * PSR-4-ish autoloader for the ErfanSanat\Theme namespace.
 *
 * Maps ErfanSanat\Theme\Foo\Bar  ->  inc/Foo/Bar.php
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

if (!defined('ABSPATH')) {
    exit;
}

final class Autoloader
{
    private const PREFIX = 'ErfanSanat\\Theme\\';

    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }

    public static function load(string $class): void
    {
        if (strpos($class, self::PREFIX) !== 0) {
            return;
        }
        $relative = substr($class, strlen(self::PREFIX));
        $path = ERFANSANAT_THEME_DIR . 'inc/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($path)) {
            require_once $path;
        }
    }
}

Autoloader::register();
