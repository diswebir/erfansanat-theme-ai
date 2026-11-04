<?php
/**
 * PSR-4 Autoloader for ErfanSanat\Theme namespace.
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

final class Autoloader
{
    private const NAMESPACE_PREFIX = 'ErfanSanat\\Theme\\';

    private static bool $registered = false;

    /**
     * Register SPL autoloader mapped to the theme's inc/ directory.
     */
    public static function register(string $baseDir): void
    {
        if (self::$registered) {
            return;
        }

        $normalizedBaseDir = rtrim($baseDir, '/\\') . DIRECTORY_SEPARATOR;

        spl_autoload_register(
            static function (string $class) use ($normalizedBaseDir): void {
                if (!str_starts_with($class, self::NAMESPACE_PREFIX)) {
                    return;
                }

                $relativeClass = substr($class, strlen(self::NAMESPACE_PREFIX));
                $filePath      = $normalizedBaseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

                if (is_file($filePath)) {
                    require_once $filePath;
                }
            }
        );

        self::$registered = true;
    }
}
