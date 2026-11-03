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
$file = realpath($root . $uri);
if ($file !== false && str_starts_with($file, $root . DIRECTORY_SEPARATOR) && is_file($file)) {
    if (preg_match('#/wp-content/uploads/.*\.php$#i', $file)) {
        http_response_code(404);
        exit('Not found');
    }
    return false;
}
if ($file !== false && is_dir($file) && is_file($file . '/index.php')) {
    $_SERVER['SCRIPT_NAME'] = rtrim($uri, '/') . '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = $file . '/index.php';
    require $file . '/index.php';
    return true;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
require $root . '/index.php';
