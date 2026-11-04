<?php
declare(strict_types=1);
$themeRoot = dirname(__DIR__, 3);
$failures = [];
$assert = static function (bool $condition, string $label) use (&$failures): void { echo $condition ? "  [PASS] $label\n" : "  [FAIL] $label\n"; if (!$condition) $failures[] = $label; };
require_once '/opt/wp-testbed/wp-load.php';
$GLOBALS['wp']->main();
$themeWarnings = [];
set_error_handler(static function (int $severity, string $message, string $file) use (&$themeWarnings, $themeRoot): bool {
    if (!(error_reporting() & $severity)) return false;
    if (str_starts_with($file, $themeRoot) && !str_starts_with($file, $themeRoot . '/temp/')) $themeWarnings[] = $message;
    return false;
});
if (!post_type_exists('product')) register_post_type('product', ['public' => true, 'show_ui' => true, 'supports' => ['title', 'editor', 'excerpt', 'thumbnail']]);
$makeProduct = static function (array $meta = []): int {
    $id = wp_insert_post(['post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول تستی', 'post_content' => 'شرح کامل محصول', 'post_excerpt' => 'چکیده محصول']);
    if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
    update_post_meta($id, '_price', '125000'); update_post_meta($id, '_regular_price', '125000'); update_post_meta($id, '_stock_status', 'instock'); update_post_meta($id, '_sku', 'QA-01'); update_post_meta($id, 'true_sale', 'true');
    foreach ($meta as $key => $value) update_post_meta($id, $key, $value);
    $GLOBALS['post'] = get_post($id); $GLOBALS['product'] = function_exists('wc_get_product') ? wc_get_product($id) : null; setup_postdata($GLOBALS['post']);
    return (int) $id;
};
$renderPart = static function (string $part): string { ob_start(); get_template_part('template-parts/product/' . $part); return (string) ob_get_clean(); };
$finishTest = static function () use (&$failures, &$themeWarnings): never {
    restore_error_handler();
    if ($themeWarnings !== []) { $failures[] = 'theme warnings: ' . implode('; ', $themeWarnings); echo "  [FAIL] no theme warnings: " . implode('; ', $themeWarnings) . "\n"; }
    if ($failures !== []) { fwrite(STDERR, "[FAILED] " . implode('; ', $failures) . "\n"); exit(1); }
    echo "[PASSED]\n"; exit(0);
};
