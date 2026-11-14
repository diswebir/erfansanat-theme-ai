<?php
/**
 * Local QA harness WordPress config (SQLite via wp-sqlite-db drop-in).
 * NOT for production — this install exists only to test the theme in the sandbox.
 */
define( 'DB_NAME', 'wptest' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

define( 'AUTH_KEY', 'qa-harness-key-1' );
define( 'SECURE_AUTH_KEY', 'qa-harness-key-2' );
define( 'LOGGED_IN_KEY', 'qa-harness-key-3' );
define( 'NONCE_KEY', 'qa-harness-key-4' );
define( 'AUTH_SALT', 'qa-harness-salt-1' );
define( 'SECURE_AUTH_SALT', 'qa-harness-salt-2' );
define( 'LOGGED_IN_SALT', 'qa-harness-salt-3' );
define( 'NONCE_SALT', 'qa-harness-salt-4' );

$table_prefix = 'wp_';

define( 'WP_HOME', 'http://localhost:8090' );
define( 'WP_SITEURL', 'http://localhost:8090' );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_HTTP_BLOCK_EXTERNAL', true );
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'DISALLOW_FILE_EDIT', true );
define( 'FS_METHOD', 'direct' );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
