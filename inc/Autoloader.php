<?php
/**
 * اوتولودر PSR-4 قالب — نگاشت Namespace «ErfanSanat\Theme\» به پوشه inc/
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme;

final class Autoloader {

    private const PREFIX = 'ErfanSanat\\Theme\\';

    /** ثبت یکتا — جلوگیری از spl_autoload_register تکراری */
    private static bool $registered = false;

    public static function register(): void {
        if (self::$registered) {
            return;
        }
        spl_autoload_register([self::class, 'load']);
        self::$registered = true;
    }

    public static function is_registered(): bool {
        return self::$registered;
    }

    /**
     * resolve کلاس: نام کلاس → مسیر فایل.
     * دو الگوی نام‌گذاری پشتیبانی می‌شود:
     *  ۱) هم‌نام کلاس (PascalCase):        Frontend\AssetManager → inc/Frontend/AssetManager.php
     *  ۲) kebab-case:                      Frontend\AssetManager → inc/Frontend/asset-manager.php
     */
    public static function load(string $class_name): void {
        if (!str_starts_with($class_name, self::PREFIX)) {
            return;
        }

        $relative = substr($class_name, strlen(self::PREFIX));
        $relative = str_replace('\\', '/', $relative);
        $base     = (defined('ERFANSANAT_DIR') ? ERFANSANAT_DIR : '') . 'inc/';

        $candidates = [
            $base . $relative . '.php',
            $base . self::to_kebab($relative) . '.php',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                require_once $candidate;
                return;
            }
        }
    }

    /** تبدیل PascalCase به kebab-case برای نام فایل */
    public static function to_kebab(string $relative): string {
        $parts = explode('/', $relative);
        $parts = array_map(
            static fn(string $part): string => strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $part)),
            $parts
        );
        return implode('/', $parts);
    }
}
