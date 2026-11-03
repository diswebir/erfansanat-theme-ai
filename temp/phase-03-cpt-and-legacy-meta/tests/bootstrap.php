<?php
declare(strict_types=1);
$root = dirname(__DIR__, 3);
$cache = getenv('ERFANSANAT_TOOL_CACHE') ?: getenv('HOME') . '/.cache/erfansanat-tools';
require_once $cache . '/phpunit-vendor/autoload.php';
$wp = getenv('ERFANSANAT_WP_ROOT') ?: $root . '/.local/wordpress';
if (!is_file($wp . '/wp-load.php')) {
    throw new RuntimeException('Local WordPress is not installed. Run tools/setup-wordpress.sh first.');
}
require_once $wp . '/wp-load.php';
