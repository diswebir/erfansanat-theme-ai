<?php
/**
 * Router for the isolated PHP-Wasm WordPress preview.
 * The SQLite database lives outside the document root in .local/data/.
 */
declare(strict_types=1);

$root = realpath(__DIR__ . '/../../.local/wordpress');
if ($root === false) {
    http_response_code(503);
    exit('Local WordPress is not installed. Run tools/setup-wordpress.sh.');
}
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
if (str_contains($uri, "\0") || str_contains($uri, '\\') || preg_match('#(?:^|/)\.#', $uri)) {
    http_response_code(404);
    exit('Not found');
}
$segments = explode('/', trim($uri, '/'));
if (in_array('..', $segments, true)) {
    http_response_code(404);
    exit('Not found');
}
$blocked = '#(?:^/wp-config\.php$|^/wp-content/db\.php$|^/wp-content/(?:database|uploads/private)/|^/wp-content/uploads/.*\.php$|\.(?:sqlite|sqlite3|db|log|sql|bak|ini|lock|json|md|sh|py)$)#i';
if (preg_match($blocked, $uri)) {
    http_response_code(404);
    exit('Not found');
}
/*
 * Themes and plugins may be symlinks to working copies outside the document root
 * (the erfansanat theme is developed that way). Resolve the link, but only serve
 * a regular non-PHP file for theme/plugin assets. Anything else that resolves
 * outside the document root is rejected instead of being handed to PHP.
 */
$file = is_file($root . $uri) ? realpath($root . $uri) : false;
if ($file !== false && is_file($file)) {
    $inside = str_starts_with($file, $root . DIRECTORY_SEPARATOR);
    if ($inside) {
        return false;
    }
    if (preg_match('#^/wp-content/(?:themes|plugins)/#', $uri) && ! preg_match('#\.php$#i', $file)) {
        return false;
    }
    http_response_code(404);
    exit('Not found');
}
/*
 * A request that looks like a static file and does not exist is a genuine 404.
 * Handing it to WordPress would let the canonical-redirect guesser answer 301.
 */
if (
    preg_match('#^/wp-(?:content|includes)/#', $uri)
    && preg_match('#\.(?:css|js|mjs|map|woff2?|ttf|otf|eot|jpe?g|png|gif|webp|avif|svg|ico|mp4|webm|pdf|zip|xml)$#i', $uri)
) {
    http_response_code(404);
    exit('Not found');
}
$file = realpath($root . $uri);
if ($file !== false && is_dir($file) && is_file($file . '/index.php')) {
    $_SERVER['SCRIPT_NAME'] = rtrim($uri, '/') . '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = $file . '/index.php';
    require $file . '/index.php';
    return true;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
require $root . '/index.php';
