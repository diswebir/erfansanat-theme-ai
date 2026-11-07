<?php
/**
 * Minimal PSR-4-style loader for theme classes.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme;

defined('ABSPATH') || exit;

/**
 * Loads classes below the ErfanSanat\Theme namespace from the theme inc directory.
 */
final class Autoloader
{
    private const PREFIX = 'ErfanSanat\\Theme\\';

    /**
     * Register the theme namespace loader.
     *
     * @param string $baseDirectory Absolute path to the theme inc directory.
     */
    public static function register(string $baseDirectory): void
    {
        $baseDirectory = realpath($baseDirectory);
        if ($baseDirectory === false) {
            return;
        }

        spl_autoload_register(
            static function (string $class) use ($baseDirectory): void {
                if (strncmp($class, self::PREFIX, strlen(self::PREFIX)) !== 0) {
                    return;
                }

                $relativeClass = substr($class, strlen(self::PREFIX));
                if ($relativeClass === '') {
                    return;
                }

                $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
                $candidate = $baseDirectory . DIRECTORY_SEPARATOR . $relativePath;
                $resolved = realpath($candidate);

                if (
                    $resolved === false
                    || strpos($resolved, $baseDirectory . DIRECTORY_SEPARATOR) !== 0
                    || !is_file($resolved)
                ) {
                    return;
                }

                require_once $resolved;
            }
        );
    }
}
