#!/bin/sh
# Reproducible, disposable WordPress activation test using PHP.wasm + SQLite.
# Usage: sh temp/setup-wordpress-test.sh
# Override WP_TEST_DIR / PHP_WASM_DIR if needed. Nothing is installed into this repo.
set -eu

ROOT="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
WP_TEST_DIR="${WP_TEST_DIR:-$ROOT/node_modules/.cache/ese-wp-test}"
PHP_WASM_DIR="${PHP_WASM_DIR:-$ROOT}"
PHP_BIN="$PHP_WASM_DIR/node_modules/.bin/php-wasm-cli"
WP_SOURCE="$WP_TEST_DIR/WordPress-master"
SQLITE_SOURCE="$WP_TEST_DIR/sqlite-source"
PLUGIN_SOURCE="$SQLITE_SOURCE/packages/plugin-sqlite-database-integration"
PLUGIN_DEST="$WP_SOURCE/wp-content/plugins/sqlite-database-integration"

command -v node >/dev/null 2>&1 || { echo "Node.js is required to run PHP.wasm." >&2; exit 1; }
command -v npm >/dev/null 2>&1 || { echo "npm is required to install PHP.wasm." >&2; exit 1; }
command -v curl >/dev/null 2>&1 || { echo "curl is required to fetch the test sources." >&2; exit 1; }
mkdir -p "$WP_TEST_DIR"

if [ ! -x "$PHP_BIN" ]; then
  npm install --prefix "$PHP_WASM_DIR" @php-wasm/cli@3.1.62 --no-audit --no-fund
fi

if [ ! -f "$WP_TEST_DIR/wordpress.zip" ]; then
  curl -fL --retry 2 -o "$WP_TEST_DIR/wordpress.zip" \
    https://codeload.github.com/WordPress/WordPress/zip/refs/heads/master
fi
if [ ! -f "$WP_SOURCE/wp-includes/version.php" ]; then
  unzip -q "$WP_TEST_DIR/wordpress.zip" -d "$WP_TEST_DIR"
fi

if [ ! -d "$SQLITE_SOURCE/.git" ]; then
  git clone --depth 1 --branch trunk \
    https://github.com/WordPress/sqlite-database-integration.git "$SQLITE_SOURCE"
fi

if [ ! -f "$PLUGIN_DEST/wp-includes/sqlite/db.php" ]; then
  mkdir -p "$PLUGIN_DEST" "$WP_SOURCE/wp-content/plugins"
  cp -a "$PLUGIN_SOURCE"/. "$PLUGIN_DEST"/
  # The source repository uses a symlink; the distributable plugin contains a copy.
  if [ -L "$PLUGIN_DEST/wp-includes/database" ]; then
    rm "$PLUGIN_DEST/wp-includes/database"
  fi
  mkdir -p "$PLUGIN_DEST/wp-includes/database"
  cp -a "$SQLITE_SOURCE/packages/mysql-on-sqlite/src"/. \
    "$PLUGIN_DEST/wp-includes/database/"
fi
cp "$PLUGIN_SOURCE/db.copy" "$WP_SOURCE/wp-content/db.php"
mkdir -p "$WP_SOURCE/wp-content/themes"
ln -sfn "$ROOT" "$WP_SOURCE/wp-content/themes/erfansanat-theme"

if [ ! -f "$WP_SOURCE/wp-config.php" ]; then
  cat > "$WP_SOURCE/wp-config.php" <<PHP
<?php
define('DB_NAME', 'erfansanat_test');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
define('DB_ENGINE', 'sqlite');
define('DB_PATH', '$WP_TEST_DIR/wordpress.sqlite');
define('AUTH_KEY', 'local-theme-test-key');
define('SECURE_AUTH_KEY', 'local-theme-test-key');
define('LOGGED_IN_KEY', 'local-theme-test-key');
define('NONCE_KEY', 'local-theme-test-key');
define('AUTH_SALT', 'local-theme-test-salt');
define('SECURE_AUTH_SALT', 'local-theme-test-salt');
define('LOGGED_IN_SALT', 'local-theme-test-salt');
define('NONCE_SALT', 'local-theme-test-salt');
\$table_prefix = 'wp_';
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', '$WP_TEST_DIR/wp-debug.log');
if (!defined('ABSPATH')) define('ABSPATH', __DIR__ . '/');
require_once ABSPATH . 'wp-settings.php';
PHP
fi

