<?php
/**
 * RateLimit — simple transient-based rate limiter for public AJAX endpoints.
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Ajax;

if (!defined('ABSPATH')) {
    exit;
}

final class RateLimit
{
    /**
     * Allow $max requests per $window seconds for $key (IP-based by default).
     * Returns true when allowed, false when limited.
     */
    public static function allow(string $bucket, int $max, int $window): bool
    {
        $ip = self::ip();
        $key = 'erfansanat_rl_' . $bucket . '_' . md5($ip);
        $count = (int) get_transient($key);
        if ($count >= $max) {
            return false;
        }
        set_transient($key, $count + 1, $window);
        return true;
    }

    private static function ip(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
        return sanitize_text_field((string) $ip);
    }
}
