<?php
/**
 * Plugin Name: ErfanSanat QA Harness
 * Description: Registers the repo root as a WordPress theme directory so the
 *              in-repo theme (erfansanat/) is discoverable without copying.
 *              FOR LOCAL QA ONLY — never deploy this file.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// __DIR__ = <repo>/temp/wp/wp-content/mu-plugins  →  dirname(,4) = <repo>
$es_repo_root = dirname( __DIR__, 4 );
if ( is_dir( $es_repo_root ) ) {
	register_theme_directory( $es_repo_root );
}
