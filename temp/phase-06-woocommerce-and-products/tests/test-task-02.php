<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.2 product breadcrumb/header ===\n";
$id = $makeProduct(['zireonvan' => 'زیرعنوان محصول QA', 'english_titl' => 'QA Lighting Product']);
ob_start(); get_template_part('template-parts/product/header'); $html = (string) ob_get_clean();
$assert(str_contains($html, 'محصول تستی'), 'product title rendered');
$assert(str_contains($html, 'زیرعنوان محصول QA'), 'legacy zireonvan rendered');
$assert(str_contains($html, 'QA Lighting Product'), 'conditional exact english_titl rendered');
$assert(str_contains($html, 'woocommerce-breadcrumb') || str_contains($html, 'aria-label="breadcrumb"'), 'breadcrumb present');
$assert(is_file($themeRoot . '/woocommerce/single-product.php'), 'single-product orchestrator exists');
wp_delete_post($id, true);
$finishTest();