cat > "$WP_TEST_DIR/install-and-activate.php" <<'PHP'
<?php
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['SERVER_NAME'] = 'example.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define('WP_INSTALLING', true);
require '/tmp/ese-wp-test/WordPress-master/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
if (!get_option('siteurl')) {
    wp_install('ESE Theme Test', 'admin', 'admin@example.test', true, '', 'local-test-password');
}
require_once ABSPATH . 'wp-includes/theme.php';
switch_theme('erfansanat-theme');
if (get_option('stylesheet') !== 'erfansanat-theme') {
    fwrite(STDERR, "Theme activation failed\n"); exit(1);
}
echo "WordPress installed; theme activated.\n";
PHP
# Replace the fixed path in generated test entrypoints with the selected test directory.
sed -i "s|/tmp/ese-wp-test/WordPress-master|$WP_SOURCE|g" "$WP_TEST_DIR/install-and-activate.php"
cat > "$WP_TEST_DIR/assert-theme.php" <<'PHP'
<?php
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['SERVER_NAME'] = 'example.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require '__WP_SOURCE__/wp-load.php';
$theme = wp_get_theme();
if (!$theme->exists() || $theme->get_stylesheet() !== 'erfansanat-theme') {
    fwrite(STDERR, "Theme is not active or does not exist\n"); exit(1);
}
foreach (['title-tag', 'post-thumbnails', 'woocommerce', 'html5'] as $support) {
    if (!current_theme_supports($support)) { fwrite(STDERR, "Missing support: {$support}\n"); exit(1); }
}
echo "WP " . get_bloginfo('version') . " loaded theme " . $theme->get('Name') . "\n";
PHP
sed -i "s|__WP_SOURCE__|$WP_SOURCE|g" "$WP_TEST_DIR/assert-theme.php"
cat > "$WP_TEST_DIR/render-theme.php" <<'PHP'
<?php
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['SERVER_NAME'] = 'example.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require '__WP_SOURCE__/wp-load.php';
$GLOBALS['wp']->main('/');
ob_start();
require get_theme_file_path('index.php');
$html = ob_get_clean();
foreach (['<!doctype html>', '<main id="primary"', '</html>'] as $needle) {
    if (strpos($html, $needle) === false) { fwrite(STDERR, "Missing rendered markup: {$needle}\n"); exit(1); }
}
echo 'Theme fallback rendered (' . strlen($html) . " bytes).\n";
PHP
sed -i "s|__WP_SOURCE__|$WP_SOURCE|g" "$WP_TEST_DIR/render-theme.php"

"$PHP_BIN" "$WP_TEST_DIR/install-and-activate.php"
"$PHP_BIN" "$WP_TEST_DIR/assert-theme.php"
"$PHP_BIN" "$WP_TEST_DIR/render-theme.php"
WP_TEST_ROOT="$WP_SOURCE" "$PHP_BIN" "$ROOT/temp/phase-01-core-and-assets/tests/test-task-01.php"
WP_TEST_ROOT="$WP_SOURCE" "$PHP_BIN" "$ROOT/temp/phase-01-core-and-assets/tests/test-task-02.php"
WP_TEST_ROOT="$WP_SOURCE" WP_TEST_ADMIN=1 "$PHP_BIN" "$ROOT/temp/phase-01-core-and-assets/tests/test-task-02.php"
WP_TEST_ROOT="$WP_SOURCE" "$PHP_BIN" "$ROOT/temp/phase-01-core-and-assets/tests/test-task-03.php"
WP_TEST_ROOT="$WP_SOURCE" WP_TEST_ADMIN=1 "$PHP_BIN" "$ROOT/temp/phase-01-core-and-assets/tests/test-task-03.php"
if [ -s "$WP_TEST_DIR/wp-debug.log" ]; then
  cat "$WP_TEST_DIR/wp-debug.log" >&2
  exit 1
fi
echo "WP_DEBUG log is empty. Test environment: $WP_TEST_DIR"
