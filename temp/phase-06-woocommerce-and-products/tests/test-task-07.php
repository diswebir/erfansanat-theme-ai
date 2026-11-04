<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.7 conditional notice and catalog slots ===\n";
$id = $makeProduct();
$assert(trim($renderPart('notices')) === '', 'no empty slots when metadata is blank');
update_post_meta($id, 'before-sale-c', 'اطلاعیه مهم پیش از خرید'); update_post_meta($id, 'product-catalog-file', 'https://example.com/catalog.pdf');
$html = $renderPart('notices');
$assert(str_contains($html, 'اطلاعیه مهم پیش از خرید'), 'before-sale-c rendered when populated');
$assert(str_contains($html, 'https://example.com/catalog.pdf') && str_contains($html, 'دانلود کاتالوگ'), 'product catalog link rendered');
wp_delete_post($id, true); $finishTest();
