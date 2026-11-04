<?php
declare(strict_types=1);
$root = dirname(__DIR__, 3);
$failures = [];
$assert = static function (bool $condition, string $label) use (&$failures): void { echo $condition ? "  [PASS] $label\n" : "  [FAIL] $label\n"; if (!$condition) $failures[] = $label; };
require_once '/opt/wp-testbed/wp-load.php';
$GLOBALS['wp']->main();
echo "=== [TEST] Task 6.1 WooCommerce wrapper/setup ===\n";
$assert(is_file($root . '/woocommerce.php'), 'theme WooCommerce wrapper exists');
$assert(is_file($root . '/inc/WooCommerce/WooSetup.php'), 'WooSetup service exists');
$assert(class_exists('ErfanSanat\\Theme\\WooCommerce\\WooSetup'), 'WooSetup autoloads');
$assert(function_exists('WC'), 'WooCommerce plugin is active in integration testbed');
$assert(current_theme_supports('woocommerce'), 'theme declares WooCommerce support');
$assert(current_theme_supports('wc-product-gallery-zoom'), 'product gallery zoom support enabled');
$assert(current_theme_supports('wc-product-gallery-lightbox'), 'product gallery lightbox support enabled');
$assert(current_theme_supports('wc-product-gallery-slider'), 'product gallery slider support enabled');
$assert(has_action('after_setup_theme', ['ErfanSanat\\Theme\\WooCommerce\\WooSetup', 'setup']) !== false, 'setup hook registered');
if ($failures) { fwrite(STDERR, "[FAILED] " . implode('; ', $failures) . "\n"); exit(1); }
echo "[PASSED]\n";
