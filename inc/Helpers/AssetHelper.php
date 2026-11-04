<?php
/**
 * Theme Asset URI & File Version Resolution Helper.
 *
 * @package ErfanSanat\Theme\Helpers
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Helpers;

final class AssetHelper
{
    /**
     * Resolve full public URI for an asset inside the theme's assets/ directory.
     */
    public static function uri(string $relativePath): string
    {
        $clean = ltrim($relativePath, '/\\');

        return rtrim(ERFANSANAT_THEME_URI, '/') . '/assets/' . $clean;
    }

    /**
     * Resolve full filesystem path for an asset inside the theme's assets/ directory.
     */
    public static function path(string $relativePath): string
    {
        $clean = ltrim($relativePath, '/\\');

        return rtrim(ERFANSANAT_THEME_DIR, '/\\') . '/assets/' . $clean;
    }

    /**
     * Resolve full public URI for an image/media asset inside assets/images/.
     */
    public static function imageUri(string $imageRelativePath): string
    {
        $clean = ltrim($imageRelativePath, '/\\');

        return self::uri('images/' . $clean);
    }

    /**
     * Return deterministic cache-busting version string using filemtime.
     */
    public static function version(string $relativePath): string
    {
        $filePath = self::path($relativePath);
        if (is_file($filePath)) {
            $mtime = filemtime($filePath);
            if ($mtime !== false) {
                return (string) $mtime;
            }
        }

        return ERFANSANAT_THEME_VERSION;
    }
}
